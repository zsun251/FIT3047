<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 */
class UsersFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        // Database triggers create the matching admins, clinicians and patients rows.
        // One bcrypt hash of 'Passw0rd!' shared by every test user.
        $hash = '$2y$12$CwBN98OmCdnNrAqCslVU0OrprCV0UKrQe8loiF/Midd/yLuB3RnMK';
        $this->records = [
            [
                'id' => 1,
                'username' => 'testadmin',
                'password' => $hash,
                'first_name' => 'Test',
                'last_name' => 'Admin',
                'email' => 'testadmin@example.com',
                'phone' => '0400000001',
                'address' => '1 Test Street',
                'date_of_birth' => '2000-01-01',
                'type' => 'admin',
                'created_at' => '2026-03-21 03:30:20',
            ],
            [
                'id' => 2,
                'username' => 'testclinician',
                'password' => $hash,
                'first_name' => 'Test',
                'last_name' => 'Clinician',
                'email' => 'testclinician@example.com',
                'phone' => '0400000002',
                'address' => '1 Test Street',
                'date_of_birth' => '2000-01-01',
                'type' => 'clinician',
                'created_at' => '2026-03-21 03:30:20',
            ],
            [
                'id' => 3,
                'username' => 'testpatient',
                'password' => $hash,
                'first_name' => 'Test',
                'last_name' => 'Patient',
                'email' => 'testpatient@example.com',
                'phone' => '0400000003',
                'address' => '1 Test Street',
                'date_of_birth' => '2000-01-01',
                'type' => 'patient',
                'created_at' => '2026-03-21 03:30:20',
            ],
            [
                'id' => 4,
                'username' => 'otherclinician',
                'password' => $hash,
                'first_name' => 'Test',
                'last_name' => 'Clinician',
                'email' => 'otherclinician@example.com',
                'phone' => '0400000004',
                'address' => '1 Test Street',
                'date_of_birth' => '2000-01-01',
                'type' => 'clinician',
                'created_at' => '2026-03-21 03:30:20',
            ],
            [
                'id' => 5,
                'username' => 'otherpatient',
                'password' => $hash,
                'first_name' => 'Test',
                'last_name' => 'Patient',
                'email' => 'otherpatient@example.com',
                'phone' => '0400000005',
                'address' => '1 Test Street',
                'date_of_birth' => '2000-01-01',
                'type' => 'patient',
                'created_at' => '2026-03-21 03:30:20',
            ],
        ];
        parent::init();
    }
}
