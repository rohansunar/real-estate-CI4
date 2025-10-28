<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('styles') ?>
<style>
/* Admin Hierarchy Tree Styles - Bootstrap 5 Enhanced */
.admin-hierarchy-tree {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.hierarchy-card {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 0.75rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    position: relative;
}

.hierarchy-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.12);
    border-color: #0d6efd;
}

/* Level-based indentation and styling */
.hierarchy-card.level-0 {
    margin-left: 0;
    border-left: 4px solid #007bff;
}

.hierarchy-card.level-1 {
    margin-left: 2rem;
    border-left: 4px solid #28a745;
    background: #f8f9fa;
}

.hierarchy-card.level-2 {
    margin-left: 4rem;
    border-left: 4px solid #ffc107;
    background: #fffbf0;
}

.hierarchy-card.level-3 {
    margin-left: 6rem;
    border-left: 4px solid #fd7e14;
    background: #fff5f0;
}

.hierarchy-card.level-4 {
    margin-left: 8rem;
    border-left: 4px solid #e83e8c;
    background: #fdf2f8;
}

.hierarchy-card.level-5 {
    margin-left: 10rem;
    border-left: 4px solid #6f42c1;
    background: #f8f4ff;
}

/* Children container */
.hierarchy-children {
    margin-top: 1rem;
    padding-left: 1rem;
    border-left: 2px dashed #dee2e6;
    transition: all 0.3s ease;
}

.hierarchy-children.show {
    opacity: 1;
    max-height: none;
    overflow: visible;
}

.hierarchy-children.collapse {
    opacity: 0;
    max-height: 0;
    overflow: hidden;
}

/* Expand toggle button with enhanced animations */
.expand-toggle {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.expand-toggle:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.expand-toggle:active {
    transform: scale(0.98);
}

.expand-toggle i {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.expand-toggle[aria-expanded="true"] i {
    transform: rotate(180deg);
}

/* Loading state for expand buttons */
.expand-toggle.loading {
    pointer-events: none;
    opacity: 0.7;
}

.expand-toggle.loading i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Smooth slide animations for hierarchy children */
.hierarchy-children {
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    transform-origin: top;
}

.hierarchy-children.show {
    opacity: 1;
    max-height: 2000px;
    transform: scaleY(1);
    padding-top: 1rem;
}

.hierarchy-children.collapse {
    opacity: 0;
    max-height: 0;
    transform: scaleY(0);
    padding-top: 0;
}

/* Loading skeleton animation */
.loading-skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading-shimmer 1.5s infinite;
    border-radius: 0.5rem;
}

@keyframes loading-shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

/* Enhanced hover effects */
.hierarchy-card {
    transform-origin: center;
}

.hierarchy-card:hover {
    transform: translateY(-3px) scale(1.02);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Responsive enhancements */
@media (max-width: 768px) {
    .hierarchy-card.level-1,
    .hierarchy-card.level-2,
    .hierarchy-card.level-3,
    .hierarchy-card.level-4,
    .hierarchy-card.level-5 {
        margin-left: 1rem;
    }

    .expand-toggle {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }

    .hierarchy-children {
        padding-left: 0.5rem;
    }
}

.hierarchy-card-header {
    padding: 1.25rem;
    border-bottom: 1px solid #e9ecef;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 0.75rem 0.75rem 0 0;
}

.hierarchy-card-body {
    padding: 1.25rem;
}

.level-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    font-weight: 600;
    font-size: 0.75rem;
    color: white;
    margin-right: 0.75rem;
}

.level-0 { background: linear-gradient(135deg, #dc3545, #c82333); }
.level-1 { background: linear-gradient(135deg, #fd7e14, #e55a00); }
.level-2 { background: linear-gradient(135deg, #ffc107, #e0a800); }
.level-3 { background: linear-gradient(135deg, #198754, #146c43); }
.level-4 { background: linear-gradient(135deg, #0dcaf0, #0aa2c0); }
.level-5 { background: linear-gradient(135deg, #6f42c1, #59359a); }

.agent-profile {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: inherit;
    transition: color 0.2s ease;
}

.agent-profile:hover {
    color: #0d6efd;
}

.agent-avatar-large {
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.25rem;
    margin-right: 1rem;
    border: 3px solid #ffffff;
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
}



.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.stat-item {
    text-align: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 0.5rem;
    border: 1px solid #e9ecef;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #495057;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.pagination-controls {
    background: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-top: 1.5rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .hierarchy-card-header,
    .hierarchy-card-body {
        padding: 1rem;
    }
    
    .agent-avatar-large {
        width: 2.5rem;
        height: 2.5rem;
        font-size: 1rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Loading animation */
.loading-card {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
    border-radius: 0.75rem;
    height: 200px;
    margin-bottom: 1rem;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
                <div class="mb-3 mb-lg-0">
                    <h1 class="h2 fw-bold text-primary mb-1">
                        <i class="fas fa-sitemap me-2"></i>Agent Hierarchy Management
                    </h1>
                    <p class="text-muted mb-0">Complete overview of your multi-level agent network</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-primary" id="addRootAgentBtn">
                        <i class="fas fa-plus me-1"></i>Add Root Agent
                    </button>
                    <button class="btn btn-outline-success" id="exportHierarchyBtn">
                        <i class="fas fa-download me-1"></i>Export
                    </button>
                    <button class="btn btn-outline-info" id="refreshHierarchyBtn">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h3 class="mb-0" id="totalAgents">-</h3>
                    <small>Total Agents</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 bg-gradient text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-center">
                    <i class="fas fa-layer-group fa-2x mb-2"></i>
                    <h3 class="mb-0" id="maxDepth">-</h3>
                    <small>Max Depth</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 bg-gradient text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h3 class="mb-0" id="activeAgents">-</h3>
                    <small>Active Agents</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 bg-gradient text-white" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-2x mb-2"></i>
                    <h3 class="mb-0" id="activeAgents">-</h3>
                    <small>Active Agents</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Hierarchy Controls -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3">
                                <label class="text-muted mb-0">View:</label>
                                <select class="form-select" style="width: auto;" id="viewModeSelect">
                                    <option value="paginated" selected>Paginated View</option>
                                    <option value="tree">Tree View</option>
                                    <option value="levels">Level-based View</option>
                                </select>
                                
                                <label class="text-muted mb-0">Per Page:</label>
                                <select class="form-select" style="width: auto;" id="perPageSelect">
                                    <option value="5">5</option>
                                    <option value="10" selected>10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" id="searchInput" placeholder="Search agents...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hierarchy Content -->
    <div class="row">
        <div class="col-12">
            <!-- Loading State -->
            <div id="loadingState">
                <div class="loading-card"></div>
                <div class="loading-card"></div>
                <div class="loading-card"></div>
            </div>

            <!-- Hierarchy Container -->
            <div id="hierarchyContainer" class="admin-hierarchy-tree" style="display: none;">
                <!-- Hierarchy cards will be dynamically inserted here -->
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="text-center py-5" style="display: none;">
                <i class="fas fa-users fa-4x text-muted mb-4"></i>
                <h4 class="text-muted mb-3">No agents found</h4>
                <p class="text-muted mb-4">Start building your agent network by adding the first root agent.</p>
                <button class="btn btn-primary btn-lg" id="addFirstAgentBtn">
                    <i class="fas fa-plus me-2"></i>Add First Agent
                </button>
            </div>

            <!-- Pagination -->
            <div id="paginationContainer" class="pagination-controls" style="display: none;">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-muted">
                        Showing <span id="showingFrom">1</span> to <span id="showingTo">10</span> 
                        of <span id="totalItems">0</span> root agents
                    </div>
                    <nav aria-label="Hierarchy pagination">
                        <ul class="pagination mb-0" id="paginationList">
                            <!-- Pagination items will be inserted here -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Agent Details Modal -->
<div class="modal fade" id="agentModal" tabindex="-1" aria-labelledby="agentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="agentModalLabel">Associate Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="agentModalContent">
                <!-- Agent details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editAgentBtn">
                    <i class="fas fa-edit me-1"></i>Edit Associate
                </button>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Admin Hierarchy Management JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const hierarchyContainer = document.getElementById('hierarchyContainer');
    const loadingState = document.getElementById('loadingState');
    const emptyState = document.getElementById('emptyState');
    const paginationContainer = document.getElementById('paginationContainer');
    
    let currentPage = 1;
    let perPage = 10;
    let viewMode = 'paginated';
    let searchQuery = '';

    // Hierarchy expansion state
    const expandedNodes = new Set();
    const levelExpandedNodes = new Map(); // Track expanded nodes per level for accordion behavior

    // Memory management for event listeners and cleanup
    const activeEventListeners = new WeakMap();
    const hierarchyCache = new Map(); // Cache for hierarchy data
    const CACHE_DURATION = 5 * 60 * 1000; // 5 minutes cache
    
    // Utility function for debouncing
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Global cleanup on page unload to prevent memory leaks
    window.addEventListener('beforeunload', function() {
        cleanupEventListeners();
        hierarchyCache.clear();
        expandedNodes.clear();
        levelExpandedNodes.clear();
    });

    // Periodic cache cleanup every 5 minutes
    setInterval(cleanupCache, 5 * 60 * 1000);

    // Initialize
    loadHierarchy();
    
    // Event listeners
    document.getElementById('viewModeSelect').addEventListener('change', function() {
        viewMode = this.value;
        currentPage = 1;
        loadHierarchy();
    });
    
    document.getElementById('perPageSelect').addEventListener('change', function() {
        perPage = parseInt(this.value);
        currentPage = 1;
        loadHierarchy();
    });
    
    document.getElementById('searchInput').addEventListener('input', debounce(function() {
        searchQuery = this.value.trim();
        currentPage = 1;
        loadHierarchy();
    }, 300));
    
    document.getElementById('refreshHierarchyBtn').addEventListener('click', loadHierarchy);
    
    /**
     * Load hierarchy data with caching and performance optimization
     */
    async function loadHierarchy() {
        showLoading();

        try {
            // Create cache key
            const cacheKey = `hierarchy_${currentPage}_${perPage}_${viewMode}_${searchQuery}`;
            const cachedData = hierarchyCache.get(cacheKey);

            // Check if we have valid cached data
            if (cachedData && (Date.now() - cachedData.timestamp) < CACHE_DURATION) {
                // Using cached hierarchy data
                renderHierarchy(cachedData.data.agents);
                updatePagination(cachedData.data.pagination);
                updateStatistics(cachedData.data.statistics);
                showHierarchy();
                return;
            }

            const params = new URLSearchParams({
                page: currentPage,
                per_page: perPage,
                view_mode: viewMode,
                search: searchQuery
            });

            // Add timeout for better error handling
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 15000); // 15 second timeout

            const response = await fetch(`<?= base_url('dashboard/agents/hierarchy/data') ?>?${params}`, {
                signal: controller.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            clearTimeout(timeoutId);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            if (data.success) {
                // Cache the successful response
                hierarchyCache.set(cacheKey, {
                    data: data,
                    timestamp: Date.now()
                });

                // Clean old cache entries to prevent memory buildup
                cleanupCache();

                if (data.agents && data.agents.length > 0) {
                    renderHierarchy(data.agents);
                    updatePagination(data.pagination);
                    updateStatistics(data.statistics);
                    showHierarchy();
                } else {
                    showEmpty();
                }
            } else {
                showError(data.message || 'Failed to load hierarchy');
            }
        } catch (error) {
            let errorMessage = 'Failed to load hierarchy data';
            if (error.name === 'AbortError') {
                errorMessage = 'Request timed out. Please check your connection and try again.';
            } else if (error.message.includes('404')) {
                errorMessage = 'Hierarchy data not found. Please refresh the page.';
            } else if (error.message.includes('500')) {
                errorMessage = 'Server error occurred. Please try again later.';
            }

            showError(errorMessage);
        }
    }
    
    /**
     * Render hierarchy cards with tree structure support
     */
    function renderHierarchy(agents) {
        hierarchyContainer.innerHTML = '';

        agents.forEach(agent => {
            const card = createHierarchyCard(agent, 0);
            hierarchyContainer.appendChild(card);

            // Render children if expanded
            if (expandedNodes.has(agent.id) && agent.children) {
                renderChildren(card, agent.children, 1);
            }
        });

        // Add expansion event listeners
        addExpansionEventListeners();
    }

    /**
     * Render children agents recursively
     */
    function renderChildren(parentCard, children, level) {
        const childrenContainer = parentCard.querySelector('.hierarchy-children');
        if (!childrenContainer) return;

        childrenContainer.innerHTML = '';

        children.forEach(child => {
            const childCard = createHierarchyCard(child, level);
            childrenContainer.appendChild(childCard);

            // Render grandchildren if expanded
            if (expandedNodes.has(child.id) && child.children) {
                renderChildren(childCard, child.children, level + 1);
            }
        });
    }
    
    /**
     * Create hierarchy card element with expansion support
     */
    function createHierarchyCard(agent, level = 0) {
        const card = document.createElement('div');
        card.className = `hierarchy-card level-${level}`;
        card.dataset.agentId = agent.id;
        card.dataset.level = level;

        const hasChildren = agent.children && agent.children.length > 0;
        const totalDownline = agent.total_downline || 0;
        const isExpanded = expandedNodes.has(agent.id);


        card.innerHTML = `
            <div class="hierarchy-card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="agent-profile" onclick="showAgentDetails(${agent.id})">
                        <div class="level-badge level-${Math.min(level, 5)}">
                            L${level}
                        </div>
                        <div class="agent-avatar-large">
                            ${agent.name.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold">${agent.name}</h5>
                            <div class="text-muted small">${agent.email}</div>
                            <div class="text-muted small">ID: ${agent.unique_agent_id}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge ${agent.is_active ? 'bg-success' : 'bg-secondary'} fs-6">
                            ${agent.is_active ? 'Active' : 'Inactive'}
                        </span>
                        ${hasChildren ? `
                            <button class="btn btn-sm btn-outline-secondary expand-toggle"
                                    data-agent-id="${agent.id}"
                                    data-level="${level}"
                                    aria-expanded="${isExpanded}"
                                    title="${isExpanded ? 'Collapse' : 'Expand'} sub-agents">
                                <i class="fas fa-chevron-${isExpanded ? 'up' : 'down'}"></i>
                            </button>
                        ` : ''}
                    </div>
                </div>
            </div>

            <div class="hierarchy-card-body">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value">${totalDownline}</div>
                        <div class="stat-label">Total Downline</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">${hasChildren ? agent.children.length : 0}</div>
                        <div class="stat-label">Direct Children</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">${agent.is_active ? 'Yes' : 'No'}</div>
                        <div class="stat-label">Active</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">${new Date(agent.created_at).toLocaleDateString()}</div>
                        <div class="stat-label">Joined</div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button class="btn btn-sm btn-outline-primary" onclick="showAgentDetails(${agent.id})">
                        <i class="fas fa-eye me-1"></i>View Details
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="addSubAgent(${agent.id})">
                        <i class="fas fa-plus me-1"></i>Add Sub-Associate
                    </button>
                    ${hasChildren ? `
                        <button class="btn btn-sm btn-outline-info expand-toggle"
                                data-agent-id="${agent.id}"
                                data-level="${level}"
                                aria-expanded="${isExpanded}">
                            <i class="fas fa-chevron-${isExpanded ? 'up' : 'down'} me-1"></i>
                            ${isExpanded ? 'Collapse' : 'Expand'}
                        </button>
                    ` : ''}
                </div>
            </div>

            ${hasChildren ? `
                <div class="hierarchy-children ${isExpanded ? 'show' : 'collapse'}"
                     data-parent-id="${agent.id}">
                    <!-- Children will be rendered here -->
                </div>
            ` : ''}
        `;

        return card;
    }
    
    /**
     * Update pagination
     */
    function updatePagination(pagination) {
        if (!pagination || pagination.total_pages <= 1) {
            paginationContainer.style.display = 'none';
            return;
        }
        
        document.getElementById('showingFrom').textContent = pagination.from || 1;
        document.getElementById('showingTo').textContent = pagination.to || 0;
        document.getElementById('totalItems').textContent = pagination.total || 0;
        
        const paginationList = document.getElementById('paginationList');
        paginationList.innerHTML = '';
        
        // Previous button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Previous</a>`;
        paginationList.appendChild(prevLi);
        
        // Page numbers
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(pagination.total_pages, currentPage + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === currentPage ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i})">${i}</a>`;
            paginationList.appendChild(li);
        }
        
        // Next button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === pagination.total_pages ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Next</a>`;
        paginationList.appendChild(nextLi);
        
        paginationContainer.style.display = 'block';
    }
    
    /**
     * Update statistics
     */
    function updateStatistics(stats) {
        document.getElementById('totalAgents').textContent = stats.total_agents || 0;
        document.getElementById('maxDepth').textContent = stats.max_depth || 0;

        document.getElementById('activeAgents').textContent = stats.active_agents || 0;
    }
    
    /**
     * Change page
     */
    window.changePage = function(page) {
        if (page >= 1 && page !== currentPage) {
            currentPage = page;
            loadHierarchy();
        }
    };
    
    /**
     * Show agent details in modal
     */
    window.showAgentDetails = function(agentId) {
        try {
            const modal = new bootstrap.Modal(document.getElementById('agentModal'));
            const modalContent = document.getElementById('agentModalContent');
            const modalTitle = document.getElementById('agentModalLabel');
            const editBtn = document.getElementById('editAgentBtn');

            // Update modal title
            modalTitle.textContent = 'Loading Agent Details...';

            // Show loading spinner
            modalContent.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading agent details...</p>
                </div>
            `;

            // Show the modal
            modal.show();

            // Load agent details via AJAX with timeout
            const controller = new AbortController();
            const timeoutId = setTimeout(() => {
                controller.abort();
            }, 10000); // 10 second timeout

            fetch('<?= base_url('dashboard/agents/view/') ?>' + agentId, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
                },
                signal: controller.signal,
                cache: 'default'
            })
            .then(response => {
                clearTimeout(timeoutId);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.text();
            })
            .then(html => {
                // Update modal content
                modalContent.innerHTML = html;
                modalTitle.textContent = 'Agent Details';

                // Update edit button to point to correct agent
                editBtn.onclick = function() {
                    window.location.href = '<?= base_url('dashboard/agents/edit/') ?>' + agentId;
                };
            })
            .catch(error => {
                let errorMessage = 'Failed to load agent details. Please try again.';

                if (error.name === 'AbortError') {
                    errorMessage = 'Request timed out. Please check your connection and try again.';
                } else if (error.message.includes('404')) {
                    errorMessage = 'Agent not found or access denied.';
                } else if (error.message.includes('500')) {
                    errorMessage = 'Server error occurred. Please try again later.';
                }

                modalContent.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Error:</strong> ${errorMessage}
                        <div class="mt-3">
                            <button class="btn btn-primary btn-sm" onclick="showAgentDetails(${agentId})">
                                <i class="fas fa-sync-alt me-2"></i>
                                Retry
                            </button>
                            <button class="btn btn-secondary btn-sm ms-2" data-bs-dismiss="modal">
                                Close
                            </button>
                        </div>
                    </div>
                `;
                modalTitle.textContent = 'Error Loading Agent Details';
            });

        } catch (error) {
            alert('An unexpected error occurred. Please refresh the page and try again.');
        }
    };
    
    /**
     * View agent logs
     */
    window.viewAgentLogs = function(agentId) {
        try {
            // For now, show a simple alert with agent logs functionality
            // This can be enhanced with a dedicated logs modal later
            fetch('<?= base_url('dashboard/agents/logs/') ?>' + agentId, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Agent Logs for ${data.agent.name}:\n\nThis feature will be enhanced in future updates.`);
                } else {
                    alert('Failed to load agent logs: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                alert('Failed to load agent logs. Please try again.');
            });
        } catch (error) {
            alert('An unexpected error occurred. Please try again.');
        }
    };

    /**
     * Clean up old cache entries to prevent memory buildup
     */
    function cleanupCache() {
        const now = Date.now();
        const keysToDelete = [];

        for (const [key, value] of hierarchyCache.entries()) {
            if ((now - value.timestamp) > CACHE_DURATION) {
                keysToDelete.push(key);
            }
        }

        keysToDelete.forEach(key => hierarchyCache.delete(key));

        // Limit cache size to prevent memory issues
        if (hierarchyCache.size > 50) {
            const entries = Array.from(hierarchyCache.entries());
            entries.sort((a, b) => a[1].timestamp - b[1].timestamp);

            // Remove oldest 25% of entries
            const toRemove = Math.floor(entries.length * 0.25);
            for (let i = 0; i < toRemove; i++) {
                hierarchyCache.delete(entries[i][0]);
            }
        }
    }

    /**
     * Add expansion event listeners with proper cleanup tracking
     */
    function addExpansionEventListeners() {
        // Clean up existing listeners first
        cleanupEventListeners();

        document.querySelectorAll('.expand-toggle').forEach(btn => {
            const handler = function(e) {
                e.preventDefault();
                e.stopPropagation();

                const agentId = parseInt(this.dataset.agentId);
                const level = parseInt(this.dataset.level);

                toggleAgentExpansion(agentId, level);
            };

            btn.addEventListener('click', handler);

            // Track the listener for cleanup
            activeEventListeners.set(btn, handler);
        });
    }

    /**
     * Clean up event listeners to prevent memory leaks
     */
    function cleanupEventListeners() {
        document.querySelectorAll('.expand-toggle').forEach(btn => {
            const handler = activeEventListeners.get(btn);
            if (handler) {
                btn.removeEventListener('click', handler);
                activeEventListeners.delete(btn);
            }
        });
    }

    /**
     * Toggle agent expansion with smooth animations and loading states
     */
    async function toggleAgentExpansion(agentId, level) {
        try {
            const isCurrentlyExpanded = expandedNodes.has(agentId);
            const agentCard = document.querySelector(`[data-agent-id="${agentId}"]`);
            const toggleButtons = agentCard.querySelectorAll('.expand-toggle');
            const childrenContainer = agentCard.querySelector('.hierarchy-children');

            // Add loading state to buttons
            toggleButtons.forEach(btn => {
                btn.classList.add('loading');
                btn.disabled = true;
                const icon = btn.querySelector('i');
                if (icon) {
                    icon.classList.add('fa-spinner', 'fa-spin');
                    icon.classList.remove('fa-chevron-down', 'fa-chevron-up');
                }
            });

            if (!isCurrentlyExpanded) {
                // EXPANDING: First auto-collapse other nodes at the same level
                await autoCollapseNodesAtSameLevel(level, agentId);

                // Then expand the current node with animation
                expandedNodes.add(agentId);
                levelExpandedNodes.set(level, agentId);

                if (childrenContainer) {
                    // Smooth expand animation
                    childrenContainer.style.display = 'block';
                    childrenContainer.classList.remove('collapse');
                    childrenContainer.classList.add('show');

                    // Trigger reflow for smooth animation
                    childrenContainer.offsetHeight;
                }

            } else {
                // COLLAPSING: Collapse the current node and all its children with animation
                if (childrenContainer) {
                    childrenContainer.classList.remove('show');
                    childrenContainer.classList.add('collapse');

                    // Wait for animation to complete before hiding
                    setTimeout(() => {
                        if (childrenContainer.classList.contains('collapse')) {
                            childrenContainer.style.display = 'none';
                        }
                    }, 400);
                }

                collapseNodeAndChildren(agentId);

                // Remove from level tracking
                if (levelExpandedNodes.get(level) === agentId) {
                    levelExpandedNodes.delete(level);
                }
            }

            // Update button states with smooth transition
            setTimeout(() => {
                toggleButtons.forEach(btn => {
                    btn.classList.remove('loading');
                    btn.disabled = false;
                    btn.setAttribute('aria-expanded', !isCurrentlyExpanded);

                    const icon = btn.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-spinner', 'fa-spin');
                        if (!isCurrentlyExpanded) {
                            icon.classList.add('fa-chevron-up');
                            icon.classList.remove('fa-chevron-down');
                        } else {
                            icon.classList.add('fa-chevron-down');
                            icon.classList.remove('fa-chevron-up');
                        }
                    }

                    // Update button text if it has text content
                    const textSpan = btn.querySelector('.expand-text, span:not(.visually-hidden)');
                    if (textSpan && textSpan.textContent.includes('Expand') || textSpan.textContent.includes('Collapse')) {
                        textSpan.textContent = !isCurrentlyExpanded ? 'Collapse' : 'Expand';
                    }
                });
            }, 200);

            // Re-render the hierarchy to reflect changes (with debouncing)
            clearTimeout(window.hierarchyRenderTimeout);
            window.hierarchyRenderTimeout = setTimeout(() => {
                loadHierarchy();
            }, 100);

        } catch (error) {
            // Reset loading states on error
            const agentCard = document.querySelector(`[data-agent-id="${agentId}"]`);
            if (agentCard) {
                const toggleButtons = agentCard.querySelectorAll('.expand-toggle');
                toggleButtons.forEach(btn => {
                    btn.classList.remove('loading');
                    btn.disabled = false;
                });
            }
        }
    }

    /**
     * Auto-collapse other nodes at the same level with smooth animations
     */
    async function autoCollapseNodesAtSameLevel(level, currentAgentId) {
        const previouslyExpanded = levelExpandedNodes.get(level);

        if (previouslyExpanded && previouslyExpanded !== currentAgentId) {
            // Find and animate collapse of the previously expanded node
            const previousCard = document.querySelector(`[data-agent-id="${previouslyExpanded}"]`);
            if (previousCard) {
                const childrenContainer = previousCard.querySelector('.hierarchy-children');
                const toggleButtons = previousCard.querySelectorAll('.expand-toggle');

                if (childrenContainer && childrenContainer.classList.contains('show')) {
                    // Animate collapse
                    childrenContainer.classList.remove('show');
                    childrenContainer.classList.add('collapse');

                    // Update button states
                    toggleButtons.forEach(btn => {
                        btn.setAttribute('aria-expanded', 'false');
                        const icon = btn.querySelector('i');
                        if (icon) {
                            icon.classList.add('fa-chevron-down');
                            icon.classList.remove('fa-chevron-up');
                        }

                        const textSpan = btn.querySelector('.expand-text, span:not(.visually-hidden)');
                        if (textSpan && textSpan.textContent.includes('Collapse')) {
                            textSpan.textContent = 'Expand';
                        }
                    });

                    // Wait for animation to complete
                    await new Promise(resolve => setTimeout(resolve, 400));

                    if (childrenContainer.classList.contains('collapse')) {
                        childrenContainer.style.display = 'none';
                    }
                }
            }

            // Collapse the previously expanded node and its children
            collapseNodeAndChildren(previouslyExpanded);
        }
    }

    /**
     * Collapse a node and all its children recursively
     */
    function collapseNodeAndChildren(agentId) {
        expandedNodes.delete(agentId);

        // Find and collapse all children recursively
        const agentCard = document.querySelector(`[data-agent-id="${agentId}"]`);
        if (agentCard) {
            const childCards = agentCard.querySelectorAll('.hierarchy-children [data-agent-id]');
            childCards.forEach(childCard => {
                const childId = parseInt(childCard.dataset.agentId);
                expandedNodes.delete(childId);
            });
        }
    }

    /**
     * Add sub-agent
     */
    window.addSubAgent = function(parentId) {
        window.location.href = `<?= base_url('dashboard/agents/create') ?>?parent_id=${parentId}`;
    };
    

    
    /**
     * Show enhanced loading state with skeleton screens
     */
    function showLoading() {
        loadingState.style.display = 'block';
        hierarchyContainer.style.display = 'none';
        emptyState.style.display = 'none';
        paginationContainer.style.display = 'none';

        // Create skeleton loading cards for better UX
        const skeletonHTML = `
            <div class="row g-3">
                ${Array.from({length: 3}, (_, i) => `
                    <div class="col-12">
                        <div class="hierarchy-card loading-skeleton" style="height: 200px;">
                            <div class="hierarchy-card-header" style="background: transparent;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="loading-skeleton me-3" style="width: 60px; height: 60px; border-radius: 50%;"></div>
                                        <div>
                                            <div class="loading-skeleton mb-2" style="width: 150px; height: 20px;"></div>
                                            <div class="loading-skeleton mb-1" style="width: 200px; height: 16px;"></div>
                                            <div class="loading-skeleton" style="width: 120px; height: 14px;"></div>
                                        </div>
                                    </div>
                                    <div class="loading-skeleton" style="width: 80px; height: 32px; border-radius: 16px;"></div>
                                </div>
                            </div>
                            <div class="hierarchy-card-body" style="background: transparent;">
                                <div class="row g-2">
                                    ${Array.from({length: 4}, () => `
                                        <div class="col-6">
                                            <div class="loading-skeleton mb-2" style="width: 60px; height: 24px;"></div>
                                            <div class="loading-skeleton" style="width: 100px; height: 16px;"></div>
                                        </div>
                                    `).join('')}
                                </div>
                                <div class="d-flex justify-content-end gap-2 mt-3">
                                    <div class="loading-skeleton" style="width: 100px; height: 32px; border-radius: 4px;"></div>
                                    <div class="loading-skeleton" style="width: 120px; height: 32px; border-radius: 4px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;

        // Show skeleton in a temporary container
        const tempContainer = document.createElement('div');
        tempContainer.innerHTML = skeletonHTML;
        tempContainer.className = 'skeleton-container';
        tempContainer.style.display = 'block';

        // Insert skeleton after loading state
        loadingState.parentNode.insertBefore(tempContainer, loadingState.nextSibling);

        // Store reference for cleanup
        window.currentSkeleton = tempContainer;
    }
    
    /**
     * Show hierarchy with smooth transition from skeleton
     */
    function showHierarchy() {
        // Clean up skeleton loading
        if (window.currentSkeleton) {
            window.currentSkeleton.style.opacity = '0';
            setTimeout(() => {
                if (window.currentSkeleton && window.currentSkeleton.parentNode) {
                    window.currentSkeleton.parentNode.removeChild(window.currentSkeleton);
                }
                window.currentSkeleton = null;
            }, 300);
        }

        loadingState.style.display = 'none';
        hierarchyContainer.style.display = 'block';
        emptyState.style.display = 'none';

        // Smooth fade-in animation for hierarchy content
        hierarchyContainer.style.opacity = '0';
        hierarchyContainer.style.transform = 'translateY(20px)';

        requestAnimationFrame(() => {
            hierarchyContainer.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            hierarchyContainer.style.opacity = '1';
            hierarchyContainer.style.transform = 'translateY(0)';
        });
    }
    
    /**
     * Show empty state
     */
    function showEmpty() {
        loadingState.style.display = 'none';
        hierarchyContainer.style.display = 'none';
        emptyState.style.display = 'block';
        paginationContainer.style.display = 'none';
    }
    
    /**
     * Show error
     */
    function showError(message) {
        loadingState.style.display = 'none';
        hierarchyContainer.innerHTML = `
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
            </div>
        `;
        hierarchyContainer.style.display = 'block';
        emptyState.style.display = 'none';
        paginationContainer.style.display = 'none';
    }
    
    /**
     * Debounce function
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});
</script>
<?= $this->endSection() ?>
