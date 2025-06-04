<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database config
$host = "sql109.infinityfree.com";
$user = "if0_38583332";
$password = "Devsprint";
$database = "if0_38583332_webcraft";

// Connect
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, password FROM teams WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        // Verify password hash
        if (password_verify($password, $row['password'])) {
            // Password correct - start session
            $_SESSION['id'] = $row['id'];
            $_SESSION['email'] = $email;

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // Password wrong
            header("Location: login.php?error=Incorrect%20password.");
            exit();
        }
    } else {
        // No user found
        header("Location: login.php?error=Email%20not%20registered.");
        exit();
    }
} else {
    // Not POST request
    header("Location: login.php");
    exit();
}
?>
