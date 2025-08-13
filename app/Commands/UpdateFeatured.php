<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class UpdateFeatured extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Database';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'update:featured';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Update some properties as featured for testing';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'update:featured';

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
        $propertyModel = new \App\Models\PropertyModel();

        // Update some properties as featured
        $propertyModel->update(1, ['is_featured' => true]); // Villa
        $propertyModel->update(2, ['is_featured' => true]); // Apartment
        $propertyModel->update(4, ['is_featured' => true]); // House
        $propertyModel->update(6, ['is_featured' => true]); // House

        CLI::write('Updated properties as featured successfully!', 'green');
        CLI::write('Featured properties:', 'yellow');

        $featured = $propertyModel->where('is_featured', true)->findAll();
        foreach ($featured as $property) {
            CLI::write("- {$property['title']} ({$property['type']})", 'white');
        }
    }
}
