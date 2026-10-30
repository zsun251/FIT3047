<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Database\Driver\Sqlite;
use Cake\Datasource\ConnectionManager;
use Migrations\Command\MarkMigratedCommand;
use Migrations\Command\MigrationsMigrateCommand;
use Migrations\Command\SeedCommand;
use PDO;

/**
 * SetupSqlite command.
 *
 * Builds the SQLite demo database from config/schema/schema.sqlite.sql and
 * config/schema/seed.sql, so the app runs without a MySQL server.
 * Runs automatically after `composer install`.
 */
class SetupSqliteCommand extends Command
{
    /**
     * Hook method for defining this command's option parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);
        $parser->setDescription('Create the SQLite demo database with the schema and sample data.');

        $parser->addOption('force', [
            'short' => 'f',
            'help' => 'Delete the existing database and build it again',
            'boolean' => true,
            'default' => false,
        ]);

        return $parser;
    }

    /**
     * Execute command
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $config = ConnectionManager::getConfig('default');
        if (($config['driver'] ?? null) !== Sqlite::class) {
            $io->out('The default datasource is not SQLite, skipping.');

            return static::CODE_SUCCESS;
        }

        $path = $config['database'];
        if (file_exists($path)) {
            if (!$args->getOption('force')) {
                $io->out("SQLite database already exists at {$path}. Use --force to rebuild it.");

                return static::CODE_SUCCESS;
            }
            unlink($path);
        }

        $pdo = new PDO('sqlite:' . $path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec(file_get_contents(CONFIG . 'schema' . DS . 'schema.sqlite.sql'));
        $pdo->exec($this->convertSeed(file_get_contents(CONFIG . 'schema' . DS . 'seed.sql')));
        $pdo = null;

        // schema.sqlite.sql already has the changes from config/Migrations
        $this->executeCommand(MarkMigratedCommand::class, [], $io);
        // content_blocks table (editable site text) from the ContentBlocks plugin.
        // Uses the phinx migrate command: the builtin one stores the `modified`
        // column's CURRENT_TIMESTAMP default as a literal string on SQLite.
        $this->executeCommand(MigrationsMigrateCommand::class, ['-p', 'ContentBlocks'], $io);
        $this->executeCommand(SeedCommand::class, ['--seed', 'ContentBlocksSeed'], $io);

        $io->success("SQLite database created at {$path}");

        return static::CODE_SUCCESS;
    }

    /**
     * Rewrite the MySQL date functions in seed.sql for SQLite,
     * so sample dates stay relative to the day the database is built.
     * Uses local time, like MySQL's NOW().
     */
    protected function convertSeed(string $sql): string
    {
        $sql = preg_replace_callback(
            '/DATE_(ADD|SUB)\(NOW\(\),\s*INTERVAL\s+(\d+)\s+(DAY|HOUR|MINUTE)\)/i',
            fn($m) => sprintf(
                "datetime('now', 'localtime', '%s%d %s')",
                strtoupper($m[1]) === 'ADD' ? '+' : '-',
                $m[2],
                strtolower($m[3])
            ),
            $sql
        );

        return str_ireplace('NOW()', "datetime('now', 'localtime')", $sql);
    }
}
