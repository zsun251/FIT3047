<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\I18n\FrozenTime;
use Cake\TestSuite\Fixture\TestFixture;

/**
 * RemindersFixture
 */
class RemindersFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'reminders';

    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        // Dated today, because the reminders page only lists today's reminders
        $today = FrozenTime::now()->startOfDay()->format('Y-m-d H:i:s');
        $this->records = [
            [
                'id' => 1,
                'message' => 'Patient one reminder',
                'scheduled_at' => $today,
                'status' => 'sent',
                'delivery_method' => 'system',
            ],
            [
                'id' => 2,
                'message' => 'Patient two reminder',
                'scheduled_at' => $today,
                'status' => 'sent',
                'delivery_method' => 'system',
            ],
        ];
        parent::init();
    }
}
