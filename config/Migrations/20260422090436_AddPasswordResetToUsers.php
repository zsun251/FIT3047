<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddPasswordResetToUsers extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/4/en/migrations.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('users');
        $table->addColumn('reset_token', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ])
            ->addColumn('reset_token_expires', 'datetime', [
                'default' => null,
                'null' => true,
            ])
            ->update();
    }
}
