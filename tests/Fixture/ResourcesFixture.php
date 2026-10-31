<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ResourcesFixture
 */
class ResourcesFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'clinician_id' => 1,
                'title' => 'Using an inhaler',
                'type' => 'article',
                'body' => 'How to use an inhaler.',
                'url' => null,
                'thumbnail_url' => null,
                'status' => 'published',
                'published_at' => '2026-03-21 03:31:21',
                'created_at' => '2026-03-21 03:31:21',
                'updated_at' => '2026-03-21 03:31:21',
            ],
        ];
        parent::init();
    }
}
