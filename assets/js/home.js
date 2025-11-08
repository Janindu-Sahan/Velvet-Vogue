import { supabase } from './config.js';

async function loadFeaturedProducts() {
    const container = document.getElementById('featuredProducts');

    try {
        const { data: products, error } = await supabase
            .from('products')
            .select('*')
            .eq('featured', true)
            .order('created_at', { ascending: false });

        if (error) throw error;

        if (!products || products.length === 0) {
            container.innerHTML = '<p class="error">No featured products available.</p>';
            return;
        }

        container.innerHTML = products.map(product => `
            <a href="product.html?id=${product.id}" class="product-card">
                <img src="${product.image_url}" alt="${product.name}" class="product-image">
                <div class="product-info">
                    <h3 class="product-name">${product.name}</h3>
                    <p class="product-price">$${parseFloat(product.price).toFixed(2)}</p>
                </div>
            </a>
        `).join('');
    } catch (error) {
        console.error('Error loading featured products:', error);
        container.innerHTML = '<p class="error">Failed to load products. Please try again later.</p>';
    }
}

document.addEventListener('DOMContentLoaded', loadFeaturedProducts);