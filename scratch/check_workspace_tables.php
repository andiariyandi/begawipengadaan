<?php
require 'app/Config/Paths.php';
require 'system/bootstrap.php';
$db = \Config\Database::connect();
$tables = [
    'event_registrations', 'event_attendances', 'events',
    'assignments', 'assignment_submissions',
    'quizzes', 'quiz_attempts', 'certificates',
    'bookmarks', 'notifications', 'user_activity_logs', 'audit_trails',
    'class_evaluators', 'class_trainers', 'enrollments'
];
foreach ($tables as $t) {
    echo $t . ': ' . ($db->tableExists($t) ? 'YES' : 'NO') . PHP_EOL;
}
