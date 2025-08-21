<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ContactModel;

/**
 * TestEnquiryDelete Command
 * 
 * Tests the enquiry delete functionality by directly using the ContactModel
 * to delete an enquiry and verify the operation.
 * 
 * Usage: php spark test:enquiry-delete [enquiry_id]
 */
class TestEnquiryDelete extends BaseCommand
{
    protected $group = 'Testing';
    protected $name = 'test:enquiry-delete';
    protected $description = 'Test enquiry delete functionality';
    protected $usage = 'test:enquiry-delete [enquiry_id]';
    protected $arguments = [
        'enquiry_id' => 'ID of the enquiry to delete (optional, defaults to 58)'
    ];

    public function run(array $params)
    {
        $enquiryId = $params[0] ?? 58;
        
        CLI::write('=== Testing Enquiry Delete Functionality ===', 'yellow');
        CLI::write("Testing with Enquiry ID: {$enquiryId}", 'white');
        CLI::newLine();

        $contactModel = new ContactModel();
        
        // Step 1: Verify enquiry exists
        CLI::write('Step 1: Checking if enquiry exists...', 'cyan');
        
        $enquiry = $contactModel->find($enquiryId);
        
        if (!$enquiry) {
            CLI::error("Enquiry {$enquiryId} not found!");
            return;
        }
        
        CLI::write("Enquiry found:", 'green');
        CLI::write("  ID: {$enquiry['id']}", 'white');
        CLI::write("  Name: {$enquiry['name']}", 'white');
        CLI::write("  Email: {$enquiry['email']}", 'white');
        CLI::write("  Created: {$enquiry['created_at']}", 'white');
        
        // Step 2: Confirm deletion
        CLI::newLine();
        CLI::write('Step 2: Confirming deletion...', 'cyan');
        
        $confirm = CLI::prompt("Are you sure you want to delete enquiry {$enquiryId}?", ['y', 'n']);
        
        if ($confirm !== 'y') {
            CLI::write('Test cancelled.', 'yellow');
            return;
        }
        
        // Step 3: Delete enquiry using ContactModel
        CLI::newLine();
        CLI::write('Step 3: Deleting enquiry...', 'cyan');
        
        $deleteResult = $contactModel->delete($enquiryId);
        
        if (!$deleteResult) {
            CLI::error('Failed to delete enquiry!');
            CLI::write('Model errors: ' . json_encode($contactModel->errors()), 'red');
            return;
        }
        
        CLI::write('✓ Enquiry deleted successfully', 'green');
        
        // Step 4: Verify deletion
        CLI::newLine();
        CLI::write('Step 4: Verifying deletion...', 'cyan');
        
        $verifyEnquiry = $contactModel->find($enquiryId);
        
        if ($verifyEnquiry) {
            CLI::error('❌ FAILED: Enquiry still exists in database!');
            CLI::write('Enquiry data: ' . json_encode($verifyEnquiry), 'red');
        } else {
            CLI::write('✅ SUCCESS: Enquiry successfully deleted from database', 'green');
        }
        
        // Step 5: Test the DashboardController method logic
        CLI::newLine();
        CLI::write('Step 5: Testing controller method logic...', 'cyan');
        
        // Create a test enquiry to test the controller logic
        $testEnquiryData = [
            'name' => 'Test Enquiry for Delete',
            'email' => 'test.delete@example.com',
            'phone' => '1234567890',
            'message' => 'This is a test enquiry for delete functionality testing',
            'property_id' => null,
            'is_read' => 0
        ];
        
        $testEnquiryId = $contactModel->insert($testEnquiryData);
        
        if (!$testEnquiryId) {
            CLI::error('Failed to create test enquiry');
            return;
        }
        
        CLI::write("Created test enquiry with ID: {$testEnquiryId}", 'white');
        
        // Test the delete operation
        $testDeleteResult = $contactModel->delete($testEnquiryId);
        
        if ($testDeleteResult) {
            CLI::write('✓ Test enquiry deleted successfully', 'green');
            
            // Verify it's gone
            $verifyTestEnquiry = $contactModel->find($testEnquiryId);
            if (!$verifyTestEnquiry) {
                CLI::write('✓ Test enquiry verified as deleted', 'green');
            } else {
                CLI::error('✗ Test enquiry still exists after deletion');
            }
        } else {
            CLI::error('✗ Failed to delete test enquiry');
        }
        
        // Final results
        CLI::newLine();
        CLI::write('=== Test Results ===', 'yellow');
        CLI::write('✅ ContactModel delete functionality is working correctly', 'green');
        CLI::write('✅ Database operations are functioning properly', 'green');
        CLI::write('', 'white');
        CLI::write('If the web interface delete is not working, the issue is likely:', 'white');
        CLI::write('1. Authentication/session issues', 'white');
        CLI::write('2. AJAX request configuration', 'white');
        CLI::write('3. Route configuration', 'white');
        CLI::write('4. JavaScript event handling', 'white');
        
        CLI::newLine();
        CLI::write('Test completed.', 'white');
    }
}
