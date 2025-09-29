<?php
include_once "database.php";
include_once "header.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id  = $_SESSION['user_id'];
$role     = $_SESSION['role'];
$username = $_SESSION['username'];


if ($role === "librarian") {
    $sql_current = "
        SELECT bh.id AS borrow_id, b.title, b.author, b.isbn, bh.borrow_date, bh.status, bh.book_id, u.username
        FROM borrow_history bh
        JOIN books b ON bh.book_id = b.id
        JOIN users u ON bh.user_id = u.id
        WHERE bh.status = 'borrowed'
        ORDER BY bh.borrow_date DESC
        LIMIT 20
    ";
    $stmt_current = $conn->prepare($sql_current);
} else {
    $sql_current = "
        SELECT bh.id AS borrow_id, b.title, b.author, b.isbn, bh.borrow_date, bh.status, bh.book_id
        FROM borrow_history bh
        JOIN books b ON bh.book_id = b.id
        WHERE bh.user_id = ? AND bh.status = 'borrowed'
        ORDER BY bh.borrow_date DESC
    ";
    $stmt_current = $conn->prepare($sql_current);
    $stmt_current->bind_param("i", $user_id);
}
$stmt_current->execute();
$current = $stmt_current->get_result();


if ($role === "librarian") {
    $sql_history = "
        SELECT bh.id AS borrow_id, b.title, b.author, b.isbn, bh.status, bh.borrow_date, bh.return_date, u.username
        FROM borrow_history bh
        JOIN books b ON bh.book_id = b.id
        JOIN users u ON bh.user_id = u.id
        ORDER BY bh.borrow_date DESC
        LIMIT 20
    ";
    $stmt_history = $conn->prepare($sql_history);
} else {
    $sql_history = "
        SELECT bh.id AS borrow_id, b.title, b.author, b.isbn, bh.status, bh.borrow_date, bh.return_date
        FROM borrow_history bh
        JOIN books b ON bh.book_id = b.id
        WHERE bh.user_id = ?
        ORDER BY bh.borrow_date DESC
        LIMIT 10
    ";
    $stmt_history = $conn->prepare($sql_history);
    $stmt_history->bind_param("i", $user_id);
}
$stmt_history->execute();
$history = $stmt_history->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Library Home</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f7;
        }
        .content {
            padding: 20px;
        }
        h2, h3 {
            margin: 15px 0;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            vertical-align: middle;
        }
        th {
            background: #2c3e50;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        tr:hover {
            background: #eef3f7;
        }
        .status-cell {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }
        .btn-return {
            background: #e74c3c;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .btn-return:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
    <div class="content">
        <h2>Welcome, <?= htmlspecialchars($username) ?>!</h2>

        <h3><?= $role === "librarian" ? "All Currently Borrowed Books" : "Your Currently Borrowed Books" ?></h3>
        <table>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>ISBN</th>
                <th>Borrow Date</th>
                <?php if ($role === "librarian"): ?><th>User</th><?php endif; ?>
                <th>Status</th>
            </tr>
            <?php if ($current->num_rows > 0): ?>
                <?php while ($row = $current->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['author']) ?></td>
                        <td><?= htmlspecialchars($row['isbn']) ?></td>
                        <td><?= $row['borrow_date'] ?></td>
                        <?php if ($role === "librarian"): ?>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                        <?php endif; ?>
                        <td>
                            <div class="status-cell">
                                <span><?= ucfirst($row['status']) ?></span>
                                <?php if ($row['status'] === 'borrowed' && $role === "librarian"): ?>
                                    <form method="POST" action="return_book.php" style="margin:0;">
                                        <input type="hidden" name="borrowing_id" value="<?= $row['borrow_id'] ?>">
                                        <input type="hidden" name="book_id" value="<?= $row['book_id'] ?>">
                                        <button type="submit" class="btn-return" onclick="return confirm('Return this book?');">Return</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= $role === "librarian" ? 6 : 5 ?>" style="text-align:center;">
                        No currently borrowed books.
                    </td>
                </tr>
            <?php endif; ?>
        </table>

        <h3><?= $role === "librarian" ? "All Borrow History" : "Your Borrow History" ?></h3>
        <table>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>ISBN</th>
                <?php if ($role === "librarian"): ?><th>User</th><?php endif; ?>
                <th>Status</th>
                <th>Borrow Date</th>
                <th>Return Date</th>
            </tr>
            <?php if ($history->num_rows > 0): ?>
                <?php while ($row = $history->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['author']) ?></td>
                        <td><?= htmlspecialchars($row['isbn']) ?></td>
                        <?php if ($role === "librarian"): ?>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                        <?php endif; ?>
                        <td><?= ucfirst($row['status']) ?></td>
                        <td><?= $row['borrow_date'] ?></td>
                        <td><?= $row['return_date'] ?? "-" ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= $role === "librarian" ? 7 : 6 ?>" style="text-align:center;">
                        No borrow history found.
                    </td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
