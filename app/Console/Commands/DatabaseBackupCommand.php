<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseBackupCommand extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Safely create a compressed database backup using pure PDO and prune backups older than 14 days';

    public function handle(): int
    {
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $database = config('database.connections.mysql.database');
        $filename = 'prime_db_backup_' . date('Y_m_d_His') . '.sql.gz';
        $filepath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        $this->info("Starting automated backup for database [{$database}]...");
        $startTime = microtime(true);

        $gz = gzopen($filepath, 'w9');
        if (!$gz) {
            $this->error("Cannot open {$filepath} for writing.");
            return Command::FAILURE;
        }

        $pdo = DB::connection()->getPdo();

        // Header
        gzwrite($gz, "-- Prime Booking Automated Database Backup\n");
        gzwrite($gz, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
        gzwrite($gz, "-- Database: {$database}\n\n");
        gzwrite($gz, "SET FOREIGN_KEY_CHECKS=0;\n");
        gzwrite($gz, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
        gzwrite($gz, "SET time_zone = \"+00:00\";\n\n");

        $tablesStmt = $pdo->query('SHOW TABLES');
        $tables = $tablesStmt->fetchAll(\PDO::FETCH_COLUMN);
        $totalTables = count($tables);

        $this->info("Found {$totalTables} tables to dump.");

        foreach ($tables as $table) {
            // Drop & Create Table
            gzwrite($gz, "-- --------------------------------------------------------\n");
            gzwrite($gz, "-- Table structure for `{$table}`\n");
            gzwrite($gz, "-- --------------------------------------------------------\n\n");
            gzwrite($gz, "DROP TABLE IF EXISTS `{$table}`;\n");

            $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
            $createRow = $createStmt->fetch(\PDO::FETCH_NUM);
            if (!empty($createRow[1])) {
                gzwrite($gz, $createRow[1] . ";\n\n");
            }

            // Table Data
            $dataStmt = $pdo->query("SELECT * FROM `{$table}`");
            $rows = [];
            while ($row = $dataStmt->fetch(\PDO::FETCH_NUM)) {
                $escapedValues = array_map(function ($val) use ($pdo) {
                    if ($val === null) return 'NULL';
                    return $pdo->quote($val);
                }, $row);
                $rows[] = '(' . implode(', ', $escapedValues) . ')';

                if (count($rows) >= 200) {
                    gzwrite($gz, "INSERT INTO `{$table}` VALUES\n" . implode(",\n", $rows) . ";\n");
                    $rows = [];
                }
            }
            if (count($rows) > 0) {
                gzwrite($gz, "INSERT INTO `{$table}` VALUES\n" . implode(",\n", $rows) . ";\n\n");
            }
        }

        gzwrite($gz, "SET FOREIGN_KEY_CHECKS=1;\n");
        gzclose($gz);

        $duration = round(microtime(true) - $startTime, 2);
        $sizeMb = round(filesize($filepath) / 1024 / 1024, 2);
        $this->info("✓ Database backup completed successfully in {$duration}s: {$filename} ({$sizeMb} MB)");

        // Prune backups older than 14 days
        $files = File::files($backupDir);
        $pruned = 0;
        foreach ($files as $file) {
            if ($file->getMTime() < (time() - 14 * 86400)) {
                File::delete($file->getPathname());
                $pruned++;
            }
        }
        if ($pruned > 0) {
            $this->comment("✓ Pruned {$pruned} backup(s) older than 14 days.");
        }

        return Command::SUCCESS;
    }
}
