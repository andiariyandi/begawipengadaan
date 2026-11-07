<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SchemaDriftAudit extends BaseCommand
{
    protected $group       = 'Audit';
    protected $name        = 'audit:schema-drift';
    protected $description = 'Audit presisi skema database terhadap Model definitions, deteksi DDL runtime liar, dan integritas tabel.';

    public function run(array $params)
    {
        CLI::write("==================================================================", 'cyan');
        CLI::write("        BEGAWIPENGADAAN ZERO SCHEMA-DRIFT AUDIT WATCHDOG          ", 'cyan');
        CLI::write("==================================================================", 'cyan');
        CLI::newLine();

        $dbName = config('Database')->default['database'] ?? 'db_begawipbj';
        $db = \Config\Database::connect();

        // 1. Ambil seluruh skema tabel & kolom dari information_schema
        CLI::write("--- [1] MENGAMBIL GROUND TRUTH DARI INFORMATION_SCHEMA ($dbName) ---", 'yellow');
        $query = $db->query("SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ?", [$dbName]);
        $rows = $query->getResultArray();

        $schema = [];
        foreach ($rows as $r) {
            $tName = $r['TABLE_NAME'];
            $cName = strtolower($r['COLUMN_NAME']);
            $schema[$tName][$cName] = true;
        }

        $tableCount = count($schema);
        $columnCount = count($rows);
        CLI::write(" [INFO] Skema Aktual: {$tableCount} Tabel dan {$columnCount} Kolom terdaftar di MySQL.", 'white');

        $passed = 0;
        $failed = 0;

        // 2. Audit Model Definitions ($table & $allowedFields)
        CLI::newLine();
        CLI::write("--- [2] MEMERIKSA INTEGRITAS CLASS MODEL (app/Models/**) ---", 'yellow');

        $modelFiles = [];
        $modelsDir = APPPATH . 'Models';
        if (is_dir($modelsDir)) {
            $iter = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($modelsDir));
            foreach ($iter as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $modelFiles[] = $file->getPathname();
                }
            }
        }

        $modelIssues = [];
        $verifiedModels = 0;

        foreach ($modelFiles as $mf) {
            $code = file_get_contents($mf);
            if (! preg_match('/class\s+(\w+)\s+extends/i', $code, $cm)) {
                continue;
            }
            $className = $cm[1];

            // Dapatkan nama tabel
            $table = null;
            if (preg_match('/protected\s+\$table\s*=\s*[\'"]([^\'"]+)[\'"]/i', $code, $tm)) {
                $table = $tm[1];
            }

            // Dapatkan allowedFields
            $allowedFields = [];
            if (preg_match('/protected\s+\$allowedFields\s*=\s*\[(.*?)\];/s', $code, $am)) {
                preg_match_all('/[\'"]([^\'"]+)[\'"]/', $am[1], $fieldMatches);
                $allowedFields = $fieldMatches[1] ?? [];
            }

            if ($table !== null) {
                $verifiedModels++;
                if (! isset($schema[$table])) {
                    $modelIssues[] = "Model {$className}: Tabel '{$table}' TIDAK ditemukan di database {$dbName}!";
                    continue;
                }

                $invalidCols = [];
                foreach ($allowedFields as $f) {
                    $colLower = strtolower($f);
                    if (! isset($schema[$table][$colLower])) {
                        $invalidCols[] = $f;
                    }
                }

                if (! empty($invalidCols)) {
                    $colsStr = implode(', ', $invalidCols);
                    $modelIssues[] = "Model {$className} (tabel '{$table}'): Kolom fiktif terdeteksi pada allowedFields: [{$colsStr}]";
                }
            }
        }

        if (empty($modelIssues)) {
            CLI::write(" [PASS] Seluruh {$verifiedModels} Class Model memiliki definisi \$table & \$allowedFields 100% valid sesuai skema MySQL.", 'green');
            $passed++;
        } else {
            foreach ($modelIssues as $err) {
                CLI::error(" [FAIL] {$err}");
            }
            $failed += count($modelIssues);
        }

        // 3. Scan Controllers & Services for Runtime DDL and Invalid table() calls
        CLI::newLine();
        CLI::write("--- [3] MEMINDAI RUNTIME DDL & LITERAL NAMA TABEL (app/Controllers & app/Services) ---", 'yellow');

        $scanDirs = [APPPATH . 'Controllers', APPPATH . 'Services'];
        $phpFiles = [];
        foreach ($scanDirs as $d) {
            if (! is_dir($d)) {
                continue;
            }
            $iter = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($d));
            foreach ($iter as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $phpFiles[] = $file->getPathname();
                }
            }
        }

        $ddlIssues = [];
        $tableIssues = [];

        foreach ($phpFiles as $file) {
            $code = file_get_contents($file);
            $relPath = str_replace(ROOTPATH, '', $file);

            // Deteksi runtime CREATE TABLE atau ALTER TABLE
            if (preg_match('/\b(CREATE\s+TABLE|ALTER\s+TABLE)\b/i', $code, $m)) {
                $ddlIssues[] = "{$relPath}: Mengandung DDL runtime liar ({$m[1]})!";
            }

            // Validasi ->table('...')
            if (preg_match_all('/->table\(\s*[\'"]([^\'"]+)[\'"]\s*\)/', $code, $matches)) {
                foreach ($matches[1] as $tableLiteral) {
                    $parts = preg_split('/\s+/', trim($tableLiteral));
                    $rawTable = $parts[0] ?? '';
                    if ($rawTable !== '' && ! isset($schema[$rawTable])) {
                        $tableIssues[] = "{$relPath}: Menggunakan literal tabel tidak valid '{$rawTable}' pada ->table('{$tableLiteral}')!";
                    }
                }
            }
        }

        if (empty($ddlIssues)) {
            CLI::write(" [PASS] 0 Runtime DDL (CREATE/ALTER TABLE) terdeteksi pada seluruh " . count($phpFiles) . " berkas Controllers & Services.", 'green');
            $passed++;
        } else {
            foreach ($ddlIssues as $err) {
                CLI::error(" [FAIL] {$err}");
            }
            $failed += count($ddlIssues);
        }

        if (empty($tableIssues)) {
            CLI::write(" [PASS] Seluruh literal nama tabel pada pemanggilan ->table('...') 100% valid ada di skema database.", 'green');
            $passed++;
        } else {
            foreach ($tableIssues as $err) {
                CLI::error(" [FAIL] {$err}");
            }
            $failed += count($tableIssues);
        }

        // 4. Validasi Integritas Kolom Service PublicVisibilityGuard
        CLI::newLine();
        CLI::write("--- [4] VALIDASI INTEGRITAS KOLOM SERVICE PUBLICVISIBILITYGUARD ---", 'yellow');
        $guardFile = APPPATH . 'Services/PublicVisibilityGuard.php';
        $guardIssues = [];

        if (! file_exists($guardFile)) {
            $guardIssues[] = "File PublicVisibilityGuard.php tidak ditemukan di {$guardFile}!";
        } else {
            $guardCode = file_get_contents($guardFile);

            // a. Validasi applyPublicEvents
            if (preg_match('/function\s+applyPublicEvents\s*\([^)]*\)\s*:[^{]*\{([^}]+)\}/s', $guardCode, $mEvents)) {
                $eventsBody = $mEvents[1];
                if (preg_match('/\b(workflow_status|is_active)\b/i', $eventsBody, $fictitious)) {
                    $guardIssues[] = "PublicVisibilityGuard::applyPublicEvents mengandung kolom fiktif '{$fictitious[1]}' yang tidak ada pada tabel events!";
                }

                if (preg_match_all("/->where(?:In)?\(\s*\\\$prefix\s*\.\s*['\"]([^'\"]+)['\"]/i", $eventsBody, $mCols)) {
                    foreach ($mCols[1] as $col) {
                        $colClean = strtolower(trim($col));
                        if (! isset($schema['events'][$colClean])) {
                            $guardIssues[] = "PublicVisibilityGuard::applyPublicEvents memanggil kolom '{$colClean}' yang tidak terdaftar di skema tabel events!";
                        }
                    }
                }
            } else {
                $guardIssues[] = "Method applyPublicEvents() tidak dapat diparsing di PublicVisibilityGuard.php!";
            }

            // b. Validasi applyPublicCourses
            if (preg_match('/function\s+applyPublicCourses\s*\([^)]*\)\s*:[^{]*\{([^}]+)\}/s', $guardCode, $mCourses)) {
                $coursesBody = $mCourses[1];
                if (preg_match('/\b(workflow_status|is_active)\b/i', $coursesBody, $fictitious)) {
                    $guardIssues[] = "PublicVisibilityGuard::applyPublicCourses mengandung kolom fiktif '{$fictitious[1]}' yang tidak ada pada tabel courses!";
                }

                if (preg_match_all("/->where(?:In)?\(\s*\\\$prefix\s*\.\s*['\"]([^'\"]+)['\"]/i", $coursesBody, $mCols)) {
                    foreach ($mCols[1] as $col) {
                        $colClean = strtolower(trim($col));
                        if (! isset($schema['courses'][$colClean])) {
                            $guardIssues[] = "PublicVisibilityGuard::applyPublicCourses memanggil kolom '{$colClean}' yang tidak terdaftar di skema tabel courses!";
                        }
                    }
                }
            } else {
                $guardIssues[] = "Method applyPublicCourses() tidak dapat diparsing di PublicVisibilityGuard.php!";
            }

            // c. Validasi applyPublicWorkflow
            if (preg_match('/function\s+applyPublicWorkflow\s*\([^)]*\)\s*:[^{]*\{([^}]+)\}/s', $guardCode, $mWorkflow)) {
                $workflowBody = $mWorkflow[1];
                $targetTables = ['news', 'regulations', 'knowledge_articles'];

                if (preg_match_all("/->where(?:In)?\(\s*\\\$prefix\s*\.\s*['\"]([^'\"]+)['\"]/i", $workflowBody, $mCols)) {
                    foreach ($mCols[1] as $col) {
                        $colClean = strtolower(trim($col));
                        foreach ($targetTables as $tgtTable) {
                            if (! isset($schema[$tgtTable][$colClean])) {
                                $guardIssues[] = "PublicVisibilityGuard::applyPublicWorkflow memanggil kolom '{$colClean}' yang tidak terdaftar di skema tabel {$tgtTable}!";
                            }
                        }
                    }
                }
            } else {
                $guardIssues[] = "Method applyPublicWorkflow() tidak dapat diparsing di PublicVisibilityGuard.php!";
            }
        }

        if (empty($guardIssues)) {
            CLI::write(" [PASS] Seluruh kolom pada PublicVisibilityGuard (events, courses, news, regulations, knowledge_articles) 100% valid sesuai skema MySQL.", 'green');
            $passed++;
        } else {
            foreach ($guardIssues as $err) {
                CLI::error(" [FAIL] {$err}");
            }
            $failed += count($guardIssues);
        }

        // SUMMARY
        CLI::newLine();
        CLI::write("==================================================================", 'cyan');
        CLI::write("                 HASIL AUDIT SCHEMA-DRIFT                         ", 'cyan');
        CLI::write("==================================================================", 'cyan');
        CLI::write(" Total Kategori Audit : " . ($passed + $failed), 'white');
        CLI::write(" Kategori Berhasil    : " . $passed, 'green');
        CLI::write(" Kategori Gagal       : " . $failed, $failed > 0 ? 'red' : 'green');
        CLI::write("==================================================================", 'cyan');

        return $failed > 0 ? 1 : 0;
    }
}
