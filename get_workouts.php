<?php
session_start();

if (!isset($_SESSION['username'])) {
    http_response_code(403);
    echo "Not logged in";
    exit();
}

$pdo = new PDO("mysql:host=localhost;dbname=self_leveling", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get POST values
$username = $_SESSION['username'];
$exercise = $_POST['exercise_name'] ?? '';
$duration = $_POST['duration'] ?? 0;
$reps = $_POST['reps'] ?? 0;
$calories = $_POST['calories'] ?? 0.0;

$stmt = $pdo->prepare("INSERT INTO workouts (username, exercise_name, duration, calories_burned, repetitions)
                       VALUES (:username, :exercise, :duration, :calories, :reps)");
$stmt->execute([
    'username' => $username,
    'exercise' => $exercise,
    'duration' => $duration,
    'calories' => $calories,
    'reps' => $reps
]);

echo "Success";
