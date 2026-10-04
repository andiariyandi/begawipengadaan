<?php
chdir(__DIR__);
require 'vendor/autoload.php';
$db = \Config\Database::connect();
foreach (['users','roles','user_roles','programs','courses','learning_classes','class_trainers','learning_modules','lessons','quizzes','quiz_questions','quiz_options','enrollments'] as $t) {
    $c = $db->table($t)->countAllResults();
    echo str_pad($t, 22) . " => $c\n";
}