<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\ImageManagementService;

/**
 * Test the ImageManagementService functionality
 */
class ImageManagementTest extends CIUnitTestCase
{
    protected $imageService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->imageService = new ImageManagementService();
    }

    public function testServiceInstantiation()
    {
        $this->assertInstanceOf(ImageManagementService::class, $this->imageService);
    }

    public function testDeleteNonExistentImage()
    {
        $result = $this->imageService->deleteImage('non-existent-image.jpg');
        
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('not found', $result['message']);
    }

    public function testDeleteEmptyImagePath()
    {
        $result = $this->imageService->deleteImage('');
        
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('No image to delete', $result['message']);
    }

    public function testDeleteMultipleImagesEmpty()
    {
        $result = $this->imageService->deleteMultipleImages([]);
        
        $this->assertTrue($result['success']);
        $this->assertEquals(0, $result['deleted_count']);
        $this->assertEquals(0, $result['failed_count']);
    }

    public function testDeletePropertyImagesEmpty()
    {
        $result = $this->imageService->deletePropertyImages([]);
        
        $this->assertTrue($result['success']);
        $this->assertEquals(0, $result['deleted_count']);
        $this->assertEquals(0, $result['failed_count']);
    }
}
