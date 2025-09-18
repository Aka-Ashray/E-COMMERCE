<?php
session_start();
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);
    
    try {
        $stmt->execute();
        $_SESSION['user_id'] = $mysqli->insert_id;
        $_SESSION['username'] = $username;
        $_SESSION['is_admin'] = false;
        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        $error = "Username already exists";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Cupboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h2 class="text-2xl font-bold mb-6 text-center">Register Account</h2>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Username</label>
                    <input type="text" name="username" required class="w-full p-2 border rounded">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required class="w-full p-2 border rounded">
                </div>
                <button class="w-full bg-green-500 text-white p-2 rounded hover:bg-green-600">Register</button>
            </form>
            <p class="mt-4 text-center">
                Already have an account? <a href="login.php" class="text-blue-500">Login here</a>
            </p>
        </div>
    </div>
</body>
</html>
