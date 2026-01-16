// konfirmasi hapus produk
function confirmDelete() {
    return confirm("Yakin ingin menghapus produk ini?");
}

// animasi kecil (opsional)
document.addEventListener("DOMContentLoaded", function () {
    const cards = document.querySelectorAll(".card");
    cards.forEach(card => {
        card.style.transition = "transform 0.2s";
        card.addEventListener("mouseenter", () => {
            card.style.transform = "scale(1.03)";
        });
        card.addEventListener("mouseleave", () => {
            card.style.transform = "scale(1)";
        });
    });
});


// Fungsi untuk menambahkan produk ke keranjang
function addToCart(productId, quantity = 1) {
    // Tampilkan loading
    const loading = showLoading();
    
    fetch('../cart/add.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'product_id': productId,
            'quantity': quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        loading.remove();
        
        if (data.success) {
            // Update cart counter
            updateCartCounter(data.cartCount);
            
            // Tampilkan popup konfirmasi
            showCartConfirmation(data.productName, data.quantity, data.price);
            
        } else {
            // Jika perlu login, redirect
            if (data.redirect) {
                window.location.href = data.redirect;
                return;
            }
            
            // Tampilkan popup error
            showErrorPopup(data.message);
        }
    })
    .catch(error => {
        loading.remove();
        showErrorPopup('Network error. Please try again.');
        console.error('Error:', error);
    });
}

// Fungsi untuk menampilkan popup konfirmasi
function showCartConfirmation(productName, quantity, price) {
    // Hapus popup sebelumnya jika ada
    const existingPopup = document.getElementById('cart-confirmation-popup');
    if (existingPopup) {
        existingPopup.remove();
    }
    
    // Format harga
    const formattedPrice = new Intl.NumberFormat('id-ID').format(price);
    const totalPrice = price * quantity;
    const formattedTotal = new Intl.NumberFormat('id-ID').format(totalPrice);
    
    // Buat overlay backdrop
    const backdrop = document.createElement('div');
    backdrop.className = 'popup-backdrop';
    
    // Buat popup container
    const popup = document.createElement('div');
    popup.id = 'cart-confirmation-popup';
    popup.className = 'cart-confirmation-popup';
    popup.innerHTML = `
        <div class="popup-container">
            <!-- Header dengan ikon -->
            <div class="popup-header">
                <div class="popup-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h5 class="popup-title">Added to Cart</h5>
            </div>
            
            <!-- Body dengan detail produk -->
            <div class="popup-body">
                <p class="popup-message">
                    <strong>${productName}</strong> has been added to your shopping cart.
                </p>
                
                <div class="product-details">
                    <div class="detail-row">
                        <span>Quantity:</span>
                        <span>${quantity} × Rp ${formattedPrice}</span>
                    </div>
                    <div class="detail-row total">
                        <span>Total:</span>
                        <span class="total-price">Rp ${formattedTotal}</span>
                    </div>
                </div>
            </div>
            
            <!-- Footer dengan tombol -->
            <div class="popup-footer">
                <button type="button" class="btn btn-continue" onclick="closeCartPopup()">
                    Continue Shopping
                </button>
                <a href="${BASE_URL}/cart" class="btn btn-view-cart">
                    View Cart
                </a>
            </div>
        </div>
    `;
    
    // Tambahkan ke body
    document.body.appendChild(backdrop);
    document.body.appendChild(popup);
    
    // Tampilkan dengan animasi
    setTimeout(() => {
        backdrop.classList.add('show');
        popup.classList.add('show');
    }, 10);
    
    // Auto close setelah 5 detik
    setTimeout(() => {
        closeCartPopup();
    }, 5000);
}

// Fungsi untuk menampilkan popup error
function showErrorPopup(message) {
    const existingPopup = document.getElementById('cart-confirmation-popup');
    if (existingPopup) {
        existingPopup.remove();
    }
    
    const backdrop = document.createElement('div');
    backdrop.className = 'popup-backdrop';
    
    const popup = document.createElement('div');
    popup.id = 'cart-confirmation-popup';
    popup.className = 'cart-confirmation-popup error';
    popup.innerHTML = `
        <div class="popup-container">
            <div class="popup-header">
                <div class="popup-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <h5 class="popup-title">Cannot Add to Cart</h5>
            </div>
            
            <div class="popup-body">
                <p class="popup-message">${message}</p>
            </div>
            
            <div class="popup-footer">
                <button type="button" class="btn btn-ok" onclick="closeCartPopup()">
                    OK
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(backdrop);
    document.body.appendChild(popup);
    
    setTimeout(() => {
        backdrop.classList.add('show');
        popup.classList.add('show');
    }, 10);
}

// Fungsi untuk menutup popup
function closeCartPopup() {
    const backdrop = document.querySelector('.popup-backdrop');
    const popup = document.getElementById('cart-confirmation-popup');
    
    if (popup) popup.classList.remove('show');
    if (backdrop) backdrop.classList.remove('show');
    
    // Hapus dari DOM setelah animasi
    setTimeout(() => {
        if (popup) popup.remove();
        if (backdrop) backdrop.remove();
    }, 300);
}

// Fungsi untuk menampilkan loading
function showLoading() {
    const loading = document.createElement('div');
    loading.className = 'cart-loading';
    loading.innerHTML = '<div class="spinner"></div>';
    
    document.body.appendChild(loading);
    return loading;
}

// Fungsi untuk update cart counter
function updateCartCounter(count) {
    const cartCounter = document.getElementById('cart-counter');
    if (cartCounter) {
        cartCounter.textContent = count;
        
        // Animasi kecil
        cartCounter.classList.add('pulse');
        setTimeout(() => {
            cartCounter.classList.remove('pulse');
        }, 300);
    }
}

// Tambahkan CSS untuk popup
const popupStyle = document.createElement('style');
popupStyle.textContent = `
    /* Backdrop overlay */
    .popup-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9998;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .popup-backdrop.show {
        opacity: 1;
    }
    
    /* Popup container */
    .cart-confirmation-popup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.9);
        width: 90%;
        max-width: 400px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        z-index: 9999;
        opacity: 0;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .cart-confirmation-popup.show {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
    
    .cart-confirmation-popup.error {
        max-width: 350px;
    }
    
    /* Popup container inner */
    .popup-container {
        padding: 0;
    }
    
    /* Header */
    .popup-header {
        padding: 25px 25px 15px;
        text-align: center;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }
    
    .popup-icon {
        margin-bottom: 15px;
    }
    
    .popup-icon i {
        font-size: 48px;
        color: #28a745;
    }
    
    .cart-confirmation-popup.error .popup-icon i {
        color: #dc3545;
    }
    
    .popup-title {
        margin: 0;
        color: #333;
        font-weight: 600;
        font-size: 1.3rem;
    }
    
    /* Body */
    .popup-body {
        padding: 20px 25px;
    }
    
    .popup-message {
        margin: 0 0 20px 0;
        color: #555;
        line-height: 1.5;
        text-align: center;
    }
    
    .product-details {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        color: #666;
    }
    
    .detail-row.total {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #dee2e6;
        font-weight: 600;
    }
    
    .total-price {
        color: #FFB6C1;
        font-size: 1.1em;
    }
    
    /* Footer dengan tombol */
    .popup-footer {
        padding: 20px 25px;
        display: flex;
        gap: 10px;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }
    
    .popup-footer .btn {
        flex: 1;
        padding: 12px;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .btn-continue {
        background: #6c757d;
        color: white;
    }
    
    .btn-continue:hover {
        background: #5a6268;
        transform: translateY(-1px);
    }
    
    .btn-view-cart {
        background: #FFB6C1;
        color: white;
    }
    
    .btn-view-cart:hover {
        background: #ff9aad;
        transform: translateY(-1px);
    }
    
    .btn-ok {
        background: #6c757d;
        color: white;
        width: 100%;
    }
    
    .btn-ok:hover {
        background: #5a6268;
    }
    
    /* Loading spinner */
    .cart-loading {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        z-index: 9997;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .cart-loading .spinner {
        width: 40px;
        height: 40px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #FFB6C1;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Cart counter animation */
    .pulse {
        animation: pulse 0.3s ease-in-out;
    }
    
    @keyframes pulse {
        0% { transform: translate(-50%, -50%) scale(1); }
        50% { transform: translate(-50%, -50%) scale(1.3); }
        100% { transform: translate(-50%, -50%) scale(1); }
    }
    
    /* Responsive */
    @media (max-width: 576px) {
        .cart-confirmation-popup {
            width: 95%;
        }
        
        .popup-footer {
            flex-direction: column;
        }
        
        .popup-header {
            padding: 20px 20px 15px;
        }
        
        .popup-body {
            padding: 15px 20px;
        }
        
        .popup-footer {
            padding: 15px 20px;
        }
    }
`;
document.head.appendChild(popupStyle);