// JavaScript cho ShopDunk
function TimKiem() {
    var searchForm = document.getElementById("searchForm");

    if (searchForm.style.display === "none" || searchForm.style.display === "") {
        searchForm.style.display = "block";
    } else {
        searchForm.style.display = "none";
    }
}
document.addEventListener('DOMContentLoaded', function() {
  
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize Bootstrap Popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Product images thumbnails functionality
    const mainProductImage = document.getElementById('mainProductImage');
    const thumbnails = document.querySelectorAll('.product-thumbnail');

    if (mainProductImage && thumbnails.length > 0) {
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function() {
                mainProductImage.src = this.dataset.src;
                mainProductImage.alt = this.alt;

                // Remove active class from all thumbnails
                thumbnails.forEach(t => t.classList.remove('active'));

                // Add active class to clicked thumbnail
                this.classList.add('active');
            });
        });
    }

    // Quantity input control
    const quantityInputs = document.querySelectorAll('.quantity-input');

    if (quantityInputs.length > 0) {
        quantityInputs.forEach(input => {
            const decreaseBtn = input.previousElementSibling;
            const increaseBtn = input.nextElementSibling;

            if (decreaseBtn && decreaseBtn.tagName === 'BUTTON') {
                decreaseBtn.addEventListener('click', function() {
                    const currentValue = parseInt(input.value);
                    if (currentValue > 1) {
                        input.value = currentValue - 1;
                    }
                });
            }

            if (increaseBtn && increaseBtn.tagName === 'BUTTON') {
                increaseBtn.addEventListener('click', function() {
                    const currentValue = parseInt(input.value);
                    input.value = currentValue + 1;
                });
            }
        });
    }

    // Newsletter signup form (prevent default submission for demo)
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const emailInput = this.querySelector('input[type="email"]');
            if (emailInput && emailInput.value) {
                alert('Cảm ơn bạn đã đăng ký nhận tin!');
                emailInput.value = '';
            }
        });
    }

    // Auto-hide messages after 5 seconds
    const alertMessages = document.querySelectorAll('.alert');
    if (alertMessages.length > 0) {
        setTimeout(function() {
            alertMessages.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    }

    // Form validation for checkout
    const checkoutForm = document.querySelector('form[name="checkout"]');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(event) {
            if (!checkoutForm.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            checkoutForm.classList.add('was-validated');
        });
    }

    // Sticky header on scroll
    const header = document.querySelector('header');
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                header.classList.add('sticky-top', 'shadow-sm');
            } else {
                header.classList.remove('sticky-top', 'shadow-sm');
            }
        });
    }

    // Add to cart animation
    const addToCartBtn = document.querySelector('button[name="add_to_cart"]');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            const cartIcon = document.querySelector('.fa-shopping-bag');
            if (cartIcon) {
                cartIcon.classList.add('fa-bounce');
                setTimeout(() => {
                    cartIcon.classList.remove('fa-bounce');
                }, 1000);
            }
        });
    }

    // Search autocomplete placeholder (would be implemented with AJAX in a real system)
    const searchInput = document.querySelector('input[name="keyword"]');
    if (searchInput) {
        const placeholders = [
            'iPhone 15 Pro Max',
            'MacBook Air M2',
            'iPad Air 5',
            'Apple Watch Series 8',
            'AirPods Pro 2'
        ];

        let index = 0;
        setInterval(() => {
            searchInput.setAttribute('placeholder', 'Tìm kiếm: ' + placeholders[index]);
            index = (index + 1) % placeholders.length;
        }, 3000);
    }
});

// Function to validate login form
function validateLoginForm() {
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');

    if (!usernameInput.value.trim()) {
        alert('Vui lòng nhập tên đăng nhập');
        usernameInput.focus();
        return false;
    }

    if (!passwordInput.value) {
        alert('Vui lòng nhập mật khẩu');
        passwordInput.focus();
        return false;
    }

    return true;
}

// Function to validate registration form
function validateRegisterForm() {
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');

    if (!usernameInput.value.trim()) {
        alert('Vui lòng nhập tên đăng nhập');
        usernameInput.focus();
        return false;
    }

    if (!emailInput.value.trim()) {
        alert('Vui lòng nhập email');
        emailInput.focus();
        return false;
    }

    // Simple email validation regex
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(emailInput.value.trim())) {
        alert('Vui lòng nhập địa chỉ email hợp lệ');
        emailInput.focus();
        return false;
    }

    if (!passwordInput.value) {
        alert('Vui lòng nhập mật khẩu');
        passwordInput.focus();
        return false;
    }

    if (passwordInput.value.length < 6) {
        alert('Mật khẩu phải có ít nhất 6 ký tự');
        passwordInput.focus();
        return false;
    }

    if (passwordInput.value !== confirmPasswordInput.value) {
        alert('Mật khẩu xác nhận không khớp');
        confirmPasswordInput.focus();
        return false;
    }

    return true;
}

// Function for changing product image in product detail page
function changeMainImage(src) {
    const mainImage = document.getElementById('mainProductImage');
    if (mainImage) {
        mainImage.src = src;

        // Update active state on thumbnails
        const thumbnails = document.querySelectorAll('.thumbnail');
        thumbnails.forEach(thumb => {
            if (thumb.src === src) {
                thumb.classList.add('active');
            } else {
                thumb.classList.remove('active');
            }
        });
    }
}

// Quantity control functions for product and cart pages
function decreaseQuantity(productId) {
    const inputId = productId ? 'quantity_' + productId : 'quantity';
    const quantityInput = document.getElementById(inputId);
    if (quantityInput) {
        const currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
        }
    }
}

function increaseQuantity(maxStock, productId) {
    const inputId = productId ? 'quantity_' + productId : 'quantity';
    const quantityInput = document.getElementById(inputId);
    if (quantityInput) {
        const currentValue = parseInt(quantityInput.value);
        const max = maxStock || 100;
        if (currentValue < max) {
            quantityInput.value = currentValue + 1;
        }
    }
}REM Update 17 - Add JavaScript functionality 
