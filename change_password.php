<?php
// Database connection
$host = "sql109.infinityfree.com";
$username = "if0_38583332";
$password = "Devsprint";
$database = "if0_38583332_webcraft";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];

    if (!empty($email) && !empty($new_password)) {
        // Securely hash the password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE teams SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $success = "Password updated successfully for $email.";
            } else {
                $error = "No user found with that email.";
            }
        } else {
            $error = "Failed to update password.";
        }

        $stmt->close();
    } else {
        $error = "Both fields are required.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Change Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center px-4">
    <div class="bg-gray-800 p-8 rounded-xl shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Admin: Change User Password</h2>

        <?php if ($success): ?>
            <div class="bg-green-500 text-white p-3 rounded mb-3"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="bg-red-500 text-white p-3 rounded mb-3"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label for="email" class="block mb-1">User Email</label>
                <input type="email" name="email" id="email" class="w-full px-4 py-2 rounded bg-gray-700 border border-gray-600 text-white" required>
            </div>

            <div class="mb-4">
                <label for="new_password" class="block mb-1">New Password</label>
                <input type="password" name="new_password" id="new_password" class="w-full px-4 py-2 rounded bg-gray-700 border border-gray-600 text-white" required>
            </div>

            <button type="submit" class="w-full py-2 px-4 rounded bg-blue-500 hover:bg-blue-600 transition">Update Password</button>
        </form>
    </div>
</body>
</html>
