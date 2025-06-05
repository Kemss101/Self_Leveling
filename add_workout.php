<?php
include 'db.php';
session_start();

$user_id = $_SESSION['user_id'];
$exercise = $_POST['exercise'];
$duration = $_POST['duration'];
$calories = $_POST['calories'];
$reps = $_POST['reps'];

$sql = "INSERT INTO workouts (user_id, exercise_name, duration, calories_burned, repetitions)
        VALUES ($user_id, '$exercise', $duration, $calories, $reps)";

if ($conn->query($sql) === TRUE) {
    echo "Workout saved";
} else {
    echo "Error: " . $conn->error;
}
?>