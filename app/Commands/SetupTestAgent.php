<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\AgentModel;

class SetupTestAgent extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'setup:testagent';
    protected $description = 'Sets up a test agent with login credentials';

    public function run(array $params)
    {
        $agentModel = new AgentModel();
        
        // Find the first agent and set a password
        $agent = $agentModel->where('email', 'rajesh@realestate.com')->first();
        
        if ($agent) {
            // Update the agent with a password
            $result = $agentModel->update($agent['id'], ['password' => 'agent123']);
            
            if ($result) {
                CLI::write('✅ Password set successfully for agent: ' . $agent['name'], 'green');
                CLI::write('📧 Email: ' . $agent['email'], 'yellow');
                CLI::write('🔑 Password: agent123', 'yellow');
                CLI::write('🆔 Unique ID: ' . $agent['unique_agent_id'], 'yellow');
                CLI::write('');
                CLI::write('You can now login at: http://localhost:8081/agent/login', 'cyan');
            } else {
                CLI::error('❌ Failed to set password');
            }
        } else {
            CLI::error('❌ Agent not found');
        }
    }
}
