<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CliniciansFixture
 */
class CliniciansFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        // Rows are created by database triggers when UsersFixture is inserted.
        $this->records = [];
        parent::init();
    }
}
