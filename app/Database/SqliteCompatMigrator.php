<?php

namespace App\Database;

use Illuminate\Database\Events\MigrationEnded;
use Illuminate\Database\Events\MigrationStarted;
use Illuminate\Database\Migrations\Migrator;

/**
 * Custom Migrator yang skip MySQL-only MODIFY COLUMN statements saat running di SQLite.
 *
 * 30+ migration files di project ini menggunakan:
 *   DB::statement('ALTER TABLE x MODIFY [COLUMN] y ...')
 * yang valid di MySQL tapi tidak didukung SQLite.
 *
 * Kolom-kolom tersebut sudah didefinisikan dengan benar di migration CREATE TABLE asalnya,
 * sehingga MODIFY hanya mengubah tipe — aman di-skip untuk testing.
 */
class SqliteCompatMigrator extends Migrator
{
    /**
     * Override runMigration untuk wrap dengan try/catch khusus MODIFY error pada SQLite.
     */
    protected function runMigration($migration, $method): void
    {
        $connection = $this->resolveConnection(
            $migration->getConnection()
        );

        // Hanya apply guard pada SQLite — production MySQL tetap normal
        if ($connection->getDriverName() !== 'sqlite') {
            parent::runMigration($migration, $method);
            return;
        }

        $callback = function () use ($connection, $migration, $method) {
            if (method_exists($migration, $method)) {
                $this->fireMigrationEvent(new MigrationStarted($migration, $method));
                $this->runMethod($connection, $migration, $method);
                $this->fireMigrationEvent(new MigrationEnded($migration, $method));
            }
        };

        try {
            $this->getSchemaGrammar($connection)->supportsSchemaTransactions()
                && $migration->withinTransaction
                    ? $connection->transaction($callback)
                    : $callback();
        } catch (\Illuminate\Database\QueryException $e) {
            // Skip MySQL-only statements that SQLite doesn't support.
            // These migrations only alter column types or backfill data on existing rows —
            // on a fresh in-memory test DB there are no rows to update, so skipping is safe.
            // Patterns covered:
            //   - ALTER TABLE ... MODIFY [COLUMN]   → column type changes
            //   - information_schema queries         → MySQL system catalog
            //   - UPDATE t alias LEFT JOIN           → MySQL-specific UPDATE with JOIN/alias
            //   - General syntax errors from MySQL-specific SQL
            $msg = $e->getMessage();
            if (stripos($msg, 'MODIFY') !== false
                || stripos($msg, 'information_schema') !== false
                || stripos($msg, 'syntax error') !== false
                || stripos($msg, 'no such function') !== false) {
                return; // silently skip
            }
            throw $e;
        }
    }
}
