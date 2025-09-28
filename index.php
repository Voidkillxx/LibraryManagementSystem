<?php
session_start();
include_once "database.php";
include_once "header.php";

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$role   = $_SESSION['role'];
$search = trim($_GET['search'] ?? '');


$successMessage = $_GET['success'] ?? '';
$errorMessage   = $_GET['error'] ?? '';

if ($role === 'user') {
    if ($search !== '') {
        $sql  = "SELECT * FROM books 
                 WHERE status = 'available' 
                 AND (title LIKE ? OR author LIKE ? OR isbn LIKE ? OR genre LIKE ?)";
        $stmt = mysqli_prepare($conn, $sql);
        $searchLike = "%$search%";
        mysqli_stmt_bind_param($stmt, "ssss", $searchLike, $searchLike, $searchLike, $searchLike);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = mysqli_query($conn, "SELECT * FROM books WHERE status = 'available'");
    }
} else {
    if ($search !== '') {
        $sql  = "SELECT * FROM books 
                 WHERE title LIKE ? OR author LIKE ? OR isbn LIKE ? OR genre LIKE ?";
        $stmt = mysqli_prepare($conn, $sql);
        $searchLike = "%$search%";
        mysqli_stmt_bind_param($stmt, "ssss", $searchLike, $searchLike, $searchLike, $searchLike);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = mysqli_query($conn, "SELECT * FROM books");
    }
}

$books = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Library Catalog</title>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f9;
        margin: 0;
    }

    .content { padding: 20px; }

    .search-form { margin-bottom: 20px; }

    .search-form input[type=text] {
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

    .search-form button:hover { background-color: #0b7dda; }

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

    tr:nth-child(even) { background-color: #f9f9f9; }
    tr:hover { background-color: #eef2f7; }

   
    .status-cell {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 10px;
    }

    .status-text {
        font-weight: bold;
        text-transform: capitalize;
    }

    .action-form {
        display: inline-block;
        margin: 0;
        padding: 0;
    }

    .action-form button {
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        color: white;
        cursor: pointer;
        font-size: 13px;
    }

    .delete-button { background-color: #e74c3c; }
    .delete-button:hover { background-color: #c0392b; }

    .update-button { background-color: #3498db; }
    .update-button:hover { background-color: #2980b9; }

    .borrow-button { background-color: #2ecc71; }
    .borrow-button:hover { background-color: #27ae60; }
</style>
</head>
<body>

<div class="content">
    <form method="get" class="search-form">
        <input type="text" name="search" placeholder="Search Book, Author, ISBN, Genre" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <?php if (!empty($books)): ?>
        <table>
            <tr>
                <th>Book Title</th>
                <th>Author</th>
                <th>ISBN</th>
                <th>Publication Year</th>
                <th>Genre</th>
                <th>Status</th>
            </tr>
            <?php foreach ($books as $book): ?>
                <tr>
                    <td><?= htmlspecialchars($book['title']) ?></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td><?= htmlspecialchars($book['isbn']) ?></td>
                    <td><?= htmlspecialchars($book['publication_year']) ?></td>
                    <td><?= htmlspecialchars($book['genre']) ?></td>
                    <td class="status-cell">
                        <span class="status-text"><?= htmlspecialchars($book['status']) ?></span>
                        <?php if ($role === "librarian"): ?>
                            <form method="get" action="edit_book.php" class="action-form">
                                <input type="hidden" name="id" value="<?= (int)$book['id'] ?>">
                                <button type="submit" class="update-button">Update</button>
                            </form>
                            <form method="post" action="delete_book.php" class="action-form" 
                                  onsubmit="return confirm('Delete this book?');">
                                <input type="hidden" name="book_id" value="<?= (int)$book['id'] ?>">
                                <button type="submit" class="delete-button">Delete</button>
                            </form>
                        <?php elseif ($role === "user" && strtolower($book['status']) === "available"): ?>
                            <form method="post" action="borrow_book.php" class="action-form" 
                                  onsubmit="return confirm('Borrow this book?');">
                                <input type="hidden" name="book_id" value="<?= (int)$book['id'] ?>">
                                <button type="submit" class="borrow-button">Borrow</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <h2>No books found</h2>
    <?php endif; ?>
</div>

<script>
    
    <?php if ($successMessage): ?>
        alert(" <?= addslashes($successMessage) ?>");
    <?php endif; ?>
    <?php if ($errorMessage): ?>
        alert(" <?= addslashes($errorMessage) ?>");
    <?php endif; ?>
</script>

</body>
</html>

