// product.js – PHP session version

document.addEventListener("DOMContentLoaded", () => {
    const sizeSelect = document.getElementById("sizeSelect");
    const qtyInput = document.getElementById("quantityInput");
    // try IDs first, fall back to class selectors to match markup
    const increaseBtn = document.getElementById("increaseQty") || document.querySelector(".qty-btn.plus");
    const decreaseBtn = document.getElementById("decreaseQty") || document.querySelector(".qty-btn.minus");
    const addToCartBtn = document.getElementById("addToCartBtn");

    let quantity = 1;

    // Size selection
    if (sizeSelect) {
        sizeSelect.addEventListener("change", () => {
            sizeSelect.classList.add("selected");
        });
    }

    // Quantity controls
    if (increaseBtn && decreaseBtn) {
        increaseBtn.addEventListener("click", () => {
            quantity++;
            qtyInput.value = quantity;
        });

        decreaseBtn.addEventListener("click", () => {
            if (quantity > 1) {
                quantity--;
                qtyInput.value = quantity;
            }
        });
    }

    // Add to Cart
    if (addToCartBtn) {
        addToCartBtn.addEventListener("click", () => {
            const size = sizeSelect ? sizeSelect.value : "";
            const productId = addToCartBtn.dataset.id;

            // Require size only if a size dropdown exists on the page
            if (sizeSelect && !size) {
                alert("Please select a size before adding to cart.");
                return;
            }

            const formData = new FormData();
            formData.append("product_id", productId);
            formData.append("size", size);
            formData.append("quantity", quantity);

            fetch("cart.php", {
                method: "POST",
                body: formData
            })
                .then((res) => {
                    if (res.ok) {
                        window.location.href = "cart.php";
                    } else {
                        alert("Failed to add item to cart. Please try again.");
                    }
                })
                .catch((err) => {
                    console.error("Error:", err);
                    alert("Error adding to cart.");
                });
        });
    }
});
   
