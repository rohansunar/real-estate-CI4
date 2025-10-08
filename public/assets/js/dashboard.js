// Dashboard JavaScript functionality with Bootstrap 5

document.addEventListener('DOMContentLoaded', function() {
    // Initialize sidebar functionality
    initializeSidebar();
    
    // Initialize data tables
    initializeDataTables();
    
    // Initialize modals
    initializeModals();
    
    // Initialize tooltips
    initializeTooltips();
    
    // Auto-hide toasts after 5 seconds
    autoHideToasts();
});

/**
 * Initialize sidebar functionality
 */
function initializeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarClose = document.getElementById('sidebarClose');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    // Check if required elements exist
    if (!sidebar || !sidebarToggle) {
        return; // Exit if required elements not found
    }
    
    // Toggle sidebar on mobile
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.add('show');
        if (sidebarOverlay) {
            sidebarOverlay.classList.add('show');
        }
        document.body.style.overflow = 'hidden';
    });
    
    // Close sidebar
    function closeSidebar() {
        sidebar.classList.remove('show');
        sidebarOverlay.classList.remove('show');
        document.body.style.overflow = '';
    }
    
    if (sidebarClose) {
        sidebarClose.addEventListener('click', closeSidebar);
    }
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }
    
    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('show')) {
            closeSidebar();
        }
    });
    
    // Handle responsive behavior
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            closeSidebar();
        }
    });
}

/**
 * Initialize data tables functionality
 */
function initializeDataTables() {
    const tables = document.querySelectorAll('[data-table]');
    
    tables.forEach(table => {
        new DataTable(table);
    });
}

/**
 * Data table class
 */
class DataTable {
    constructor(tableElement) {
        this.table = tableElement;
        this.currentSort = { column: null, direction: 'asc' };
        this.currentPage = 1;
        this.itemsPerPage = 10;
        this.data = [];
        this.filteredData = [];
        this.init();
    }

    init() {
        if (!this.table) return;

        // Initialize sorting
        this.table.querySelectorAll('.sortable').forEach(header => {
            header.addEventListener('click', () => {
                const column = header.getAttribute('data-sort');
                this.sort(column);
            });
        });

        // Initialize search
        const searchInput = document.querySelector(`[data-table-search="${this.table.id}"]`);
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.search(e.target.value);
            });
        }

        // Load initial data
        this.loadData();
    }

    loadData() {
        // Extract data from table rows
        const rows = this.table.querySelectorAll('tbody tr');
        this.data = Array.from(rows).map(row => {
            const cells = row.querySelectorAll('td');
            return {
                element: row,
                data: Array.from(cells).map(cell => cell.textContent.trim())
            };
        });
        this.filteredData = [...this.data];
    }

    sort(column) {
        const columnIndex = parseInt(column);
        
        if (this.currentSort.column === columnIndex) {
            this.currentSort.direction = this.currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            this.currentSort.column = columnIndex;
            this.currentSort.direction = 'asc';
        }

        this.filteredData.sort((a, b) => {
            const aValue = a.data[columnIndex] || '';
            const bValue = b.data[columnIndex] || '';
            
            const comparison = aValue.localeCompare(bValue, undefined, { numeric: true });
            return this.currentSort.direction === 'asc' ? comparison : -comparison;
        });

        this.render();
        this.updateSortIndicators();
    }

    search(query) {
        if (!query.trim()) {
            this.filteredData = [...this.data];
        } else {
            const searchTerm = query.toLowerCase();
            this.filteredData = this.data.filter(row => 
                row.data.some(cell => cell.toLowerCase().includes(searchTerm))
            );
        }
        
        this.currentPage = 1;
        this.render();
    }

    render() {
        const tbody = this.table.querySelector('tbody');
        if (!tbody) return;

        // Clear current rows
        tbody.innerHTML = '';

        // Calculate pagination
        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        const pageData = this.filteredData.slice(startIndex, endIndex);

        // Render rows
        pageData.forEach(row => {
            tbody.appendChild(row.element.cloneNode(true));
        });

        // Update pagination info
        this.updatePaginationInfo();
    }

    updateSortIndicators() {
        this.table.querySelectorAll('.sortable').forEach(header => {
            const column = parseInt(header.getAttribute('data-sort'));
            let indicator = header.querySelector('.sort-indicator');
            
            if (!indicator) {
                indicator = document.createElement('span');
                indicator.className = 'sort-indicator';
                header.appendChild(indicator);
            }

            if (column === this.currentSort.column) {
                indicator.innerHTML = this.currentSort.direction === 'asc' ? '↑' : '↓';
                indicator.classList.add('active');
            } else {
                indicator.innerHTML = '↕';
                indicator.classList.remove('active');
            }
        });
    }

    updatePaginationInfo() {
        const info = document.querySelector(`[data-table-info="${this.table.id}"]`);
        if (info) {
            const start = (this.currentPage - 1) * this.itemsPerPage + 1;
            const end = Math.min(start + this.itemsPerPage - 1, this.filteredData.length);
            info.textContent = `Showing ${start}-${end} of ${this.filteredData.length} results`;
        }
    }
}

/**
 * Initialize modals
 */
function initializeModals() {
    // Property quick view buttons
    document.querySelectorAll('[data-property-quick-view]').forEach(button => {
        button.addEventListener('click', function() {
            const propertyId = this.getAttribute('data-property-id');
            showPropertyQuickView(propertyId);
        });
    });

    // Enquiry quick view buttons
    document.querySelectorAll('[data-enquiry-quick-view]').forEach(button => {
        button.addEventListener('click', function() {
            const enquiryId = this.getAttribute('data-enquiry-id');
            showEnquiryQuickView(enquiryId);
        });
    });
}

/**
 * Show property quick view modal
 */
function showPropertyQuickView(propertyId) {

    // Fetch property details
    fetch(`/properties/details/${propertyId}`)
        .then(response => response.json())
        .then(property => {
            if (property.error) {
                showNotification('Property not found', 'danger');
                return;
            }

            // Parse images array
            let images = [];
            try {
                images = property.images ? JSON.parse(property.images) : [];
            } catch (e) {
                images = [];
            }

            const firstImage = images.length > 0 ? images[0] : null;
            const imageHtml = firstImage
                ? `<img src="/${firstImage}" class="img-fluid rounded" alt="${property.title}" style="height: 200px; width: 100%; object-fit: cover;">`
                : `<div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                     <span class="text-muted">No Image Available</span>
                   </div>`;

            const modalHtml = `
                <div class="modal fade" id="propertyQuickViewModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Property Quick View</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        ${imageHtml}
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-semibold">${property.title}</h6>
                                        <div class="mb-2">
                                            <span class="badge bg-primary">${property.type.charAt(0).toUpperCase() + property.type.slice(1)}</span>
                                        </div>
                                        <p class="text-muted mb-2">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            ${property.location}
                                        </p>
                                        ${property.area ? `<p class="text-muted mb-2">
                                            <i class="fas fa-expand-arrows-alt me-1"></i>
                                            ${parseInt(property.area).toLocaleString()} sq ft
                                        </p>` : ''}
                                        <p class="text-muted small">${property.description.substring(0, 150)}${property.description.length > 150 ? '...' : ''}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <a href="/dashboard/properties/edit/${property.id}" class="btn btn-primary">
                                    <i class="fas fa-edit me-1"></i>Edit Property
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal
            const existingModal = document.getElementById('propertyQuickViewModal');
            if (existingModal) {
                existingModal.remove();
            }

            // Add modal to DOM
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('propertyQuickViewModal'));
            modal.show();

            // Clean up when modal is hidden
            document.getElementById('propertyQuickViewModal').addEventListener('hidden.bs.modal', function() {
                this.remove();
            });
        })
        .catch(error => {
            showNotification('Error loading property details', 'danger');
        });
}

/**
 * Show enquiry quick view modal with real data
 */
function showEnquiryQuickView(enquiryId) {
    // Show loading modal first
    const loadingModalHtml = `
        <div class="modal fade" id="enquiryQuickViewModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Enquiry Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading enquiry details...</p>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove existing modal
    const existingModal = document.getElementById('enquiryQuickViewModal');
    if (existingModal) {
        existingModal.remove();
    }

    // Add loading modal to DOM
    document.body.insertAdjacentHTML('beforeend', loadingModalHtml);
    const modal = new bootstrap.Modal(document.getElementById('enquiryQuickViewModal'));
    modal.show();

    // Fetch enquiry details
    fetch(`/dashboard/enquiries/details/${enquiryId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const enquiry = data.enquiry;
            const formattedDate = new Date(enquiry.created_at).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            // Update modal content with real data
            const modalContent = `
                <div class="modal-header">
                    <h5 class="modal-title">Enquiry Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <strong>Name:</strong> ${enquiry.name}
                        </div>
                        <div class="col-6">
                            <strong>Email:</strong>
                            <a href="mailto:${enquiry.email}" class="text-decoration-none">${enquiry.email}</a>
                        </div>
                        <div class="col-6">
                            <strong>Phone:</strong>
                            <a href="tel:${enquiry.phone}" class="text-decoration-none">${enquiry.phone}</a>
                        </div>
                        <div class="col-6">
                            <strong>Property Interest:</strong> ${enquiry.properties_in}
                        </div>
                        <div class="col-12">
                            <strong>Enquiry Date:</strong> ${formattedDate}
                        </div>
                        <div class="col-12">
                            <strong>Status:</strong>
                            <span class="badge ${enquiry.is_read ? 'bg-success' : 'bg-warning'}">
                                <i class="fas fa-${enquiry.is_read ? 'check-circle' : 'bell'} me-1"></i>
                                ${enquiry.is_read ? 'Read' : 'Unread'}
                            </span>
                        </div>
                        <div class="col-12">
                            <strong>Message:</strong>
                            <div class="mt-2 p-3 bg-light rounded">
                                <p class="mb-0 text-muted">${enquiry.message}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    ${!enquiry.is_read ? `<button type="button" class="btn btn-success" onclick="markEnquiryAsRead(${enquiry.id}, this)">
                        <i class="fas fa-check me-2"></i>Mark as Read
                    </button>` : ''}
                </div>
            `;

            document.querySelector('#enquiryQuickViewModal .modal-content').innerHTML = modalContent;
        } else {
            // Show error message
            const errorContent = `
                <div class="modal-header">
                    <h5 class="modal-title">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-5">
                    <div class="text-danger mb-3">
                        <i class="fas fa-exclamation-triangle fs-1"></i>
                    </div>
                    <h6>Failed to load enquiry details</h6>
                    <p class="text-muted">${data.message || 'Please try again later.'}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            `;
            document.querySelector('#enquiryQuickViewModal .modal-content').innerHTML = errorContent;
        }
    })
    .catch(error => {
        // Show error message
        const errorContent = `
            <div class="modal-header">
                <h5 class="modal-title">Error</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-5">
                <div class="text-danger mb-3">
                    <i class="fas fa-exclamation-triangle fs-1"></i>
                </div>
                <h6>Network Error</h6>
                <p class="text-muted">Failed to connect to server. Please check your connection and try again.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        `;
        document.querySelector('#enquiryQuickViewModal .modal-content').innerHTML = errorContent;
    });

    // Clean up when modal is hidden
    document.getElementById('enquiryQuickViewModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

/**
 * Initialize tooltips
 */
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltips = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Register cleanup function to prevent memory leaks
    window.addEventListener('beforeunload', function() {
        tooltips.forEach(tooltip => {
            if (tooltip && typeof tooltip.dispose === 'function') {
                tooltip.dispose();
            }
        });
    });
}

/**
 * Auto-hide toasts
 */
function autoHideToasts() {
    const toasts = document.querySelectorAll('.toast.show');
    toasts.forEach(toast => {
        setTimeout(() => {
            const bsToast = bootstrap.Toast.getOrCreateInstance(toast);
            bsToast.hide();
        }, 5000);
    });
}

/**
  * Show notification toast
  */
function showNotification(message, type = 'info') {
    // Ensure message is a string and not undefined
    const safeMessage = message || 'An unknown error occurred';
    const safeType = ['success', 'danger', 'warning', 'info'].includes(type) ? type : 'info';

    const toastHtml = `
        <div class="toast" role="alert">
            <div class="toast-header bg-${safeType} text-white">
                <i class="fas fa-info-circle me-2"></i>
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">${safeMessage}</div>
        </div>
    `;

    const toastContainer = document.querySelector('.toast-container');
    if (toastContainer) {
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        const newToast = toastContainer.lastElementChild;
        const bsToast = new bootstrap.Toast(newToast);
        bsToast.show();

        // Remove toast element after it's hidden
        newToast.addEventListener('hidden.bs.toast', function() {
            if (this && this.remove) {
                this.remove();
            }
        });
    } else {
        // Fallback to browser alert if toast container doesn't exist
        console.warn('Toast container not found, falling back to alert');
        alert(safeMessage);
    }
}

/**
 * Mark enquiry as read from modal
 */
function markEnquiryAsRead(enquiryId, buttonElement = null) {
    const button = buttonElement || document.querySelector(`button[onclick="markEnquiryAsRead(${enquiryId})"]`);
    const originalText = button.innerHTML;

    // Show loading state
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Marking as Read...';
    button.disabled = true;

    fetch(`/dashboard/enquiries/mark-read/${enquiryId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hide the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('enquiryQuickViewModal'));
            modal.hide();

            // Show success notification
            showNotification('Enquiry marked as read successfully', 'success');

            // Reload page after short delay to update the enquiry list
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            // Restore button state
            button.innerHTML = originalText;
            button.disabled = false;
            showNotification('Failed to mark enquiry as read', 'danger');
        }
    })
    .catch(() => {
        // Error marking enquiry as read
        // Restore button state
        button.innerHTML = originalText;
        button.disabled = false;
        showNotification('Network error. Please try again.', 'danger');
    });
}

// Make functions globally available
window.showPropertyQuickView = showPropertyQuickView;
window.showEnquiryQuickView = showEnquiryQuickView;
window.showNotification = showNotification;
window.markEnquiryAsRead = markEnquiryAsRead;
