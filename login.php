<?php
$host = "localhost";
$dbname = "self_leveling";
$username = "root"; // default user for XAMPP
$password = "";     // default password is empty

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user = $_POST['username'];
        $pass = hash('sha256', $_POST['password']); // hash to match stored password

        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
        $stmt->bindParam(':username', $user);
        $stmt->bindParam(':password', $pass);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Valid login
            $_SESSION['username'] = $user;
            
            header("Location: dashboard.html");
            exit();
        } else {
            echo "<script>alert('Invalid username or password'); window.location.href = 'index.html';</script>";
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
