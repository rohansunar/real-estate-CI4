<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
  /* Subtle Bootstrap-like card styling for OrgChart nodes */
  #treeContainer .node { border-radius: .5rem; box-shadow: 0 .25rem .75rem rgba(0,0,0,.05); }
  #treeContainer .node:hover { transform: translateY(-2px); transition: transform .2s ease; box-shadow: 0 .5rem 1rem rgba(0,0,0,.15); }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4">
  <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
    <div>
      <h1 class="h3 fw-bold mb-1">MLM Hierarchy</h1>
      <p class="text-muted mb-0">Interactive tree with level analytics</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2">
      <a class="btn btn-outline-primary" id="btnBackToRoot"><i class="fa fa-home me-1"></i>Back to Root</a>
      <a class="btn btn-outline-secondary" id="btnJumpParent"><i class="fa fa-level-up-alt me-1"></i>Jump to Parent</a>
      <a class="btn btn-outline-dark" id="btnPrint"><i class="fa fa-print me-1"></i>Print Tree</a>
      <a class="btn btn-success" id="btnExportCsv"><i class="fa fa-file-csv me-1"></i>Export CSV</a>
    </div>
  </div>

  <!-- Search -->
  <div class="card mb-3">
    <div class="card-body">
      <div class="row g-2 align-items-center">
        <div class="col-12 col-md-6">
          <div class="input-group">
            <span class="input-group-text bg-light"><i class="fa fa-search"></i></span>
            <input type="text" class="form-control" id="searchInput" placeholder="Search by name or ID...">
          </div>
        </div>
        <div class="col-auto">
          <div class="btn-group" role="group">
            <input type="radio" class="btn-check" name="searchType" id="searchByName" value="name" checked>
            <label class="btn btn-outline-primary" for="searchByName">Name</label>
            <input type="radio" class="btn-check" name="searchType" id="searchById" value="id">
            <label class="btn btn-outline-primary" for="searchById">ID</label>
          </div>
        </div>
        <div class="col-12 col-md-5 text-md-end">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" id="breadcrumbTrail"></ol>
          </nav>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-7">
      <div class="card">
        <div class="card-header bg-white">
          <div class="d-flex align-items-center justify-content-between">
            <h5 class="mb-0"><i class="fa fa-sitemap text-primary me-2"></i>Hierarchy Tree</h5>
            <div class="d-flex align-items-center gap-2">
              <label class="text-muted small">Depth</label>
              <select class="form-select form-select-sm" style="width:auto" id="depthSelect">
                <option value="2">2</option>
                <option value="3" selected>3</option>
                <option value="4">4</option>
                <option value="5">5</option>
              </select>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div id="treeContainer" style="width:100%; min-height:400px; overflow:auto;"></div>
          <!-- Member Details Modal -->
          <div class="modal fade" id="memberModal" tabindex="-1" aria-labelledby="memberModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="memberModalLabel">Member Details</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div id="memberDetails" class="small"></div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary" id="modalJumpBtn"><i class="fa fa-level-up-alt me-1"></i>Jump to Parent</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="card">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
          <h5 class="mb-0"><i class="fa fa-layer-group text-success me-2"></i>Level Summary</h5>
          <div class="d-flex align-items-center gap-2">
            <select class="form-select form-select-sm" id="orderBy">
              <option value="level" selected>Level</option>
              <option value="total_count">Total</option>
              <option value="active_percent">Active %</option>
            </select>
            <select class="form-select form-select-sm" id="orderDir">
              <option value="ASC">Asc</option>
              <option value="DESC">Desc</option>
            </select>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0" id="levelsTable">
            <thead class="table-light">
              <tr>
                <th>Level</th>
                <th>Total</th>
                <th>Active</th>
                <th>Active %</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
        <div class="card-body border-top py-2">
          <nav>
            <ul class="pagination pagination-sm justify-content-end mb-0" id="levelsPagination"></ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- OrgChart.js CDN (lightweight alternative) -->
<script src="https://balkangraph.com/js/latest/OrgChart.js"></script>
<script>
(function(){
  const rootId = <?= json_encode($rootId) ?>;
  let currentRoot = rootId;
  let currentPath = [];
  let chart = null;              // OrgChart instance
  let seenIds = new Set();       // Track nodes already rendered
  let loadedParents = new Set(); // Track parents whose children were lazy-loaded
  let parentChildren = new Map();// Map<parentId, Set<childId>> for collapse
  const depthSelect = document.getElementById('depthSelect');
  const treeEl = document.getElementById('treeContainer');
  const breadcrumbEl = document.getElementById('breadcrumbTrail');

  function badge(active){
    const cls = active ? 'bg-success' : 'bg-secondary';
    const txt = active ? 'Active' : 'Inactive';
    return `<span class="badge ${cls} ms-1">${txt}</span>`;
  }

  function nodeNameHtml(n){
    const expandable = !!(n.has_children && (!n.children || n.children.length === 0));
    const controls = expandable ? `
      <span class="ms-2">
        <a href="#" class="node-expand text-primary" data-id="${n.id}" aria-label="Expand"><i class="fa fa-plus-square"></i></a>
        <a href="#" class="node-collapse text-secondary d-none" data-id="${n.id}" aria-label="Collapse"><i class="fa fa-minus-square"></i></a>
      </span>` : '';
    return `${n.name} ${badge(n.is_active)} ${controls}`;
  }

  function toNodes(data){
    // Flatten nested tree to OrgChart nodes array
    const out = [];
    function walk(node, parent){
      // Add expand control if node may have children that are not yet loaded
      const expandable = node.has_children && (!node.children || node.children.length === 0);
      out.push({
        id: node.id,
        pid: parent || null,
        name: nodeNameHtml(node),
        title: `L${node.level} • Joined ${node.join_date || ''}`,
        tags: [...(node.is_active ? ["active-node"] : ["inactive-node"]), ...(expandable ? ["expandable"] : [])],
      });
      (node.children||[]).forEach(ch => walk(ch, node.id));
    }
    walk(data, null);
    return out;
  }

  function renderBreadcrumb(path){
    breadcrumbEl.innerHTML = '';
    path.forEach((p, idx) => {
      const li = document.createElement('li');
      li.className = 'breadcrumb-item'+(idx===path.length-1?' active':'');
      if (idx === path.length-1) li.textContent = p.name; else {
        const a = document.createElement('a');
        a.href = '#';
        a.textContent = p.name;
        a.addEventListener('click', (e)=>{ e.preventDefault(); setRoot(p.id); });
        li.appendChild(a);
      }
      breadcrumbEl.appendChild(li);
    });
  }

  async function fetchPath(id){
    const res = await fetch(`<?= base_url('mlm/search') ?>?query=${id}&type=id`);
    const json = await res.json();
    return json.path || [];
  }

  async function setRoot(id){
    currentRoot = id;
    currentPath = await fetchPath(id);
    renderBreadcrumb(currentPath);
    await loadTree();
    await loadLevels();
  }

  async function loadTree(){
    const depth = parseInt(depthSelect.value, 10) || 3;
    treeEl.innerHTML = `<div class='text-center py-5'><div class='spinner-border text-primary'></div><div class='mt-2 text-muted small'>Loading tree...</div></div>`;
    const url = `<?= base_url('mlm/tree') ?>/${currentRoot}?depth=${depth}`;
    const res = await fetch(url);
    const json = await res.json();
    if (json.status !== 'ok') { treeEl.innerHTML = `<div class='text-danger'>${json.message||'Failed to load tree'}</div>`; return; }
    const nodes = toNodes(json.data);
    treeEl.innerHTML = '';
    chart = new OrgChart(treeEl, {
      template: 'ana',
      nodeMouseClick: OrgChart.action.none,
      enableSearch: false,
      scaleInitial: OrgChart.match.boundary,
      nodeMenu: {
        details:  { text: "Details",  onClick: async (id) => { await openMemberDetails(id); } },
        expand:   { text: "Expand",   onClick: async (id) => { await toggleExpandCollapse(id, true); } },
        collapse: { text: "Collapse", onClick: async (id) => { await toggleExpandCollapse(id, false); } }
      },
      nodeBinding: {
        field_0: 'name',
        field_1: 'title'
      },
      tags: {
        expandable: { menu: { details: true, expand: true, collapse: true } }
      },
      nodes
    });

    // Track nodes seen in the chart (for lazy add)
    seenIds.clear();
    nodes.forEach(n => seenIds.add(n.id));
    loadedParents.clear();

    chart.on('click', function(sender, args){
      const id = args.node.id;
      // Click on node background -> drill down
      if (id && id !== currentRoot) setRoot(id);
      return false;
    });

    // Delegate clicks for expand/collapse controls within node HTML
    treeEl.addEventListener('click', async (e) => {
      const a = e.target.closest('a.node-expand, a.node-collapse');
      if (!a) return;
      e.preventDefault();
      const id = parseInt(a.getAttribute('data-id'), 10);
      if (a.classList.contains('node-expand')) {
        await toggleExpandCollapse(id, true);
        // toggle icons
        a.classList.add('d-none');
        const collapseEl = treeEl.querySelector(`a.node-collapse[data-id="${id}"]`);
        collapseEl?.classList.remove('d-none');
      } else if (a.classList.contains('node-collapse')) {
        await toggleExpandCollapse(id, false);
        a.classList.add('d-none');
        const expandEl = treeEl.querySelector(`a.node-expand[data-id="${id}"]`);
        expandEl?.classList.remove('d-none');
      }
    });
  }

  async function toggleExpandCollapse(id, expand = true){
    if (expand) {
      // Expand: lazy-load if not already loaded
      if (!loadedParents.has(id)){
        const res = await fetch(`<?= base_url('mlm/tree') ?>/${id}?depth=2`);
        const json = await res.json();
        if (json.status !== 'ok') return;
        const subtree = json.data;
        const toFlat = [];
        const childrenSet = parentChildren.get(id) || new Set();
        (function walk(n, parent){
          if (n.id !== id) {
            toFlat.push({ id:n.id, pid:parent, name:`${n.name} ${badge(n.is_active)}`, title:`L${n.level} • Joined ${n.join_date || ''}`, tags:n.is_active?["active-node"]:["inactive-node"] });
            if (parent === id) childrenSet.add(n.id);
          }
          (n.children||[]).forEach(ch => walk(ch, n.id));
        })(subtree, id);
        parentChildren.set(id, childrenSet);
        if (!chart) return;
        for (const node of toFlat){ if (!seenIds.has(node.id)){ chart.addNode(node); seenIds.add(node.id); } }
        loadedParents.add(id);
      }
    } else {
      // Collapse: remove only the previously added direct children (and their descendants)
      const childrenSet = parentChildren.get(id);
      if (!childrenSet || !chart) return;

      // Recursively collect all descendants of each child to remove
      const toRemove = new Set();
      function collectDescendants(pid){
        toRemove.add(pid);
        // OrgChart API: getNode(id) -> node with childrenIds
        const node = chart.getNode(pid);
        const kids = node?.childrenIds || [];
        kids.forEach(k => collectDescendants(k));
      }
      childrenSet.forEach(childId => collectDescendants(childId));

      toRemove.forEach(nid => { chart.removeNode(nid); seenIds.delete(nid); });
      loadedParents.delete(id);
      parentChildren.delete(id);
    }
  }

  async function loadLevels(page=1){
    const orderBy = document.getElementById('orderBy').value;
    const orderDir = document.getElementById('orderDir').value;
    const url = `<?= base_url('mlm/levels-summary') ?>?page=${page}&limit=10&memberId=${currentRoot}&orderBy=${orderBy}&orderDir=${orderDir}`;
    const res = await fetch(url);
    const json = await res.json();
    if (json.status !== 'ok') return;
    const tbody = document.querySelector('#levelsTable tbody');
    tbody.innerHTML = '';
    (json.data||[]).forEach(r => {
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>L${r.level}</td><td>${r.total_count}</td><td>${r.active_count}</td><td>${r.active_percent}%</td>`;
      tbody.appendChild(tr);
    });
    // pagination
    const pag = document.getElementById('levelsPagination');
    pag.innerHTML = '';
    const totalPages = json.pagination.total_pages || 1;
    for(let i=1;i<=totalPages;i++){
      const li = document.createElement('li');
      li.className = 'page-item ' + (i===json.pagination.page?'active':'');
      const a = document.createElement('a');
      a.className = 'page-link'; a.href = '#'; a.textContent = i;
      a.addEventListener('click', (e)=>{ e.preventDefault(); loadLevels(i); });
      li.appendChild(a); pag.appendChild(li);
    }
  }

  // Search
  let debounceTimer;
  document.getElementById('searchInput').addEventListener('input', function(){
    clearTimeout(debounceTimer);
    const q = this.value.trim();
    const type = document.querySelector('input[name="searchType"]:checked').value;
    debounceTimer = setTimeout(async ()=>{
      if (!q) return; 
      const res = await fetch(`<?= base_url('mlm/search') ?>?query=${encodeURIComponent(q)}&type=${type}`);
      const json = await res.json();
      if ((json.results||[]).length){ setRoot(json.results[0].id); }
    }, 350);
  });

  async function openMemberDetails(id){
    const m = await fetch(`<?= base_url('mlm/member') ?>/${id}/details`).then(r=>r.json());
    if (m.status !== 'ok') return;
    const d = m.data;
    const html = `
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="fw-semibold">${d.name} ${d.is_active ? '<span class="badge bg-success ms-1">Active</span>' : '<span class="badge bg-secondary ms-1">Inactive</span>'}</div>
          <div class="text-muted small">ID: ${d.unique_agent_id || ''}</div>
        </div>
        <button class="btn btn-sm btn-outline-primary" id="modalGoBtn"><i class="fa fa-location-arrow me-1"></i>Go Here</button>
      </div>
      <hr>
      <div class="row small g-2">
        <div class="col-6"><span class="text-muted">Joined:</span> ${d.created_at ? new Date(d.created_at).toLocaleDateString('en-US') : ''}</div>
        <div class="col-6"><span class="text-muted">Phone:</span> ${d.phone || ''}</div>
        <div class="col-12"><span class="text-muted">Email:</span> ${d.email || ''}</div>
        <div class="col-12"><span class="text-muted">Address:</span> ${d.address || ''}</div>
        <div class="col-12"><span class="text-muted">Qualification:</span> ${d.qualification || ''}</div>
      </div>
    `;
    document.getElementById('memberDetails').innerHTML = html;
    const modalEl = document.getElementById('memberModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    document.getElementById('modalGoBtn')?.addEventListener('click', (e)=>{ e.preventDefault(); modal.hide(); setRoot(d.id); });
    document.getElementById('modalJumpBtn')?.addEventListener('click', (e)=>{ e.preventDefault(); modal.hide(); if (d.parent_agent_id) setRoot(d.parent_agent_id); });
  }

  // Buttons
  document.getElementById('btnBackToRoot').addEventListener('click', (e)=>{ e.preventDefault(); setRoot(rootId); });
  document.getElementById('btnJumpParent').addEventListener('click', async (e)=>{
    e.preventDefault();
    const details = await fetch(`<?= base_url('mlm/member') ?>/${currentRoot}/details`).then(r=>r.json());
    if (details.status==='ok' && details.data.parent_agent_id) setRoot(details.data.parent_agent_id);
  });
  document.getElementById('btnExportCsv').addEventListener('click', (e)=>{
    e.preventDefault();
    window.location = `<?= base_url('mlm/levels-summary.csv') ?>?memberId=${currentRoot}`;
  });

  document.getElementById('btnPrint').addEventListener('click', (e)=>{
    e.preventDefault();
    window.print();
  });

  depthSelect.addEventListener('change', ()=> loadTree());
  document.getElementById('orderBy').addEventListener('change', ()=> loadLevels());
  document.getElementById('orderDir').addEventListener('change', ()=> loadLevels());

  // Initial load
  if (rootId) setRoot(rootId);
})();
</script>
<?= $this->endSection() ?>

