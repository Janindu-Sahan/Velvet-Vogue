import { supabase, getSessionId } from './config.js';
import { showNotification, updateCartCount } from './main.js';

let currentProduct = null;
let selectedSize = null;
let selectedColor = null;
let quantity = 1;

async function loadProduct() {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');

    if (!productId) {
        window.location.href = 'shop.html';
        return;
    }

    const container = document.getElementById('productDetail');

    try {
        const { data: product, error } = await supabase
            .from('products')
            .select('*, categories(name)')
            .eq('id', productId)
            .maybeSingle();

        if (error) throw error;

        if (!product) {
            container.innerHTML = '<p class="error">Product not found.</p>';
            return;
        }

        currentProduct = product;
        displayProduct(product);
    } catch (error) {
        console.error('Error loading product:', error);
        container.innerHTML = '<p class="error">Failed to load product. Please try again later.</p>';
    }
}

function displayProduct(product) {
    const container = document.getElementById('productDetail');
    const breadcrumb = document.getElementById('breadcrumbProduct');

    breadcrumb.textContent = product.name;
    document.title = `${product.name} - Velvet Vogue`;

    const stockStatus = product.stock > 10 ? 'in-stock' : product.stock > 0 ? 'low-stock' : 'out-of-stock';
    const stockText = product.stock > 10 ? 'In Stock' : product.stock > 0 ? `Low Stock (${product.stock} left)` : 'Out of Stock';

    container.innerHTML = `
        <div class="product-image-container">
            <img src="${product.image_url}" alt="${product.name}" class="product-main-image">
        </div>
        <div class="product-info-container">
            <h1 class="product-title">${product.name}</h1>
            <div class="product-price-display">$${parseFloat(product.price).toFixed(2)}</div>

            <div class="stock-status ${stockStatus}">
                ${stockText}
            </div>

            <p class="product-description">${product.description}</p>

            <div class="product-meta">
                <p><strong>Category:</strong> ${product.categories ? product.categories.name : 'N/A'}</p>
                <p><strong>Gender:</strong> ${product.gender}</p>
            </div>

            <div class="product-options">
                <div class="option-group">
                    <label>SELECT SIZE *</label>
                    <div class="option-buttons" id="sizeButtons">
                        ${product.sizes.map(size => `
                            <button class="option-btn" data-size="${size}">${size}</button>
                        `).join('')}
                    </div>
                </div>

                <div class="option-group">
                    <label>SELECT COLOR *</label>
                    <div class="option-buttons" id="colorButtons">
                        ${product.colors.map(color => `
                            <button class="option-btn" data-color="${color}">${color}</button>
                        `).join('')}
                    </div>
                </div>
            </div>

            <div class="quantity-group">
                <label>QUANTITY</label>
                <div class="quantity-selector">
                    <button class="quantity-btn" id="decreaseQty">-</button>
                    <input type="number" class="quantity-input" id="quantityInput" value="1" min="1" max="${product.stock}" readonly>
                    <button class="quantity-btn" id="increaseQty">+</button>
                </div>
            </div>

            <div class="add-to-cart-section">
                <button class="btn btn-primary add-to-cart-btn" id="addToCartBtn" ${product.stock === 0 ? 'disabled' : ''}>
                    ${product.stock === 0 ? 'OUT OF STOCK' : 'ADD TO CART'}
                </button>
            </div>
        </div>
    `;

    setupEventListeners();
}

function setupEventListeners() {
    document.querySelectorAll('[data-size]').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('[data-size]').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedSize = btn.dataset.size;
        });
    });

    document.querySelectorAll('[data-color]').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('[data-color]').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedColor = btn.dataset.color;
        });
    });

    const quantityInput = document.getElementById('quantityInput');
    document.getElementById('decreaseQty').addEventListener('click', () => {
        if (quantity > 1) {
            quantity--;
            quantityInput.value = quantity;
        }
    });

    document.getElementById('increaseQty').addEventListener('click', () => {
        if (quantity < currentProduct.stock) {
            quantity++;
            quantityInput.value = quantity;
        }
    });

    document.getElementById('addToCartBtn').addEventListener('click', addToCart);
}

async function addToCart() {
    if (!selectedSize || !selectedColor) {
        showNotification('Please select size and color', 'error');
        return;
    }

    if (currentProduct.stock === 0) {
        showNotification('Product is out of stock', 'error');
        return;
    }

    try {
        const { data: { user } } = await supabase.auth.getUser();

        const cartItem = {
            product_id: currentProduct.id,
            quantity: quantity,
            size: selectedSize,
            color: selectedColor
        };

        if (user) {
            cartItem.user_id = user.id;

            const { data: existingItem } = await supabase
                .from('cart_items')
                .select('*')
                .eq('user_id', user.id)
                .eq('product_id', currentProduct.id)
                .eq('size', selectedSize)
                .eq('color', selectedColor)
                .maybeSingle();

            if (existingItem) {
                const { error } = await supabase
                    .from('cart_items')
                    .update({
                        quantity: existingItem.quantity + quantity,
                        updated_at: new Date().toISOString()
                    })
                    .eq('id', existingItem.id);

                if (error) throw error;
            } else {
                const { error } = await supabase
                    .from('cart_items')
                    .insert([cartItem]);

                if (error) throw error;
            }
        } else {
            cartItem.session_id = getSessionId();

            const { data: existingItem } = await supabase
                .from('cart_items')
                .select('*')
                .eq('session_id', getSessionId())
                .eq('product_id', currentProduct.id)
                .eq('size', selectedSize)
                .eq('color', selectedColor)
                .maybeSingle();

            if (existingItem) {
                const { error } = await supabase
                    .from('cart_items')
                    .update({
                        quantity: existingItem.quantity + quantity,
                        updated_at: new Date().toISOString()
                    })
                    .eq('id', existingItem.id);

                if (error) throw error;
            } else {
                const { error } = await supabase
                    .from('cart_items')
                    .insert([cartItem]);

                if (error) throw error;
            }
        }

        showNotification('Product added to cart!');
        await updateCartCount();

        quantity = 1;
        document.getElementById('quantityInput').value = 1;
    } catch (error) {
        console.error('Error adding to cart:', error);
        showNotification('Failed to add to cart. Please try again.', 'error');
    }
}

document.addEventListener('DOMContentLoaded', loadProduct);