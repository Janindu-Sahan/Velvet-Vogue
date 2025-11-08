import { supabase } from './config.js';

let allProducts = [];
let allCategories = [];

async function loadCategories() {
    try {
        const { data, error } = await supabase
            .from('categories')
            .select('*')
            .order('name');

        if (error) throw error;

        allCategories = data || [];
        populateCategoryFilter();
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

function populateCategoryFilter() {
    const categoryFilter = document.getElementById('categoryFilter');
    categoryFilter.innerHTML = '<option value="">All Categories</option>' +
        allCategories.map(cat => `<option value="${cat.slug}">${cat.name}</option>`).join('');

    const urlParams = new URLSearchParams(window.location.search);
    const category = urlParams.get('category');
    if (category) {
        categoryFilter.value = category;
    }
}

async function loadProducts() {
    const container = document.getElementById('productsGrid');
    const countElement = document.getElementById('productCount');

    try {
        const { data: products, error } = await supabase
            .from('products')
            .select('*, categories(slug)')
            .order('name');

        if (error) throw error;

        allProducts = products || [];
        filterAndDisplayProducts();
    } catch (error) {
        console.error('Error loading products:', error);
        container.innerHTML = '<p class="error">Failed to load products. Please try again later.</p>';
    }
}

function filterAndDisplayProducts() {
    const container = document.getElementById('productsGrid');
    const countElement = document.getElementById('productCount');

    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const selectedCategory = document.getElementById('categoryFilter').value;
    const selectedGender = document.getElementById('genderFilter').value;
    const selectedSize = document.getElementById('sizeFilter').value;
    const maxPrice = parseFloat(document.getElementById('priceFilter').value) || Infinity;
    const sortBy = document.getElementById('sortFilter').value;

    let filtered = allProducts.filter(product => {
        const matchesSearch = product.name.toLowerCase().includes(searchTerm) ||
                            product.description.toLowerCase().includes(searchTerm);
        const matchesCategory = !selectedCategory ||
                               (product.categories && product.categories.slug === selectedCategory);
        const matchesGender = !selectedGender || product.gender === selectedGender;
        const matchesSize = !selectedSize || (product.sizes && product.sizes.includes(selectedSize));
        const matchesPrice = parseFloat(product.price) <= maxPrice;

        return matchesSearch && matchesCategory && matchesGender && matchesSize && matchesPrice;
    });

    filtered.sort((a, b) => {
        switch (sortBy) {
            case 'name-asc':
                return a.name.localeCompare(b.name);
            case 'name-desc':
                return b.name.localeCompare(a.name);
            case 'price-asc':
                return parseFloat(a.price) - parseFloat(b.price);
            case 'price-desc':
                return parseFloat(b.price) - parseFloat(a.price);
            default:
                return 0;
        }
    });

    countElement.textContent = `Showing ${filtered.length} product${filtered.length !== 1 ? 's' : ''}`;

    if (filtered.length === 0) {
        container.innerHTML = '<p class="error">No products found matching your criteria.</p>';
        return;
    }

    container.innerHTML = filtered.map(product => `
        <a href="product.html?id=${product.id}" class="product-card">
            <img src="${product.image_url}" alt="${product.name}" class="product-image">
            <div class="product-info">
                <h3 class="product-name">${product.name}</h3>
                <p class="product-price">$${parseFloat(product.price).toFixed(2)}</p>
            </div>
        </a>
    `).join('');
}

document.addEventListener('DOMContentLoaded', async () => {
    await loadCategories();
    await loadProducts();

    document.getElementById('searchInput').addEventListener('input', filterAndDisplayProducts);
    document.getElementById('categoryFilter').addEventListener('change', filterAndDisplayProducts);
    document.getElementById('genderFilter').addEventListener('change', filterAndDisplayProducts);
    document.getElementById('sizeFilter').addEventListener('change', filterAndDisplayProducts);
    document.getElementById('priceFilter').addEventListener('input', filterAndDisplayProducts);
    document.getElementById('sortFilter').addEventListener('change', filterAndDisplayProducts);

    document.getElementById('resetFilters').addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        document.getElementById('categoryFilter').value = '';
        document.getElementById('genderFilter').value = '';
        document.getElementById('sizeFilter').value = '';
        document.getElementById('priceFilter').value = '';
        document.getElementById('sortFilter').value = 'name-asc';
        filterAndDisplayProducts();
    });
});