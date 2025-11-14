// product.js – PHP session version

import { showNotification } from './main.js';

document.addEventListener("DOMContentLoaded", () => {
    const sizeSelect = document.getElementById("sizeSelect");
    const qtyInput = document.getElementById("quantityInput");
    const increaseBtn = document.querySelector(".qty-btn.plus");
    const decreaseBtn = document.querySelector(".qty-btn.minus");
    const addToCartForm = document.getElementById("addToCartForm");

    // Size selection
    if (sizeSelect) {
        sizeSelect.addEventListener("change", () => {
            sizeSelect.classList.add("selected");
        });
    }

    // Quantity controls
    if (increaseBtn && qtyInput) {
        increaseBtn.addEventListener("click", () => {
            const currentVal = parseInt(qtyInput.value) || 1;
            const maxVal = parseInt(qtyInput.getAttribute("max")) || 999;
            if (currentVal < maxVal) {
                qtyInput.value = currentVal + 1;
            }
        });
    }

    if (decreaseBtn && qtyInput) {
        decreaseBtn.addEventListener("click", () => {
            const currentVal = parseInt(qtyInput.value) || 1;
            const minVal = parseInt(qtyInput.getAttribute("min")) || 1;
            if (currentVal > minVal) {
                qtyInput.value = currentVal - 1;
            }
        });
    }

    // Form submission validation
    if (addToCartForm) {
        addToCartForm.addEventListener("submit", (e) => {
            // Validate size selection if size dropdown exists
            if (sizeSelect && !sizeSelect.value) {
                e.preventDefault();
                showNotification("Please select a size before adding to cart.", "error");
                sizeSelect.focus();
                return false;
            }

            // Validate quantity
            const quantity = parseInt(qtyInput.value) || 1;
            if (quantity < 1) {
                e.preventDefault();
                showNotification("Please enter a valid quantity.", "error");
                qtyInput.focus();
                return false;
            }

            // Form will submit normally to cart.php via POST
            return true;
        });
    }
});
