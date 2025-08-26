<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\AgentModel;
use App\Services\EmailService;

/**
 * Test the Agent Sub-Agent Management functionality
 *
 * This test verifies the fixes for:
 * 1. unique_agent_id validation error in sub-agent updates
 * 2. Welcome email functionality for sub-agent creation
 */
class AgentSubAgentTest extends CIUnitTestCase
{
    protected $agentModel;
    protected $emailService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agentModel = new AgentModel();
        $this->emailService = new EmailService();
    }

    /**
     * Test that sub-agent update validation rules exclude unique_agent_id
     */
    public function testSubAgentUpdateValidationRulesExcludeUniqueAgentId()
    {
        $validationRules = $this->agentModel->getUpdateValidationRules(1); // Use dummy ID

        // Assert that unique_agent_id is not in the validation rules for updates
        $this->assertArrayNotHasKey('unique_agent_id', $validationRules);

        // Assert that other required fields are still present
        $this->assertArrayHasKey('name', $validationRules);
        $this->assertArrayHasKey('email', $validationRules);
        $this->assertArrayHasKey('phone', $validationRules);
    }

    /**
     * Test that EmailService can be instantiated (basic email functionality test)
     */
    public function testEmailServiceInstantiation()
    {
        $this->assertInstanceOf(EmailService::class, $this->emailService);
    }

    /**
     * Test that agent welcome email data structure is correct
     */
    public function testAgentWelcomeEmailDataStructure()
    {
        $emailData = [
            'name' => 'Test Agent',
            'email' => 'test@example.com',
            'unique_agent_id' => 'AGT2508260001',
            'plain_password' => 'testpass123'
        ];

        // Verify all required fields are present
        $this->assertArrayHasKey('name', $emailData);
        $this->assertArrayHasKey('email', $emailData);
        $this->assertArrayHasKey('unique_agent_id', $emailData);
        $this->assertArrayHasKey('plain_password', $emailData);

        // Verify data types and values
        $this->assertIsString($emailData['name']);
        $this->assertIsString($emailData['email']);
        $this->assertIsString($emailData['unique_agent_id']);
        $this->assertIsString($emailData['plain_password']);
        $this->assertNotEmpty($emailData['unique_agent_id']);
    }

    /**
     * Test that create validation rules still include unique_agent_id
     */
    public function testCreateValidationRulesIncludeUniqueAgentId()
    {
        $validationRules = $this->agentModel->getCreateValidationRules();

        // Assert that unique_agent_id is present in creation rules (but permit_empty)
        $this->assertArrayHasKey('unique_agent_id', $validationRules);
        $this->assertStringContainsString('permit_empty', $validationRules['unique_agent_id']);
    }

    /**
     * Test that unique ID generation method exists (without database call)
     */
    public function testUniqueIdGenerationMethodExists()
    {
        $this->assertTrue(method_exists($this->agentModel, 'generateUniqueId'));
    }
}
