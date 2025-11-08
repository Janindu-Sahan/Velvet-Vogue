import { supabase } from './config.js';
import { showNotification } from './main.js';

document.addEventListener('DOMContentLoaded', () => {
    const contactForm = document.getElementById('contactForm');

    contactForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const name = document.getElementById('contactName').value;
        const email = document.getElementById('contactEmail').value;
        const subject = document.getElementById('contactSubject').value;
        const message = document.getElementById('contactMessage').value;

        try {
            const { error } = await supabase
                .from('contact_inquiries')
                .insert([{
                    name,
                    email,
                    subject,
                    message
                }]);

            if (error) throw error;

            showNotification('Message sent successfully! We will get back to you soon.');
            contactForm.reset();
        } catch (error) {
            console.error('Error sending message:', error);
            showNotification('Failed to send message. Please try again.', 'error');
        }
    });
});