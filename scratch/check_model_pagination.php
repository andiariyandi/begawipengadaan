<?php
define('FCPATH', realpath(__DIR__ . '/../public') . DIRECTORY_SEPARATOR);
require_once __DIR__ . '/../app/Config/Paths.php';
$paths = new Config\Paths();
require_once $paths->systemDirectory . '/Boot.php';
defined('ENVIRONMENT') || define('ENVIRONMENT', 'development');
CodeIgniter\Boot::bootConsole($paths);

$model = new \App\Models\Learning\LearningClassModel();
$builder = $model
    ->select('learning_classes.*, courses.title AS course_title')
    ->join('courses', 'courses.id = learning_classes.course_id', 'left')
    ->orderBy('learning_classes.created_at', 'DESC');

$total = $model->countAllResults(false);
echo "Total with false: " . $total . "\n";

$classes = $builder->findAll(5, 0);
echo "Classes fetched: " . count($classes) . "\n";
echo "Last query: " . $model->getLastQuery() . "\n";
