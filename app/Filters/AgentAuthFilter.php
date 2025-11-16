<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AgentAuthFilter
 *
 * Authentication filter for agent-specific routes.
 * Ensures only authenticated agents can access agent dashboard and related functionality.
 *
 * Features:
 * - Separate from admin authentication
 * - Agent session validation
 * - Redirect to agent login page
 * - Store intended URL for post-login redirect
 *
 * @author Gold Properties Team
 * @version 1.0
 * @since 2025-08-04
 */
class AgentAuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if agent is logged in
        if (!session()->get('agent_logged_in')) {
            // Store the intended URL for redirect after login
            session()->set('agent_redirect_url', current_url());
            
            // Redirect to agent login page
            return redirect()->to('/agent/login')->with('error', 'Please login to access the agent dashboard.');
        }

        // Check if agent account is still active (optional security check)
        $agentId = session()->get('agent_id');
        if ($agentId) {
            $agentModel = new \App\Models\AgentModel();
            $agent = $agentModel->find($agentId);
            
            if (!$agent || !$agent['is_active']) {
                // Agent account is inactive, clear session and redirect
                session()->remove(['agent_id', 'agent_name', 'agent_email', 'agent_unique_id', 'agent_parent_id', 'agent_logged_in']);
                return redirect()->to('/agent/login')->with('error', 'Your agent account has been deactivated. Please contact the administrator.');
            }
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
