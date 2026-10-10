<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CertificateSmoke extends BaseCommand
{
    protected $group       = 'Certificate';
    protected $name        = 'certificate:smoke';
    protected $description = 'Smoke test the certificate CRUD against the database.';
    protected $usage       = 'certificate:smoke';
    protected $arguments   = [];
    protected $options     = [];

    public function run(array $params): int
    {
        $db = \Config\Database::connect();
        CLI::write('Connected to: ' . $db->getDatabase(), 'green');
        CLI::newLine();
        CLI::write('Row counts:', 'yellow');
        foreach (['certificates','certificate_templates','certificate_sequences','enrollments','users','learning_classes','courses'] as $t) {
            try {
                $r = $db->table($t)->countAllResults();
                CLI::write(sprintf('  %-26s = %d', $t, $r));
            } catch (\Throwable $e) {
                CLI::error('  ' . $t . ' = ERR ' . $e->getMessage());
            }
        }
        CLI::newLine();
        CLI::write('Completed enrollments (first 3):', 'yellow');
        $rows = $db->table('enrollments e')
            ->select('e.id, e.user_id, e.completed_at, e.final_score, u.name AS user_name, lc.name AS class_name, c.title AS course_title')
            ->join('learning_classes lc', 'lc.id = e.class_id', 'left')
            ->join('courses c',          'c.id = lc.course_id', 'left')
            ->join('users u',            'u.id = e.user_id', 'left')
            ->where('e.completed_at IS NOT NULL', null, false)
            ->orderBy('e.id', 'DESC')
            ->limit(3)
            ->get()->getResultArray();
        if (! $rows) {
            CLI::write('  (none)');
        }
        foreach ($rows as $r) {
            CLI::write(sprintf('  #%d user=%s class=%s course=%s score=%s',
                $r['id'],
                $r['user_name']   ?? '?',
                $r['class_name']  ?? '?',
                $r['course_title']?? '?',
                $r['final_score'] ?? '?'
            ));
        }
        CLI::newLine();
        CLI::write('Sample users (first 3):', 'yellow');
        $users = $db->table('users')->orderBy('name','ASC')->limit(3)->get()->getResultArray();
        if (! $users) { CLI::write('  (none)'); }
        foreach ($users as $u) {
            CLI::write(sprintf('  #%d %s <%s>', $u['id'], $u['name'] ?? '?', $u['email'] ?? ''));
        }
        CLI::newLine();
        CLI::write('Active certificate_templates:', 'yellow');
        $tpls = $db->table('certificate_templates')->where('is_active', 1)->orderBy('id','DESC')->limit(3)->get()->getResultArray();
        if (! $tpls) { CLI::write('  (none)'); }
        foreach ($tpls as $t) {
            CLI::write(sprintf('  #%d %s', $t['id'], $t['name'] ?? '?'));
        }
        CLI::newLine();
        CLI::write('Done.', 'green');
        return 0;
    }
}