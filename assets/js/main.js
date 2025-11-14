import { supabase, getSessionId } from './config.js';

const hamburger = document.getElementById('hamburger');
const navMenu = document.getElementById('navMenu');
const cartCountElement = document.getElementById('cartCount');

if (hamburger && navMenu) {
    hamburger.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });
}

export async function updateCartCount() {
    try {
        const { data: { user } } = await supabase.auth.getUser();

        let query = supabase
            .from('cart_items')
            .select('quantity');

        if (user) {
            query = query.eq('user_id', user.id);
        } else {
            query = query.eq('session_id', getSessionId());
        }

        const { data, error } = await query;

        if (error) throw error;

        const totalCount = data ? data.reduce((sum, item) => sum + item.quantity, 0) : 0;

        if (cartCountElement) {
            cartCountElement.textContent = totalCount;
        }
    } catch (error) {
        console.error('Error updating cart count:', error);
        if (cartCountElement) {
            cartCountElement.textContent = '0';
        }
    }
}

export function showNotification(message, type = 'success') {
    // Define colors for different notification types
    const colors = {
        success: {
            bg: '#d4edda',
            text: '#155724',
            border: '#c3e6cb'
        },
        error: {
            bg: '#f8d7da',
            text: '#721c24',
            border: '#f5c6cb'
        },
        warning: {
            bg: '#fff3cd',
            text: '#856404',
            border: '#ffeeba'
        },
        info: {
            bg: '#d1ecf1',
            text: '#0c5460',
            border: '#bee5eb'
        }
    };

    const colorScheme = colors[type] || colors.success;

    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        padding: 16px 24px;
        background-color: ${colorScheme.bg};
        color: ${colorScheme.text};
        border: 1px solid ${colorScheme.border};
        border-radius: 4px;
        z-index: 10000;
        animation: slideIn 0.3s ease;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        max-width: 400px;
        word-wrap: break-word;
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

updateCartCount();

supabase.auth.onAuthStateChange((event, session) => {
    (async () => {
        if (event === 'SIGNED_IN') {
            await migrateGuestCart();
            updateCartCount();
        } else if (event === 'SIGNED_OUT') {
            updateCartCount();
        }
    })();
});

async function migrateGuestCart() {
    try {
        const sessionId = getSessionId();
        const { data: { user } } = await supabase.auth.getUser();

        if (!user) return;

        const { data: guestItems } = await supabase
            .from('cart_items')
            .select('*')
            .eq('session_id', sessionId);

        if (guestItems && guestItems.length > 0) {
            for (const item of guestItems) {
                const { data: existingItem } = await supabase
                    .from('cart_items')
                    .select('*')
                    .eq('user_id', user.id)
                    .eq('product_id', item.product_id)
                    .eq('size', item.size)
                    .eq('color', item.color)
                    .maybeSingle();

                if (existingItem) {
                    await supabase
                        .from('cart_items')
                        .update({ quantity: existingItem.quantity + item.quantity })
                        .eq('id', existingItem.id);
                } else {
                    await supabase
                        .from('cart_items')
                        .update({ user_id: user.id, session_id: null })
                        .eq('id', item.id);
                }
            }

            await supabase
                .from('cart_items')
                .delete()
                .eq('session_id', sessionId)
                .is('user_id', null);
        }
    } catch (error) {
        console.error('Error migrating cart:', error);
    }
}