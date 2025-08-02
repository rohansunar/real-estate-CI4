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
    
    if (!sidebar || !sidebarToggle) return;
    
    // Toggle sidebar on mobile
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.add('show');
        sidebarOverlay.classList.add('show');
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
            console.error('Error fetching property details:', error);
            showNotification('Error loading property details', 'danger');
        });
}

/**
 * Show enquiry quick view modal
 */
function showEnquiryQuickView(enquiryId) {
    const modalHtml = `
        <div class="modal fade" id="enquiryQuickViewModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Enquiry Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <strong>Name:</strong> John Doe
                            </div>
                            <div class="col-6">
                                <strong>Email:</strong> john@example.com
                            </div>
                            <div class="col-6">
                                <strong>Phone:</strong> +1 234 567 8900
                            </div>
                            <div class="col-6">
                                <strong>Property Interest:</strong> House
                            </div>
                            <div class="col-12">
                                <strong>Message:</strong>
                                <p class="mt-1 text-muted">I'm interested in viewing this property. Please contact me to schedule a viewing.</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success">Mark as Read</button>
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

    // Add modal to DOM
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('enquiryQuickViewModal'));
    modal.show();

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
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
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
    const toastHtml = `
        <div class="toast" role="alert">
            <div class="toast-header bg-${type} text-white">
                <i class="fas fa-info-circle me-2"></i>
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">${message}</div>
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
            this.remove();
        });
    }
}

// Make functions globally available
window.showPropertyQuickView = showPropertyQuickView;
window.showEnquiryQuickView = showEnquiryQuickView;
window.showNotification = showNotification;
