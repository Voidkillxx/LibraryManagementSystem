<?php
session_start();
include_once "database.php";
include_once "header.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $sql = "SELECT bh.id AS borrow_id, bh.book_id, bh.status, bh.borrow_date, bh.return_date,
                   b.title, b.author, b.genre, b.isbn
            FROM borrow_history bh
            JOIN books b ON bh.book_id = b.id
            WHERE bh.user_id = ?
            AND (b.title LIKE ? OR b.author LIKE ? OR b.genre LIKE ? OR b.isbn LIKE ?)
            ORDER BY bh.borrow_date DESC";

    $stmt = mysqli_prepare($conn, $sql);
    $searchLike = "%$search%";
    mysqli_stmt_bind_param($stmt, "issss", $user_id, $searchLike, $searchLike, $searchLike, $searchLike);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $sql = "SELECT bh.id AS borrow_id, bh.book_id, bh.status, bh.borrow_date, bh.return_date,
                   b.title, b.author, b.genre, b.isbn
            FROM borrow_history bh
            JOIN books b ON bh.book_id = b.id
            WHERE bh.user_id = ?
            ORDER BY bh.borrow_date DESC";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}

$histories = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Borrow History</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
        }
        .content {
            padding: 20px;
        }
        .search-form {
            margin-bottom: 20px;
        }
        .search-form input[type="text"] {
            padding: 10px;
            width: 300px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        .search-form button {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            background-color: #2196F3;
            color: white;
            cursor: pointer;
        }
        .search-form button:hover {
            background-color: #0b7dda;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: middle;
        }
        th {
            background-color: #2c3e50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #eef2f7;
        }
        .status-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .status-text {
            font-weight: bold;
            text-transform: capitalize;
        }
        
    </style>
</head>
<body>
<div class="content">
    <h2>Your Borrow History</h2>

    <form method="get" class="search-form">
        <input type="text" name="search" placeholder="Search by title, author, genre, ISBN..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <?php if (!empty($histories)): ?>
        <table>
            <tr>
                <th>Book Title</th>
                <th>Author</th>
                <th>Genre</th>
                <th>Borrow Date</th>
                <th>Return Date</th>
                <th>Status</th>
            </tr>
            <?php foreach ($histories as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['author']) ?></td>
                    <td><?= htmlspecialchars($row['genre']) ?></td>
                    <td><?= htmlspecialchars($row['borrow_date']) ?></td>
                    <td><?= $row['return_date'] ? htmlspecialchars($row['return_date']) : '-' ?></td>
                    <td class="status-cell">
                        <span class="status-text"><?= htmlspecialchars($row['status']) ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <h3>No borrow history found.</h3>
    <?php endif; ?>
</div>
</body>
</html>
