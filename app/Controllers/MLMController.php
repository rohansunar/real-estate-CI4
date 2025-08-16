<?php

namespace App\Controllers;

use App\Models\MemberModel;

class MLMController extends BaseController
{
    protected MemberModel $members;

    public function __construct()
    {
        $this->members = new MemberModel();
    }

    /**
     * Main MLM page with tree + analytics
     * Route: GET /mlm
     */
    public function index()
    {
        // Choose a default root: first top-level agent (no parent)
        $root = model('AgentModel')
            ->groupStart()->where('parent_agent_id', null)->orWhere('parent_agent_id', 0)->groupEnd()
            ->orderBy('id', 'ASC')->first();

        $rootId = $root['id'] ?? null;

        $data = [
            'title' => 'MLM Hierarchy',
            'rootId' => $rootId,
            'initialDepth' => 3,
        ];
        return view('mlm/index', $data);
    }
}

