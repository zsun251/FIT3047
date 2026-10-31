<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * VitalsRemindersFixture
 */
class VitalsRemindersFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'vitals_reminders';

    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            ['id' => 1, 'reminder_id' => 1, 'patient_id' => 1],
            ['id' => 2, 'reminder_id' => 2, 'patient_id' => 2],
        ];
        parent::init();
    }
}
