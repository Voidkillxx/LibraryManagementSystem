<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>

<nav class="navbar">
    <div class="logo">Library Management System</div>
    <ul class="nav-links">
        <li><a href="home.php">Home</a></li>
        <li><a href="index.php">View Books</a></li>

        <?php if ($role === 'librarian'): ?>
            <li><a href="add_book.php">Add Book</a></li>
        <?php elseif ($role === 'user'): ?>
            <li><a href="borrow_history.php">Borrow History</a></li>
        <?php endif; ?>

        <li><span class="username"><?php echo htmlspecialchars($username); ?></span></li>
        <li><a onclick="return confirm('Are you sure you want to logout?');" href="logout.php" class="logout">Logout</a></li>
    </ul>
</nav>

<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #f4f6f9;
    }

    .navbar {
        background: #2c3e50;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 20px;
        width: 100%;
        box-sizing: border-box;
    }

    .navbar .logo {
        font-size: 20px;
        font-weight: bold;
    }

    .nav-links {
        list-style: none;
        display: flex;
        margin: 0;
        padding: 0;
    }

    .nav-links li {
        margin-left: 20px;
        display: flex;
        align-items: center;
    }

    .nav-links a {
        text-decoration: none;
        color: white;
        font-size: 16px;
        padding: 6px 12px;
        border-radius: 5px;
        transition: background 0.3s;
    }

    .nav-links a:hover {
        background: #34495e;
    }

    .nav-links .logout {
        background: #e74c3c;
    }

    .nav-links .logout:hover {
        background: #c0392b;
    }

    .username {
        font-weight: bold;
        color: #1abc9c;
        padding: 6px 12px;
    }
</style>
