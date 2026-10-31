<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ReminderSettingsFixture
 */
class ReminderSettingsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        // Rows are created by a database trigger when a patient is inserted.
        $this->records = [];
        parent::init();
    }
}
