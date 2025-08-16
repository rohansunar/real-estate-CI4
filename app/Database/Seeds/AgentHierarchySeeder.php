<?php

namespace App\Database\Seeds;

use App\Models\AgentModel;
use CodeIgniter\Database\Seeder;

/**
 * AgentHierarchySeeder
 *
 * Creates a realistic multi-branch agent hierarchy exactly 10 levels deep.
 * - 2 top-level agents (roots)
 * - Each level branches from 2–3 parents, each spawning 2–3 children
 * - All agents are active and have diverse addresses/qualifications
 * - Uses AgentModel so passwords are hashed and unique_agent_id is generated
 *
 * Usage:
 *   php spark db:seed AgentHierarchySeeder
 *
 * Default password for all seeded agents: agent123
 */
class AgentHierarchySeeder extends Seeder
{
    private AgentModel $agentModel;

    public function run()
    {
        $this->agentModel = new AgentModel();

        if (!$this->db->tableExists('agents')) {
            echo "Agents table does not exist. Please run migrations first.\n";
            return;
        }

        $this->db->transStart();

        // Pools for realistic data
        $locations = [
            'Siliguri, West Bengal, India',
            'Champasari, Siliguri, India',
            'Bagdogra, Darjeeling, India',
            'Jalpaiguri, West Bengal, India',
            'Pradhan Nagar, Siliguri, India',
            'Milan More, Siliguri, India',
            'Khaprail, Matigara, India',
        ];

        $qualifications = [
            'MBA in Real Estate',
            'BBA, Property Management',
            'RERA Certified Agent',
            'Diploma in Sales & Marketing',
            'Certified Property Consultant',
            'B.Com with Real Estate Experience',
            'Certified Negotiation Expert (CNE)'
        ];

        $rootNames = [
            'Amit Sen', 'Priya Das',
        ];

        $emailDomain = 'realestate.com';
        $created = 0;
        $allIds = [];

        // Create 2 top-level agents (no parent)
        $levelParents = [];
        $seq = 1; // used for unique generation
        foreach ($rootNames as $name) {
            $agentId = $this->createAgent([
                'name' => $name,
                'email' => $this->uniqueEmail($name, $emailDomain, $seq++),
                'phone' => $this->uniquePhone($seq),
                'address' => $locations[array_rand($locations)],
                'qualification' => $qualifications[array_rand($qualifications)],
                'is_active' => 1,
                'password' => 'agent123',
                'parent_agent_id' => null,
            ]);
            if ($agentId) {
                $created++;
                $allIds[] = $agentId;
                $levelParents[] = $agentId;
            }
        }

        // Levels: we already have level 1 as roots. We need to go 9 more levels to reach 10.
        $totalLevels = 10; // exact depth including roots at level 1
        for ($level = 2; $level <= $totalLevels; $level++) {
            $nextLevel = [];

            // Choose 2–3 parents from the previous level to expand to avoid exponential explosion
            $parentsToExpand = array_slice($levelParents, 0, min(count($levelParents), 2 + (($level % 2) === 0 ? 0 : 1)));

            foreach ($parentsToExpand as $pIndex => $parentId) {
                // Each chosen parent spawns 2–3 children
                $childrenCount = 2 + (($pIndex + $level) % 2); // 2 or 3

                for ($i = 0; $i < $childrenCount; $i++) {
                    $name = $this->randomIndianName($seq);
                    $agentId = $this->createAgent([
                        'name' => $name,
                        'email' => $this->uniqueEmail($name, $emailDomain, $seq++),
                        'phone' => $this->uniquePhone($seq),
                        'address' => $locations[array_rand($locations)],
                        'qualification' => $qualifications[array_rand($qualifications)],
                        'is_active' => 1,
                        'password' => 'agent123',
                        'parent_agent_id' => $parentId,
                    ]);
                    if ($agentId) {
                        $created++;
                        $allIds[] = $agentId;
                        $nextLevel[] = $agentId;
                    }
                }
            }

            // Also add 1–2 sibling branches attached to the first root to ensure breadth at deep levels
            if (!empty($levelParents)) {
                $extraBranches = 1 + ($level % 2); // 1 or 2
                $anchorParent = $levelParents[0];
                for ($e = 0; $e < $extraBranches; $e++) {
                    $name = $this->randomIndianName($seq);
                    $agentId = $this->createAgent([
                        'name' => $name,
                        'email' => $this->uniqueEmail($name, $emailDomain, $seq++),
                        'phone' => $this->uniquePhone($seq),
                        'address' => $locations[array_rand($locations)],
                        'qualification' => $qualifications[array_rand($qualifications)],
                        'is_active' => 1,
                        'password' => 'agent123',
                        'parent_agent_id' => $anchorParent,
                    ]);
                    if ($agentId) {
                        $created++;
                        $allIds[] = $agentId;
                        $nextLevel[] = $agentId;
                    }
                }
            }

            // Prepare for next level
            $levelParents = $nextLevel;

            // If somehow no parents were produced (edge case), break early
            if (empty($levelParents)) {
                break;
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            echo "❌ AgentHierarchySeeder failed. Transaction rolled back.\n";
            return;
        }

        echo "✅ AgentHierarchySeeder inserted {$created} agents across 10 levels.\n";
        echo "ℹ️  Default password for all seeded agents: agent123\n";
        echo "🔎 Sample top-level agents:\n";
        echo " - {$rootNames[0]} ({$rootNames[0]}@{$emailDomain})\n";
        echo " - {$rootNames[1]} ({$rootNames[1]}@{$emailDomain})\n";
    }

    /**
     * Creates an agent using the model (for hashing & unique ID generation).
     * Returns new agent ID on success, null on failure.
     */
    private function createAgent(array $data): ?int
    {
        try {
            $this->agentModel->skipValidation(true); // keep logic simple; inputs are controlled
            if ($this->agentModel->insert($data, true)) {
                return (int) $this->agentModel->getInsertID();
            }
        } catch (\Throwable $e) {
            // Continue seeding even if one insert fails; log for visibility
            log_message('error', 'Seeder failed to insert agent: ' . $e->getMessage());
        }
        return null;
    }

    private function slugify(string $str): string
    {
        $str = strtolower(trim($str));
        $str = preg_replace('/[^a-z0-9]+/i', '-', $str);
        return trim($str, '-');
    }

    private function uniqueEmail(string $name, string $domain, int $suffix): string
    {
        $base = $this->slugify($name);
        $email = $base . ".{$suffix}@{$domain}";
        // Ensure not already taken
        $attempt = 0;
        while ($this->agentModel->where('email', $email)->first()) {
            $attempt++;
            $email = $base . ".{$suffix}{$attempt}@{$domain}";
        }
        return $email;
    }

    private function uniquePhone(int $seed): string
    {
        // Generate Indian-style mobile numbers with +91 country code
        return '+91 ' . (string) (7000000000 + (($seed * 7919) % 1999999999));
    }

    private function refCode(int $seed): string
    {
        return 'REF' . date('ymd') . str_pad((string)(($seed * 97) % 10000), 4, '0', STR_PAD_LEFT);
    }

    private function randomIndianName(int $seed): string
    {
        $firstNames = ['Rahul', 'Sneha', 'Vikas', 'Kiran', 'Neha', 'Arjun', 'Rohit', 'Pooja', 'Alok', 'Simran', 'Deepak', 'Anita', 'Kunal', 'Isha', 'Tarun'];
        $lastNames  = ['Sharma', 'Verma', 'Gupta', 'Roy', 'Chakraborty', 'Bhattacharya', 'Singh', 'Das', 'Ghosh', 'Mukherjee', 'Agarwal', 'Banerjee'];
        $f = $firstNames[$seed % count($firstNames)];
        $l = $lastNames[$seed % count($lastNames)];
        return $f . ' ' . $l;
    }
}

