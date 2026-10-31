<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ContactMessagesFixture
 */
class ContactMessagesFixture extends TestFixture
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
                'first_name' => 'Casey',
                'last_name' => 'Jones',
                'contact_number' => '0400000000',
                'email' => 'casey@example.com',
                'message' => 'I need help with my account.',
                'status' => 'unread',
                'submitted_at' => '2026-03-21 15:46:22',
                'reviewed_by' => null,
                'reviewed_at' => null,
            ],
        ];
        parent::init();
    }
}
