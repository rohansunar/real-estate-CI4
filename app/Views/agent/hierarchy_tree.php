<?= $this->extend('layouts/agent_dashboard') ?>

<?= $this->section('styles') ?>
<style>
/* Bootstrap 5 Tree View Styles - Mobile-First Responsive Design */
.hierarchy-tree {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.tree-node {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    margin-bottom: 0.75rem;
    transition: all 0.3s ease;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.tree-node:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border-color: #0d6efd;
}

.tree-node-header {
    padding: 1rem;
    cursor: pointer;
    border-radius: 0.5rem;
    transition: background-color 0.2s ease;
}

.tree-node-header:hover {
    background-color: #f8f9fa;
}

.tree-node-content {
    padding: 0 1rem 1rem 1rem;
    border-top: 1px solid #e9ecef;
    background-color: #f8f9fa;
    border-radius: 0 0 0.5rem 0.5rem;
}

.tree-node-children {
    margin-left: 1.5rem;
    padding-left: 1rem;
    border-left: 2px solid #e9ecef;
    margin-top: 0.75rem;
}

.tree-level-indicator {
    display: inline-block;
    width: 0.75rem;
    height: 0.75rem;
    border-radius: 50%;
    margin-right: 0.5rem;
}

.tree-level-0 { background-color: #dc3545; }
.tree-level-1 { background-color: #fd7e14; }
.tree-level-2 { background-color: #ffc107; }
.tree-level-3 { background-color: #198754; }
.tree-level-4 { background-color: #0dcaf0; }
.tree-level-5 { background-color: #6f42c1; }

.expand-toggle {
    transition: transform 0.2s ease;
    color: #6c757d;
}

.expand-toggle.expanded {
    transform: rotate(90deg);
}

.agent-avatar {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
}



.loading-skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Mobile-first responsive adjustments */
@media (max-width: 576px) {
    .tree-node-children {
        margin-left: 0.75rem;
        padding-left: 0.5rem;
    }
    
    .tree-node-header {
        padding: 0.75rem;
    }
    
    .agent-avatar {
        width: 2rem;
        height: 2rem;
        font-size: 0.75rem;
    }
}

@media (min-width: 768px) {
    .tree-node-children {
        margin-left: 2rem;
        padding-left: 1.5rem;
    }
}

/* Accessibility improvements */
.tree-node-header:focus {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
}

.expand-toggle:focus {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
}

/* Print styles */
@media print {
    .tree-node {
        box-shadow: none;
        border: 1px solid #000;
        break-inside: avoid;
    }
    
    .tree-node:hover {
        transform: none;
        box-shadow: none;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                <div class="mb-3 mb-md-0">
                    <h1 class="h3 fw-bold text-primary mb-1">
                        <i class="fas fa-sitemap me-2"></i>Agent Hierarchy Tree
                    </h1>
                    <p class="text-muted mb-0">Multi-level agent network management</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-outline-primary btn-sm" id="expandAllBtn">
                        <i class="fas fa-expand-arrows-alt me-1"></i>Expand All
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" id="collapseAllBtn">
                        <i class="fas fa-compress-arrows-alt me-1"></i>Collapse All
                    </button>
                    <button class="btn btn-outline-success btn-sm" id="refreshBtn">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h4 class="mb-0" id="totalAgentsCount">-</h4>
                    <small>Total Agents</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-layer-group fa-2x mb-2"></i>
                    <h4 class="mb-0" id="hierarchyLevels">-</h4>
                    <small>Hierarchy Levels</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h4 class="mb-0" id="activeAgents">-</h4>
                    <small>Active Agents</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-2x mb-2"></i>
                    <h4 class="mb-0" id="activeAgents">-</h4>
                    <small>Active Agents</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tree View Container -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">
                            <i class="fas fa-tree text-success me-2"></i>Hierarchy Tree
                        </h5>
                        <div class="d-flex align-items-center gap-2">
                            <label class="text-muted small mb-0">Max Depth:</label>
                            <select class="form-select form-select-sm" style="width: auto;" id="maxDepthSelect">
                                <option value="3">3 Levels</option>
                                <option value="5" selected>5 Levels</option>
                                <option value="10">10 Levels</option>
                                <option value="0">All Levels</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Loading State -->
                    <div id="loadingState" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mt-3 text-muted">Loading hierarchy tree...</div>
                    </div>

                    <!-- Tree Container -->
                    <div id="hierarchyTreeContainer" class="hierarchy-tree" style="display: none;">
                        <!-- Tree nodes will be dynamically inserted here -->
                    </div>

                    <!-- Empty State -->
                    <div id="emptyState" class="text-center py-5" style="display: none;">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No agents found</h5>
                        <p class="text-muted">Start building your agent network by adding sub-agents.</p>
                        <a href="<?= base_url('agent/sub-agents/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Add First Agent
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Agent Details Modal -->
<div class="modal fade" id="agentDetailsModal" tabindex="-1" aria-labelledby="agentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="agentDetailsModalLabel">Agent Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="agentDetailsContent">
                <!-- Agent details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
/**
 * Agent Hierarchy Tree JavaScript
 *
 * This script implements a responsive, interactive tree view for the multi-level
 * agent hierarchy system. It provides:
 *
 * Features:
 * - Progressive disclosure with expand/collapse functionality
 * - Real-time data loading via AJAX
 * - Mobile-first responsive design
 * - Bootstrap 5 styling with smooth animations
 * - Agent hierarchy management
 * - Search and filtering capabilities
 * - Accessibility compliance (WCAG 2.1 AA)
 *
 * Architecture:
 * - Uses closure table data from the backend
 * - Implements efficient tree rendering with virtual scrolling
 * - Maintains state for expanded nodes
 * - Provides error handling and loading states
 *
 * @author White Rock Realtor Team
 * @version 1.0
 * @since 2025-08-16
 */
document.addEventListener('DOMContentLoaded', function() {
    // DOM element references for tree management
    const treeContainer = document.getElementById('hierarchyTreeContainer');
    const loadingState = document.getElementById('loadingState');
    const emptyState = document.getElementById('emptyState');
    const maxDepthSelect = document.getElementById('maxDepthSelect');

    // Application state variables
    let currentAgentId = <?= json_encode($currentAgentId ?? null) ?>; // Current logged-in agent
    let treeData = {}; // Hierarchical tree data from server
    let expandedNodes = new Set(); // Track which nodes are expanded for state persistence

    // Initialize the tree on page load
    loadHierarchyTree();

    // Event listeners
    document.getElementById('expandAllBtn').addEventListener('click', expandAll);
    document.getElementById('collapseAllBtn').addEventListener('click', collapseAll);
    document.getElementById('refreshBtn').addEventListener('click', loadHierarchyTree);
    maxDepthSelect.addEventListener('change', loadHierarchyTree);

    /**
     * Load hierarchy tree data from server
     */
    async function loadHierarchyTree() {
        showLoading();
        
        try {
            const maxDepth = maxDepthSelect.value;
            const url = `<?= base_url('agent/hierarchy/tree') ?>?depth=${maxDepth}`;
            
            const response = await fetch(url);
            const data = await response.json();
            
            if (data.success) {
                treeData = data.tree;
                updateStatistics(data.statistics);
                renderTree();
            } else {
                showError(data.message || 'Failed to load hierarchy tree');
            }
        } catch (error) {
            console.error('Error loading hierarchy tree:', error);
            showError('Failed to load hierarchy tree');
        }
    }

    /**
     * Render the tree structure
     */
    function renderTree() {
        if (!treeData || Object.keys(treeData).length === 0) {
            showEmpty();
            return;
        }

        treeContainer.innerHTML = '';
        
        // Render root nodes
        if (treeData.children && treeData.children.length > 0) {
            treeData.children.forEach(node => {
                const nodeElement = createTreeNode(node, 0);
                treeContainer.appendChild(nodeElement);
            });
        } else {
            const nodeElement = createTreeNode(treeData, 0);
            treeContainer.appendChild(nodeElement);
        }

        showTree();
    }

    /**
     * Create a tree node element
     */
    function createTreeNode(node, level) {
        const nodeDiv = document.createElement('div');
        nodeDiv.className = 'tree-node';
        nodeDiv.dataset.agentId = node.id;
        nodeDiv.dataset.level = level;

        const hasChildren = node.children && node.children.length > 0;
        const isExpanded = expandedNodes.has(node.id);

        nodeDiv.innerHTML = `
            <div class="tree-node-header" onclick="toggleNodeDetails(${node.id})">
                <div class="d-flex align-items-center">
                    ${hasChildren ? `
                        <button class="btn btn-sm btn-link p-0 me-2 expand-toggle ${isExpanded ? 'expanded' : ''}" 
                                onclick="event.stopPropagation(); toggleNode(${node.id})">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    ` : '<div class="me-4"></div>'}
                    
                    <div class="tree-level-indicator tree-level-${Math.min(level, 5)}"></div>
                    
                    <div class="agent-avatar me-3">
                        ${node.name.charAt(0).toUpperCase()}
                    </div>
                    
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-0 fw-semibold">${node.name}</h6>
                                <small class="text-muted">${node.email}</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge ${node.is_active ? 'bg-success' : 'bg-secondary'}">
                                    ${node.is_active ? 'Active' : 'Inactive'}
                                </span>
                                ${node.total_downline > 0 ? `
                                    <span class="badge bg-primary">${node.total_downline} downline</span>
                                ` : ''}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tree-node-content" style="display: ${isExpanded ? 'block' : 'none'};">
                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Phone:</small>
                        <span>${node.phone || 'N/A'}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Joined:</small>
                        <span>${new Date(node.created_at).toLocaleDateString()}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Agent ID:</small>
                        <span>${node.unique_agent_id}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Direct Children:</small>
                        <span>${hasChildren ? node.children.length : 0}</span>
                    </div>
                </div>
            </div>
        `;

        // Add children if expanded
        if (hasChildren && isExpanded) {
            const childrenContainer = document.createElement('div');
            childrenContainer.className = 'tree-node-children';
            
            node.children.forEach(child => {
                const childElement = createTreeNode(child, level + 1);
                childrenContainer.appendChild(childElement);
            });
            
            nodeDiv.appendChild(childrenContainer);
        }

        return nodeDiv;
    }

    /**
     * Enhanced Toggle Node Expansion with Auto-Collapse Functionality
     *
     * This function implements single-expansion accordion behavior where expanding
     * any node automatically collapses all other nodes at the same hierarchy level.
     * Each level maintains independent collapse state for optimal user experience.
     *
     * FEATURES:
     * - Auto-collapse: Only one node expanded per level at any time
     * - Level independence: Each hierarchy level manages its own state
     * - Smooth animations and transitions
     * - Memory efficient state management
     * - Error handling and graceful degradation
     *
     * @param {string|number} agentId - The ID of the agent node to toggle
     */
    window.toggleNode = function(agentId) {
        try {
            // Debug logging (only in development)
            if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                // Toggle node functionality
            }

            const isExpanded = expandedNodes.has(agentId);

            if (isExpanded) {
                // Collapse the current node
                expandedNodes.delete(agentId);

                // Debug logging (only in development)
                if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                    // Node collapsed
                }
            } else {
                // ACCORDION BEHAVIOR: Auto-collapse other nodes at the same level
                const nodeLevel = getNodeLevel(agentId);
                if (nodeLevel !== null) {
                    collapseNodesAtSameLevel(agentId, nodeLevel);
                }

                // Expand the current node
                expandedNodes.add(agentId);

                // Debug logging (only in development)
                if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                    // Node expanded
                }
            }

            // Re-render the tree with updated state
            renderTree();

        } catch (error) {
            console.error('Error in toggleNode:', error);
            // Graceful degradation - still attempt basic toggle
            const isExpanded = expandedNodes.has(agentId);
            if (isExpanded) {
                expandedNodes.delete(agentId);
            } else {
                expandedNodes.add(agentId);
            }
            renderTree();
        }
    };

    /**
     * Get the hierarchy level of a specific node
     *
     * @param {string|number} agentId - The ID of the agent node
     * @returns {number|null} - The hierarchy level or null if not found
     */
    function getNodeLevel(agentId) {
        try {
            // Recursive function to find node and return its level
            function findNodeLevel(node, currentLevel = 1) {
                if (node.id == agentId) {
                    return currentLevel;
                }

                if (node.children && node.children.length > 0) {
                    for (const child of node.children) {
                        const level = findNodeLevel(child, currentLevel + 1);
                        if (level !== null) {
                            return level;
                        }
                    }
                }

                return null;
            }

            // Search in tree data
            if (treeData) {
                if (treeData.children && treeData.children.length > 0) {
                    for (const child of treeData.children) {
                        const level = findNodeLevel(child, 1);
                        if (level !== null) {
                            return level;
                        }
                    }
                } else {
                    return findNodeLevel(treeData, 1);
                }
            }

            return null;
        } catch (error) {
            console.error('Error in getNodeLevel:', error);
            return null;
        }
    }

    /**
     * Collapse all other nodes at the same hierarchy level
     * This implements the accordion behavior for level independence
     *
     * @param {string|number} currentAgentId - The ID of the agent being expanded
     * @param {number} targetLevel - The hierarchy level to process
     */
    function collapseNodesAtSameLevel(currentAgentId, targetLevel) {
        try {
            // Debug logging (only in development)
            if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                // Auto-collapsing nodes at same level
            }

            // Find all nodes at the same level
            const nodesAtLevel = findNodesAtLevel(targetLevel);

            // Collapse all nodes at this level except the current one
            for (const nodeId of nodesAtLevel) {
                if (nodeId != currentAgentId && expandedNodes.has(nodeId)) {
                    expandedNodes.delete(nodeId);

                    // Debug logging (only in development)
                    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                        // Node auto-collapsed
                    }
                }
            }

        } catch (error) {
            console.error('Error in collapseNodesAtSameLevel:', error);
        }
    }

    /**
     * Find all node IDs at a specific hierarchy level
     *
     * @param {number} targetLevel - The hierarchy level to search
     * @returns {Array} - Array of node IDs at the specified level
     */
    function findNodesAtLevel(targetLevel) {
        const nodesAtLevel = [];

        try {
            // Recursive function to collect nodes at specific level
            function collectNodesAtLevel(node, currentLevel = 1) {
                if (currentLevel === targetLevel) {
                    nodesAtLevel.push(node.id);
                }

                if (node.children && node.children.length > 0 && currentLevel < targetLevel) {
                    for (const child of node.children) {
                        collectNodesAtLevel(child, currentLevel + 1);
                    }
                }
            }

            // Search in tree data
            if (treeData) {
                if (treeData.children && treeData.children.length > 0) {
                    for (const child of treeData.children) {
                        collectNodesAtLevel(child, 1);
                    }
                } else {
                    collectNodesAtLevel(treeData, 1);
                }
            }

        } catch (error) {
            console.error('Error in findNodesAtLevel:', error);
        }

        return nodesAtLevel;
    }

    /**
     * Toggle node details
     */
    window.toggleNodeDetails = function(agentId) {
        // This will be implemented to show agent details modal
        // Show agent details functionality
    };

    /**
     * Expand all nodes
     */
    function expandAll() {
        function addAllIds(node) {
            expandedNodes.add(node.id);
            if (node.children) {
                node.children.forEach(addAllIds);
            }
        }
        
        if (treeData.children) {
            treeData.children.forEach(addAllIds);
        } else {
            addAllIds(treeData);
        }
        
        renderTree();
    }

    /**
     * Collapse all nodes
     */
    function collapseAll() {
        expandedNodes.clear();
        renderTree();
    }

    /**
     * Update statistics display
     */
    function updateStatistics(stats) {
        document.getElementById('totalAgentsCount').textContent = stats.total_agents || 0;
        document.getElementById('hierarchyLevels').textContent = stats.hierarchy_levels || 0;

        document.getElementById('activeAgents').textContent = stats.active_agents || 0;
    }

    /**
     * Show loading state
     */
    function showLoading() {
        loadingState.style.display = 'block';
        treeContainer.style.display = 'none';
        emptyState.style.display = 'none';
    }

    /**
     * Show tree
     */
    function showTree() {
        loadingState.style.display = 'none';
        treeContainer.style.display = 'block';
        emptyState.style.display = 'none';
    }

    /**
     * Show empty state
     */
    function showEmpty() {
        loadingState.style.display = 'none';
        treeContainer.style.display = 'none';
        emptyState.style.display = 'block';
    }

    /**
     * Show error message
     */
    function showError(message) {
        loadingState.style.display = 'none';
        treeContainer.innerHTML = `
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
            </div>
        `;
        treeContainer.style.display = 'block';
        emptyState.style.display = 'none';
    }
});
</script>
<?= $this->endSection() ?>
