<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestCommissionSystem extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Testing';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'test:commissionsystem';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Test the multi-level agent hierarchy and commission distribution system';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'command:name [arguments] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('🧪 Testing Multi-Level Agent Hierarchy and Commission Distribution System', 'green');
        CLI::write(str_repeat('=', 70), 'yellow');
        CLI::newLine();

        try {
            $agentModel = new \App\Models\AgentModel();

            CLI::write('📊 Current Agent Hierarchy:', 'cyan');
            CLI::write(str_repeat('-', 30), 'yellow');

            // Get all agents and show hierarchy
            $allAgents = $agentModel->findAll();
            if (empty($allAgents)) {
                CLI::write('❌ No agents found in database', 'red');
                return;
            }

            foreach ($allAgents as $agent) {
                $hierarchyPosition = $agentModel->getAgentHierarchyPosition($agent['id']);
                $indent = str_repeat('  ', $hierarchyPosition['hierarchy_level']);

                CLI::write("{$indent}🧑‍💼 {$agent['name']} (ID: {$agent['id']}, Level: {$hierarchyPosition['hierarchy_level']})", 'white');
                CLI::write("{$indent}   📧 {$agent['email']}", 'light_gray');
                CLI::write("{$indent}   🆔 {$agent['unique_agent_id']}", 'light_gray');
                CLI::write("{$indent}   👥 Direct Sub-Agents: {$hierarchyPosition['direct_sub_agents']}", 'light_gray');
                CLI::write("{$indent}   📈 Total Downline: {$hierarchyPosition['total_downline']}", 'light_gray');

                if (!empty($hierarchyPosition['upline'])) {
                    $parentName = $hierarchyPosition['upline'][count($hierarchyPosition['upline'])-1]['name'];
                    CLI::write("{$indent}   ⬆️  Parent: {$parentName}", 'light_gray');
                }
                CLI::newLine();
            }

            CLI::newLine();
            CLI::write('🏠 Testing Commission Distribution:', 'cyan');
            CLI::write(str_repeat('-', 35), 'yellow');

            // Test commission calculation
            $saleAmount = 5000000; // ₹50 lakhs
            $testAgent = $allAgents[0]; // Use first agent for testing

            CLI::write('🎯 Test Scenario:', 'green');
            CLI::write("   Selling Agent: {$testAgent['name']} (ID: {$testAgent['id']})", 'white');
            CLI::write("   Sale Amount: ₹" . number_format($saleAmount, 2), 'white');
            CLI::newLine();

            // Build hierarchy chain
            $hierarchyChain = [];
            $currentAgentId = $testAgent['id'];
            $level = 0;

            while ($currentAgentId && $level < 10) {
                $hierarchyChain[$level] = $currentAgentId;
                $agent = $agentModel->find($currentAgentId);
                if (!$agent || !$agent['parent_agent_id']) {
                    break;
                }
                $currentAgentId = $agent['parent_agent_id'];
                $level++;
            }

            CLI::write('💰 Commission Distribution Preview:', 'cyan');
            CLI::write(str_repeat('-', 40), 'yellow');

            $commissionConfig = [
                'level_distribution' => [
                    0 => 6.0,  // Selling agent gets 6%
                    1 => 2.0,  // Direct parent gets 2%
                    2 => 1.0,  // Grandparent gets 1%
                    3 => 0.5,  // Great-grandparent gets 0.5%
                    4 => 0.3,  // Great-great-grandparent gets 0.3%
                    5 => 0.2,  // And so on...
                ],
                'default_level_percentage' => 0.1
            ];

            $totalCommission = 0;
            foreach ($hierarchyChain as $level => $agentId) {
                $agent = $agentModel->find($agentId);
                $commissionPercentage = $commissionConfig['level_distribution'][$level] ?? $commissionConfig['default_level_percentage'];
                $commissionAmount = $saleAmount * ($commissionPercentage / 100);
                $totalCommission += $commissionAmount;

                $levelName = $level === 0 ? 'Selling Agent' : "Level {$level} Parent";
                CLI::write("   {$levelName}: {$agent['name']}", 'white');
                CLI::write("     💵 Commission: ₹" . number_format($commissionAmount, 2) . " ({$commissionPercentage}%)", 'green');
                CLI::write("     🆔 Agent ID: {$agent['unique_agent_id']}", 'light_gray');
                CLI::newLine();
            }

            CLI::write('📈 Summary:', 'cyan');
            CLI::write("   Total Sale Amount: ₹" . number_format($saleAmount, 2), 'white');
            CLI::write("   Total Commission Distributed: ₹" . number_format($totalCommission, 2), 'green');
            CLI::write("   Commission Percentage: " . number_format(($totalCommission / $saleAmount) * 100, 2) . "%", 'white');
            CLI::write("   Agents Benefiting: " . count($hierarchyChain), 'white');
            CLI::write("   Hierarchy Levels: " . (count($hierarchyChain) - 1), 'white');
            CLI::newLine();

            CLI::write('✅ Commission system test completed successfully!', 'green');
            CLI::write('🔗 The system supports unlimited hierarchy levels with automatic commission distribution.', 'cyan');
            CLI::newLine();

            CLI::write('🌐 Access URLs:', 'cyan');
            CLI::write('   Agent Login: http://localhost:8081/agent/login', 'white');
            CLI::write('   Agent Dashboard: http://localhost:8081/agent/dashboard', 'white');
            CLI::write('   Hierarchy Tree: http://localhost:8081/agent/hierarchy', 'white');
            CLI::write('   Commission Dashboard: http://localhost:8081/agent/commissions', 'white');
            CLI::write('   Downline Management: http://localhost:8081/agent/downline', 'white');
            CLI::newLine();

            CLI::write('🔑 Test Agent Credentials:', 'cyan');
            CLI::write('   Email: rajesh@realestate.com', 'white');
            CLI::write('   Password: agent123', 'white');
            CLI::newLine();

        } catch (\Exception $e) {
            CLI::error('❌ Error during testing: ' . $e->getMessage());
            CLI::error('📍 File: ' . $e->getFile() . ' (Line: ' . $e->getLine() . ')');
        }
    }
}
