import { supabase } from './config.js';
import { showNotification } from './main.js';

let currentUser = null;
let isAdmin = false;

async function checkAuth() {
    try {
        const { data: { user } } = await supabase.auth.getUser();

        if (user) {
            currentUser = user;

            const { data: userData } = await supabase
                .from('users')
                .select('*')
                .eq('id', user.id)
                .maybeSingle();

            if (userData) {
                isAdmin = userData.is_admin;
            }

            showDashboard();
        } else {
            showAuth();
        }
    } catch (error) {
        console.error('Error checking auth:', error);
        showAuth();
    }
}

function showAuth() {
    document.getElementById('authContainer').style.display = 'block';
    document.getElementById('dashboardContainer').style.display = 'none';
}

function showDashboard() {
    document.getElementById('authContainer').style.display = 'none';
    document.getElementById('dashboardContainer').style.display = 'block';

    document.getElementById('userInfo').innerHTML = `
        <p>Welcome, <strong>${currentUser.email}</strong></p>
    `;

    if (isAdmin) {
        document.querySelectorAll('.admin-only').forEach(el => el.style.display = 'block');
    }

    loadOrders();
    loadProfile();
}

document.addEventListener('DOMContentLoaded', () => {
    checkAuth();

    document.querySelectorAll('.auth-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

            tab.classList.add('active');
            const tabName = tab.dataset.tab;
            document.getElementById(`${tabName}Tab`).classList.add('active');
        });
    });

    document.getElementById('loginForm').addEventListener('submit', handleLogin);
    document.getElementById('registerForm').addEventListener('submit', handleRegister);
    document.getElementById('logoutBtn').addEventListener('click', handleLogout);

    document.querySelectorAll('.dashboard-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.dashboard-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.dashboard-panel').forEach(p => p.classList.remove('active'));

            tab.classList.add('active');
            const panelId = tab.dataset.tab + 'Panel';
            document.getElementById(panelId).classList.add('active');

            if (tab.dataset.tab === 'admin') {
                loadProductsForAdmin();
            }
        });
    });

    const addProductBtn = document.getElementById('addProductBtn');
    if (addProductBtn) {
        addProductBtn.addEventListener('click', () => {
            document.getElementById('productModalTitle').textContent = 'Add Product';
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = '';
            loadCategoriesForForm();
            document.getElementById('productModal').classList.add('active');
        });
    }

    const productModalClose = document.getElementById('productModalClose');
    if (productModalClose) {
        productModalClose.addEventListener('click', () => {
            document.getElementById('productModal').classList.remove('active');
        });
    }

    const productModal = document.getElementById('productModal');
    if (productModal) {
        productModal.addEventListener('click', (e) => {
            if (e.target === productModal) {
                productModal.classList.remove('active');
            }
        });
    }

    const productForm = document.getElementById('productForm');
    if (productForm) {
        productForm.addEventListener('submit', handleProductSubmit);
    }

    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', handleProfileUpdate);
    }
});

async function handleLogin(e) {
    e.preventDefault();

    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;

    try {
        const { data, error } = await supabase.auth.signInWithPassword({
            email,
            password
        });

        if (error) throw error;

        showNotification('Login successful!');
        await checkAuth();
    } catch (error) {
        console.error('Login error:', error);
        showNotification(error.message || 'Login failed', 'error');
    }
}

async function handleRegister(e) {
    e.preventDefault();

    const name = document.getElementById('registerName').value;
    const email = document.getElementById('registerEmail').value;
    const password = document.getElementById('registerPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (password !== confirmPassword) {
        showNotification('Passwords do not match', 'error');
        return;
    }

    if (password.length < 6) {
        showNotification('Password must be at least 6 characters', 'error');
        return;
    }

    try {
        const { data, error } = await supabase.auth.signUp({
            email,
            password
        });

        if (error) throw error;

        if (data.user) {
            const { error: profileError } = await supabase
                .from('users')
                .insert([{
                    id: data.user.id,
                    email: email,
                    full_name: name,
                    is_admin: false
                }]);

            if (profileError) throw profileError;

            showNotification('Registration successful! You can now login.');
            document.querySelector('[data-tab="login"]').click();
            document.getElementById('registerForm').reset();
        }
    } catch (error) {
        console.error('Registration error:', error);
        showNotification(error.message || 'Registration failed', 'error');
    }
}

async function handleLogout() {
    try {
        const { error } = await supabase.auth.signOut();
        if (error) throw error;

        showNotification('Logged out successfully');
        currentUser = null;
        isAdmin = false;
        showAuth();
    } catch (error) {
        console.error('Logout error:', error);
        showNotification('Logout failed', 'error');
    }
}

async function loadOrders() {
    const container = document.getElementById('ordersList');

    try {
        const { data: orders, error } = await supabase
            .from('orders')
            .select(`
                *,
                order_items (
                    *,
                    products (name)
                )
            `)
            .eq('user_id', currentUser.id)
            .order('created_at', { ascending: false });

        if (error) throw error;

        if (!orders || orders.length === 0) {
            container.innerHTML = '<div class="no-orders">You have no orders yet.</div>';
            return;
        }

        container.innerHTML = orders.map(order => `
            <div class="order-item">
                <div class="order-header">
                    <span class="order-id">Order #${order.id.substring(0, 8)}</span>
                    <span class="order-status ${order.status}">${order.status.toUpperCase()}</span>
                </div>
                <div class="order-details">
                    <p><strong>Date:</strong> ${new Date(order.created_at).toLocaleDateString()}</p>
                    <p><strong>Total:</strong> $${parseFloat(order.total_amount).toFixed(2)}</p>
                    <p><strong>Items:</strong> ${order.order_items.length}</p>
                    <p><strong>Products:</strong> ${order.order_items.map(item => item.products.name).join(', ')}</p>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error loading orders:', error);
        container.innerHTML = '<p class="error">Failed to load orders.</p>';
    }
}

async function loadProfile() {
    try {
        const { data, error } = await supabase
            .from('users')
            .select('*')
            .eq('id', currentUser.id)
            .maybeSingle();

        if (error) throw error;

        if (data) {
            document.getElementById('profileName').value = data.full_name || '';
            document.getElementById('profileEmail').value = data.email || '';
        }
    } catch (error) {
        console.error('Error loading profile:', error);
    }
}

async function handleProfileUpdate(e) {
    e.preventDefault();

    const name = document.getElementById('profileName').value;

    try {
        const { error } = await supabase
            .from('users')
            .update({ full_name: name })
            .eq('id', currentUser.id);

        if (error) throw error;

        showNotification('Profile updated successfully!');
    } catch (error) {
        console.error('Error updating profile:', error);
        showNotification('Failed to update profile', 'error');
    }
}

async function loadProductsForAdmin() {
    const container = document.getElementById('productsManagement');

    try {
        const { data: products, error } = await supabase
            .from('products')
            .select('*, categories(name)')
            .order('created_at', { ascending: false });

        if (error) throw error;

        if (!products || products.length === 0) {
            container.innerHTML = '<p>No products found.</p>';
            return;
        }

        container.innerHTML = `
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${products.map(product => `
                        <tr>
                            <td><img src="${product.image_url}" alt="${product.name}"></td>
                            <td>${product.name}</td>
                            <td>$${parseFloat(product.price).toFixed(2)}</td>
                            <td>${product.stock}</td>
                            <td>${product.categories ? product.categories.name : 'N/A'}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit" data-id="${product.id}">Edit</button>
                                    <button class="action-btn delete" data-id="${product.id}">Delete</button>
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;

        document.querySelectorAll('.action-btn.edit').forEach(btn => {
            btn.addEventListener('click', () => editProduct(btn.dataset.id));
        });

        document.querySelectorAll('.action-btn.delete').forEach(btn => {
            btn.addEventListener('click', () => deleteProduct(btn.dataset.id));
        });
    } catch (error) {
        console.error('Error loading products:', error);
        container.innerHTML = '<p class="error">Failed to load products.</p>';
    }
}

async function loadCategoriesForForm() {
    try {
        const { data: categories, error } = await supabase
            .from('categories')
            .select('*')
            .order('name');

        if (error) throw error;

        const select = document.getElementById('productCategory');
        select.innerHTML = categories.map(cat => `
            <option value="${cat.id}">${cat.name}</option>
        `).join('');
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

async function editProduct(productId) {
    try {
        const { data: product, error } = await supabase
            .from('products')
            .select('*')
            .eq('id', productId)
            .single();

        if (error) throw error;

        document.getElementById('productModalTitle').textContent = 'Edit Product';
        document.getElementById('productId').value = product.id;
        document.getElementById('productName').value = product.name;
        document.getElementById('productDescription').value = product.description;
        document.getElementById('productPrice').value = product.price;
        document.getElementById('productGender').value = product.gender;
        document.getElementById('productSizes').value = product.sizes.join(',');
        document.getElementById('productColors').value = product.colors.join(',');
        document.getElementById('productImageUrl').value = product.image_url;
        document.getElementById('productStock').value = product.stock;
        document.getElementById('productFeatured').checked = product.featured;

        await loadCategoriesForForm();
        document.getElementById('productCategory').value = product.category_id;

        document.getElementById('productModal').classList.add('active');
    } catch (error) {
        console.error('Error loading product:', error);
        showNotification('Failed to load product', 'error');
    }
}

async function deleteProduct(productId) {
    if (!confirm('Are you sure you want to delete this product?')) {
        return;
    }

    try {
        const { error } = await supabase
            .from('products')
            .delete()
            .eq('id', productId);

        if (error) throw error;

        showNotification('Product deleted successfully!');
        loadProductsForAdmin();
    } catch (error) {
        console.error('Error deleting product:', error);
        showNotification('Failed to delete product', 'error');
    }
}

async function handleProductSubmit(e) {
    e.preventDefault();

    const productId = document.getElementById('productId').value;
    const productData = {
        name: document.getElementById('productName').value,
        description: document.getElementById('productDescription').value,
        price: parseFloat(document.getElementById('productPrice').value),
        category_id: document.getElementById('productCategory').value,
        gender: document.getElementById('productGender').value,
        sizes: document.getElementById('productSizes').value.split(',').map(s => s.trim()),
        colors: document.getElementById('productColors').value.split(',').map(c => c.trim()),
        image_url: document.getElementById('productImageUrl').value,
        stock: parseInt(document.getElementById('productStock').value),
        featured: document.getElementById('productFeatured').checked,
        updated_at: new Date().toISOString()
    };

    try {
        let error;
        if (productId) {
            ({ error } = await supabase
                .from('products')
                .update(productData)
                .eq('id', productId));
        } else {
            ({ error } = await supabase
                .from('products')
                .insert([productData]));
        }

        if (error) throw error;

        showNotification(`Product ${productId ? 'updated' : 'added'} successfully!`);
        document.getElementById('productModal').classList.remove('active');
        loadProductsForAdmin();
    } catch (error) {
        console.error('Error saving product:', error);
        showNotification('Failed to save product', 'error');
    }
}