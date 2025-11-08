import { supabase, getSessionId } from './config.js';
import { showNotification, updateCartCount } from './main.js';

let cartItems = [];

async function loadCart() {
    const container = document.getElementById('cartItems');

    try {
        const { data: { user } } = await supabase.auth.getUser();

        let query = supabase
            .from('cart_items')
            .select(`
                *,
                products (
                    id,
                    name,
                    price,
                    image_url,
                    stock
                )
            `);

        if (user) {
            query = query.eq('user_id', user.id);
        } else {
            query = query.eq('session_id', getSessionId());
        }

        const { data, error } = await query;

        if (error) throw error;

        cartItems = data || [];
        displayCart();
    } catch (error) {
        console.error('Error loading cart:', error);
        container.innerHTML = '<p class="error">Failed to load cart. Please try again later.</p>';
    }
}

function displayCart() {
    const container = document.getElementById('cartItems');
    const subtotalElement = document.getElementById('subtotal');
    const totalElement = document.getElementById('total');

    if (cartItems.length === 0) {
        container.innerHTML = `
            <div class="empty-cart">
                <h3>YOUR CART IS EMPTY</h3>
                <p>Add some products to your cart to continue shopping.</p>
                <a href="shop.html" class="btn btn-primary">SHOP NOW</a>
            </div>
        `;
        subtotalElement.textContent = '$0.00';
        totalElement.textContent = '$0.00';
        return;
    }

    container.innerHTML = cartItems.map(item => `
        <div class="cart-item" data-item-id="${item.id}">
            <img src="${item.products.image_url}" alt="${item.products.name}" class="cart-item-image">
            <div class="cart-item-details">
                <h3 class="cart-item-name">${item.products.name}</h3>
                <p class="cart-item-meta">Size: ${item.size} | Color: ${item.color}</p>
                <p class="cart-item-price">$${parseFloat(item.products.price).toFixed(2)}</p>
            </div>
            <div class="cart-item-actions">
                <div class="cart-item-quantity">
                    <button class="qty-btn" data-action="decrease" data-id="${item.id}">-</button>
                    <span class="qty-display">${item.quantity}</span>
                    <button class="qty-btn" data-action="increase" data-id="${item.id}" ${item.quantity >= item.products.stock ? 'disabled' : ''}>+</button>
                </div>
                <button class="remove-btn" data-id="${item.id}">Remove</button>
            </div>
        </div>
    `).join('');

    const subtotal = cartItems.reduce((sum, item) => {
        return sum + (parseFloat(item.products.price) * item.quantity);
    }, 0);

    subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
    totalElement.textContent = `$${subtotal.toFixed(2)}`;

    setupCartEventListeners();
}

function setupCartEventListeners() {
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const itemId = e.target.dataset.id;
            const action = e.target.dataset.action;
            await updateQuantity(itemId, action);
        });
    });

    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const itemId = e.target.dataset.id;
            await removeItem(itemId);
        });
    });

    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', () => {
            if (cartItems.length === 0) {
                showNotification('Your cart is empty', 'error');
                return;
            }
            document.getElementById('checkoutModal').classList.add('active');
        });
    }
}

async function updateQuantity(itemId, action) {
    try {
        const item = cartItems.find(i => i.id === itemId);
        if (!item) return;

        let newQuantity = item.quantity;
        if (action === 'increase' && item.quantity < item.products.stock) {
            newQuantity++;
        } else if (action === 'decrease' && item.quantity > 1) {
            newQuantity--;
        } else {
            return;
        }

        const { error } = await supabase
            .from('cart_items')
            .update({
                quantity: newQuantity,
                updated_at: new Date().toISOString()
            })
            .eq('id', itemId);

        if (error) throw error;

        await loadCart();
        await updateCartCount();
    } catch (error) {
        console.error('Error updating quantity:', error);
        showNotification('Failed to update quantity', 'error');
    }
}

async function removeItem(itemId) {
    try {
        const { error } = await supabase
            .from('cart_items')
            .delete()
            .eq('id', itemId);

        if (error) throw error;

        showNotification('Item removed from cart');
        await loadCart();
        await updateCartCount();
    } catch (error) {
        console.error('Error removing item:', error);
        showNotification('Failed to remove item', 'error');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadCart();

    const modalClose = document.getElementById('modalClose');
    const checkoutModal = document.getElementById('checkoutModal');

    if (modalClose) {
        modalClose.addEventListener('click', () => {
            checkoutModal.classList.remove('active');
        });
    }

    if (checkoutModal) {
        checkoutModal.addEventListener('click', (e) => {
            if (e.target === checkoutModal) {
                checkoutModal.classList.remove('active');
            }
        });
    }

    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            await processCheckout();
        });
    }
});

async function processCheckout() {
    const name = document.getElementById('shippingName').value;
    const email = document.getElementById('shippingEmail').value;
    const address = document.getElementById('shippingAddress').value;
    const phone = document.getElementById('shippingPhone').value;

    if (!name || !email || !address || !phone) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }

    try {
        const { data: { user } } = await supabase.auth.getUser();

        if (!user) {
            showNotification('Please log in to complete your order', 'error');
            setTimeout(() => {
                window.location.href = 'account.html';
            }, 2000);
            return;
        }

        const total = cartItems.reduce((sum, item) => {
            return sum + (parseFloat(item.products.price) * item.quantity);
        }, 0);

        const shippingInfo = `${name}\n${email}\n${phone}\n${address}`;

        const { data: order, error: orderError } = await supabase
            .from('orders')
            .insert([{
                user_id: user.id,
                total_amount: total,
                status: 'pending',
                shipping_address: shippingInfo
            }])
            .select()
            .single();

        if (orderError) throw orderError;

        const orderItems = cartItems.map(item => ({
            order_id: order.id,
            product_id: item.products.id,
            quantity: item.quantity,
            size: item.size,
            color: item.color,
            price: item.products.price
        }));

        const { error: itemsError } = await supabase
            .from('order_items')
            .insert(orderItems);

        if (itemsError) throw itemsError;

        const { error: clearError } = await supabase
            .from('cart_items')
            .delete()
            .eq('user_id', user.id);

        if (clearError) throw clearError;

        showNotification('Order placed successfully!');
        document.getElementById('checkoutModal').classList.remove('active');
        document.getElementById('checkoutForm').reset();

        setTimeout(() => {
            window.location.href = 'account.html';
        }, 2000);
    } catch (error) {
        console.error('Error processing checkout:', error);
        showNotification('Failed to process order. Please try again.', 'error');
    }
}