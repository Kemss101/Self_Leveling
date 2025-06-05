<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

$pdo = new PDO("mysql:host=localhost;dbname=self_leveling", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$username = $_SESSION['username'];
$gender = "Not Set"; // Set from DB if needed
$image = "images/default.jpg"; // Optional user image

// Fetch past workouts
$stmt = $pdo->prepare("SELECT * FROM workouts WHERE username = :username ORDER BY created_at DESC");
$stmt->execute(['username' => $username]);
$workouts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Self Leveling</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-container">
    <div class="sidebar">
        <img src="<?= htmlspecialchars($image) ?>" class="profile-img" alt="User">
        <p class="user-info"><?= htmlspecialchars($gender) ?><br><strong><?= htmlspecialchars($username) ?></strong></p>

        <label>Choose exercise</label>
        <select id="exercise">
            <option>Squat</option>
            <option>Deadlift</option>
            <option>Push-Up</option>
        </select>

        <p>Reps: <span id="reps">0</span></p>
        <p>Current Exercise: <span id="current-exercise">None</span></p>
        <p>Time Elapsed: <span id="time">0s</span></p>
        <p>Calories Burned: <span id="calories">0.00</span></p>

        <label>Intensity</label>
        <select id="intensity">
            <option value="low">Low</option>
            <option value="medium" selected>Medium</option>
            <option value="high">High</option>
        </select>

        <input type="number" id="weight" placeholder="Enter your weight">

        <button onclick="startWorkout()">Start Workout</button>
        <button onclick="stopWorkout()">Stop</button>
        <button onclick="location.reload()">View Dashboard</button>
        <button onclick="window.location.href='logout.php'">Logout</button>
    </div>

    <div class="main-panel">
        <h2>Workout History</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Exercise Name</th>
                    <th>Duration (s)</th>
                    <th>Calories Burned</th>
                    <th>Repetitions</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($workouts as $w): ?>
                    <tr>
                        <td><?= htmlspecialchars($w['exercise_name']) ?></td>
                        <td><?= $w['duration'] ?></td>
                        <td><?= $w['calories_burned'] ?></td>
                        <td><?= $w['repetitions'] ?></td>
                        <td><?= $w['created_at'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    let reps = 0, seconds = 0, calories = 0, timer = null;

    function getCaloriesPerRep(weight, intensity) {
        const factors = { low: 0.05, medium: 0.08, high: 0.12 };
        return weight * (factors[intensity] || 0.08);
    }

    function updateUI() {
        document.getElementById("reps").innerText = reps;
        document.getElementById("time").innerText = seconds + "s";
        document.getElementById("calories").innerText = calories.toFixed(2);
    }

    function startWorkout() {
        const weight = parseFloat(document.getElementById("weight").value);
        if (isNaN(weight) || weight <= 0) {
            alert("Enter a valid weight.");
            return;
        }

        const exercise = document.getElementById("exercise").value;
        const intensity = document.getElementById("intensity").value;

        document.getElementById("current-exercise").innerText = exercise;

        reps = 0;
        seconds = 0;
        calories = 0;
        updateUI();

        timer = setInterval(() => {
            seconds++;
            if (seconds % 3 === 0) {
                reps++;
                calories += getCaloriesPerRep(weight, intensity);
            }
            updateUI();
        }, 1000);
    }

    function stopWorkout() {
        clearInterval(timer);
        const exercise = document.getElementById("exercise").value;

        // Send data to PHP to save
        fetch("save_workout.php", {
            method: "POST",
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                exercise_name: exercise,
                duration: seconds,
                reps: reps,
                calories: calories.toFixed(2)
            })
        }).then(res => res.text()).then(res => {
            alert("Workout saved: " + res);
            location.reload();
        }).catch(err => alert("Error saving workout: " + err));
    }
</script>
</body>
</html>
