/**
 * MAIN JAVASCRIPT FILE FOR INVENTORY MANAGEMENT SYSTEM
 * 
 * This file demonstrates modern JavaScript concepts and best practices:
 * - Event-driven programming with event listeners
 * - DOM manipulation and traversal
 * - AJAX concepts (though simplified here)
 * - Client-side validation for better user experience
 * - Modular function organization
 * - Progressive enhancement (works without JS, better with JS)
 */

/**
 * Initialize all JavaScript functionality when page loads
 * 
 * This demonstrates:
 * - DOMContentLoaded event (fires when HTML is loaded, before images)
 * - Modular initialization approach
 * - Separation of concerns (each feature has its own init function)
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap components
    initializeTooltips();
    
    // Initialize client-side form validation
    initializeFormValidation();
    
    // Initialize dynamic search functionality  
    initializeSearch();
    
    // Initialize dashboard auto-refresh
    initializeDashboardRefresh();
    
    // Initialize stock level alerts
    checkStockAlerts();
});

/**
 * Initialize Bootstrap tooltips for enhanced user experience
 * 
 * This demonstrates:
 * - DOM querying with querySelectorAll
 * - Array manipulation with slice.call (converts NodeList to Array)
 * - Bootstrap JavaScript component initialization
 * - Progressive enhancement (tooltips work without this, but better with it)
 */
function initializeTooltips() {
    // Find all elements that should have tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    
    // Initialize Bootstrap tooltip component for each element
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Enhanced form validation for better user experience
 * 
 * This demonstrates:
 * - Client-side validation (faster feedback than server-side only)
 * - HTML5 Constraint Validation API
 * - Event handling (submit events)
 * - CSS class manipulation for visual feedback
 * - Progressive enhancement (forms work without JS, better with JS)
 */
function initializeFormValidation() {
    // Find all forms that need enhanced validation
    const forms = document.querySelectorAll('.needs-validation');
    
    // Add validation logic to each form
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            // Check if form passes HTML5 validation
            if (!form.checkValidity()) {
                // Prevent submission if invalid
                event.preventDefault();
                event.stopPropagation();
            }
            // Add CSS class to show validation states
            form.classList.add('was-validated');
        }, false);
    });
    
    // Real-time SKU validation
    const skuInput = document.getElementById('sku');
    if (skuInput) {
        skuInput.addEventListener('blur', validateSKU);
    }
    
    // Quantity validation for stock movements
    const quantityInput = document.getElementById('quantity');
    const movementTypeSelect = document.getElementById('movement_type');
    
    if (quantityInput && movementTypeSelect) {
        movementTypeSelect.addEventListener('change', function() {
            validateStockMovement();
        });
        
        quantityInput.addEventListener('input', function() {
            validateStockMovement();
        });
    }
}

// SKU validation
function validateSKU() {
    const skuInput = document.getElementById('sku');
    const sku = skuInput.value.trim();
    
    if (sku.length < 3) {
        showFieldError(skuInput, 'SKU must be at least 3 characters long');
        return false;
    }
    
    // Check for valid SKU format (alphanumeric and hyphens)
    const skuPattern = /^[A-Za-z0-9\-]+$/;
    if (!skuPattern.test(sku)) {
        showFieldError(skuInput, 'SKU can only contain letters, numbers, and hyphens');
        return false;
    }
    
    clearFieldError(skuInput);
    return true;
}

// Stock movement validation
function validateStockMovement() {
    const productSelect = document.getElementById('product_id');
    const movementType = document.getElementById('movement_type').value;
    const quantity = parseInt(document.getElementById('quantity').value);
    
    if (!productSelect || !movementType || !quantity) return;
    
    const selectedOption = productSelect.options[productSelect.selectedIndex];
    const currentStock = parseInt(selectedOption.getAttribute('data-quantity') || 0);
    
    if (movementType === 'out' && quantity > currentStock) {
        showFieldError(document.getElementById('quantity'), 
            `Cannot remove ${quantity} units. Only ${currentStock} available.`);
        return false;
    }
    
    clearFieldError(document.getElementById('quantity'));
    return true;
}

// Show field error
function showFieldError(field, message) {
    clearFieldError(field);
    
    field.classList.add('is-invalid');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
}

// Clear field error
function clearFieldError(field) {
    field.classList.remove('is-invalid');
    
    const errorFeedback = field.parentNode.querySelector('.invalid-feedback');
    if (errorFeedback) {
        errorFeedback.remove();
    }
}

// Search functionality
function initializeSearch() {
    const searchInput = document.getElementById('search');
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                // Auto-submit search form after 500ms of no typing
                const form = searchInput.closest('form');
                if (form && searchInput.value.length >= 3) {
                    form.submit();
                }
            }, 500);
        });
    }
}

// Dashboard auto-refresh (every 5 minutes)
function initializeDashboardRefresh() {
    if (window.location.pathname === '/' || window.location.pathname.endsWith('index.php')) {
        setInterval(function() {
            // Only refresh if user hasn't been active recently
            if (getTimeSinceLastActivity() > 300000) { // 5 minutes
                window.location.reload();
            }
        }, 300000); // Check every 5 minutes
    }
}

// Track user activity
let lastActivity = Date.now();

document.addEventListener('mousemove', updateActivity);
document.addEventListener('keypress', updateActivity);
document.addEventListener('click', updateActivity);

function updateActivity() {
    lastActivity = Date.now();
}

function getTimeSinceLastActivity() {
    return Date.now() - lastActivity;
}

// Stock alerts
function checkStockAlerts() {
    const lowStockBadges = document.querySelectorAll('.badge-warning, .bg-warning');
    const criticalStockBadges = document.querySelectorAll('.badge-danger, .bg-danger');
    
    if (criticalStockBadges.length > 0) {
        showNotification('Critical stock alert! Some items are out of stock.', 'danger');
    } else if (lowStockBadges.length > 3) {
        showNotification(`${lowStockBadges.length} items are running low on stock.`, 'warning');
    }
}

// Notification system
function showNotification(message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show notification`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after duration
    setTimeout(function() {
        if (notification.parentNode) {
            notification.remove();
        }
    }, duration);
}

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

function formatNumber(number) {
    return new Intl.NumberFormat('en-US').format(number);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
}

// Confirmation dialogs
function confirmDelete(message = 'Are you sure you want to delete this item?') {
    return confirm(message);
}

// Print functionality
function printReport() {
    window.print();
}

// Export functionality (basic CSV export)
function exportToCSV(tableId, filename = 'export.csv') {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = [];
        const cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length; j++) {
            let text = cols[j].innerText;
            // Remove commas and newlines for CSV
            text = text.replace(/,/g, ';').replace(/\n/g, ' ');
            row.push('"' + text + '"');
        }
        
        csv.push(row.join(','));
    }
    
    // Download CSV
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    
    const a = document.createElement('a');
    a.setAttribute('hidden', '');
    a.setAttribute('href', url);
    a.setAttribute('download', filename);
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

// Local storage utilities
function saveToLocalStorage(key, data) {
    try {
        localStorage.setItem(key, JSON.stringify(data));
        return true;
    } catch (e) {
        console.error('Failed to save to localStorage:', e);
        return false;
    }
}

function loadFromLocalStorage(key) {
    try {
        const data = localStorage.getItem(key);
        return data ? JSON.parse(data) : null;
    } catch (e) {
        console.error('Failed to load from localStorage:', e);
        return null;
    }
}

// Form auto-save (for longer forms)
function initializeAutoSave(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            saveToLocalStorage(`autosave_${formId}`, data);
        });
    });
    
    // Restore saved data on page load
    const savedData = loadFromLocalStorage(`autosave_${formId}`);
    if (savedData) {
        Object.keys(savedData).forEach(key => {
            const input = form.querySelector(`[name="${key}"]`);
            if (input && input.type !== 'hidden') {
                input.value = savedData[key];
            }
        });
    }
}

// Clear auto-save data when form is successfully submitted
function clearAutoSave(formId) {
    localStorage.removeItem(`autosave_${formId}`);
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + S to save forms
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        const submitButton = document.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.click();
        }
    }
    
    // Ctrl/Cmd + N for new item (where applicable)
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        const addButton = document.querySelector('a[href*="add.php"]');
        if (addButton) {
            e.preventDefault();
            window.location.href = addButton.href;
        }
    }
    
    // Ctrl/Cmd + F to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        const searchInput = document.getElementById('search');
        if (searchInput) {
            e.preventDefault();
            searchInput.focus();
        }
    }
});

// Add loading states to forms
document.addEventListener('submit', function(e) {
    const form = e.target;
    const submitButton = form.querySelector('button[type="submit"]');
    
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        
        // Re-enable after 10 seconds to prevent permanent disable
        setTimeout(function() {
            submitButton.disabled = false;
            submitButton.innerHTML = submitButton.getAttribute('data-original-text') || 'Submit';
        }, 10000);
    }
});

// Store original button text for restore
document.addEventListener('DOMContentLoaded', function() {
    const submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(button => {
        button.setAttribute('data-original-text', button.innerHTML);
    });
});
