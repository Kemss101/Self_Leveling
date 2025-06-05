<?php
$host = "localhost";
$dbname = "self_leveling";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user = $_POST['username'];
        $pass = $_POST['password'];
        $confirm = $_POST['confirm_password'];
        $gender = $_POST['gender'];

        // Password match check
        if ($pass !== $confirm) {
            echo "<script>alert('Passwords do not match'); window.location.href = 'register.html';</script>";
            exit();
        }

        // Handle file upload
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir); // create if not exists
        $fileName = basename($_FILES["profile_image"]["name"]);
        $targetFile = $targetDir . time() . "_" . $fileName;
        move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFile);

        // Hash the password
        $hashedPassword = hash('sha256', $pass); // or use password_hash()

        // Insert into DB
        $stmt = $conn->prepare("INSERT INTO users (username, password, gender, profile_image) 
                                VALUES (:username, :password, :gender, :profile_image)");
        $stmt->bindParam(':username', $user);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':profile_image', $targetFile);

        $stmt->execute();

        echo "<script>alert('Registration successful!'); window.location.href = 'index.html';</script>";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
