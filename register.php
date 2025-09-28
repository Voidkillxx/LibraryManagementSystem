<?php
session_start();
include_once "database.php";

$error   = "";
$success = "";

$username         = "";
$password         = "";
$confirm_password = "";


if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $username         = trim($_POST['username']);
    $password         = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($username === "" || $password === "" || $confirm_password === "") {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 4) {
        $error = "Password must be at least 4 characters.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_fetch_assoc($result)) {
            $error = "Username already exists.";
        } else {
            mysqli_stmt_close($stmt);

            $role            = "user";

            $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $username, $password, $role);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
                $username = $password = $confirm_password = "";
            } else {
                $error = "Error: " . $conn->error;
            }

            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f0f2f5;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    .register-container {
        background: #fff;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        width: 350px;
    }
    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }
    input[type=text], input[type=password] {
        width: 100%;
        padding: 10px;
        margin: 8px 0 5px 0;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 14px;
    }
    .show-pass-container {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    .show-pass-container input[type=checkbox] {
        margin-right: 8px;
    }
    button {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 8px;
        background-color: #4CAF50;
        color: white;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s;
    }
    button:hover {
        background-color: #45a049;
    }
    .message {
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 15px;
        text-align: center;
        font-weight: bold;
    }
    .error {
        color: #842029;
        background-color: #f8d7da;
        border: 1px solid #842029;
    }
    .success {
        color: #0f5132;
        background-color: #d1e7dd;
        border: 1px solid #0f5132;
    }
    .login-link {
        display: block;
        margin-top: 15px;
        text-align: center;
        color: #333;
        text-decoration: none;
    }
    .login-link:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>
<div class="register-container">
    <h2>Register</h2>

    <?php if ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="message success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" 
               value="<?= htmlspecialchars($username) ?>" required>

        <input type="password" id="password" name="password" placeholder="Password" 
               value="<?= htmlspecialchars($password) ?>" required>

        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" 
               value="<?= htmlspecialchars($confirm_password) ?>" required>

        <div class="show-pass-container">
            <input type="checkbox" id="showPassword">
            <label for="showPassword">Show Password</label>
        </div>

        <button type="submit">Register</button>
    </form>

    <a class="login-link" href="login.php">Already have an account? Login here</a>
</div>

<script>
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const showPasswordCheckbox = document.getElementById('showPassword');

    showPasswordCheckbox.addEventListener('change', function() {
        const type = this.checked ? 'text' : 'password';
        passwordInput.type = type;
        confirmPasswordInput.type = type;
    });
</script>
</body>
</html>
