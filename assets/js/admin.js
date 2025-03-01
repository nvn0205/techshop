// Admin panel JavaScript functionalities

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
    
    // Auto-hide alert messages after 5 seconds
    const alertMessages = document.querySelectorAll('.alert');
    if (alertMessages.length > 0) {
        setTimeout(function() {
            alertMessages.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    }
    
    // Confirm delete operations
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.dataset.confirm || 'Bạn có chắc chắn muốn xóa?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
    
    // Form validations
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
    
    // Toggle sidebar on mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
    }
    
    // Handle image preview in product form
    const imageUrlInput = document.getElementById('images');
    const imagePreviewContainer = document.getElementById('imagePreview');
    
    if (imageUrlInput && imagePreviewContainer) {
        imageUrlInput.addEventListener('input', function() {
            // Clear previous previews
            imagePreviewContainer.innerHTML = '';
            
            // Get URLs (one per line)
            const urls = this.value.split('\n').filter(url => url.trim() !== '');
            
            // Create preview for each URL
            urls.forEach(url => {
                if (url.trim()) {
                    const img = document.createElement('img');
                    img.src = url.trim();
                    img.className = 'img-thumbnail me-2 mb-2';
                    img.style.width = '100px';
                    img.style.height = '100px';
                    img.style.objectFit = 'contain';
                    imagePreviewContainer.appendChild(img);
                }
            });
        });
        
        // Trigger the input event to show initial previews
        imageUrlInput.dispatchEvent(new Event('input'));
    }
    
    // Calculate discount percentage automatically
    const priceInput = document.getElementById('price');
    const salePriceInput = document.getElementById('sale_price');
    const discountPercentDisplay = document.getElementById('discount_percent_display');
    
    if (priceInput && salePriceInput && discountPercentDisplay) {
        function updateDiscountPercent() {
            const price = parseFloat(priceInput.value) || 0;
            const salePrice = parseFloat(salePriceInput.value) || 0;
            
            if (price > 0 && salePrice > 0 && salePrice < price) {
                const discountPercent = Math.round((price - salePrice) / price * 100);
                discountPercentDisplay.textContent = discountPercent + '%';
            } else {
                discountPercentDisplay.textContent = '0%';
            }
        }
        
        priceInput.addEventListener('input', updateDiscountPercent);
        salePriceInput.addEventListener('input', updateDiscountPercent);
        
        // Initial calculation
        updateDiscountPercent();
    }
    
    // Search functionality for all admin pages
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const searchInput = this.querySelector('input[name="search"]');
            if (!searchInput.value.trim()) {
                e.preventDefault();
                searchInput.focus();
            }
        });
    }
    
    // Handle bulk actions (for future implementation)
    const bulkActionForm = document.getElementById('bulkActionForm');
    if (bulkActionForm) {
        bulkActionForm.addEventListener('submit', function(e) {
            const action = document.getElementById('bulkAction').value;
            const checkboxes = document.querySelectorAll('input[name="selected[]"]:checked');
            
            if (action === '') {
                e.preventDefault();
                alert('Vui lòng chọn một hành động');
                return;
            }
            
            if (checkboxes.length === 0) {
                e.preventDefault();
                alert('Vui lòng chọn ít nhất một mục');
                return;
            }
            
            if (action === 'delete' && !confirm('Bạn có chắc chắn muốn xóa các mục đã chọn?')) {
                e.preventDefault();
            }
        });
    }
    
    // Toggle all checkboxes
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="selected[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    // Statistics charts (placeholder for future implementation)
    const statsChart = document.getElementById('statsChart');
    if (statsChart) {
        // This would be implemented with Chart.js or similar library
        console.log('Stats chart would be initialized here');
    }
});

// Confirm delete operation
function confirmDelete(message) {
    return confirm(message || 'Bạn có chắc chắn muốn xóa?');
}

// Preview product image from URL
function previewImage(url, previewId) {
    const preview = document.getElementById(previewId);
    if (preview) {
        preview.src = url;
    }
}

// Format currency for display
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', { 
        style: 'currency', 
        currency: 'VND'
    }).format(amount);
}

// Date formatter for Vietnamese format
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN', { 
        day: '2-digit', 
        month: '2-digit', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
