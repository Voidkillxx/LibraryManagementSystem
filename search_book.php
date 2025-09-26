<?php
session_start();
include_once "database.php";

$books = [];

if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['query'])) {
    $query = trim($_GET['query']);

    if (!empty($query)) {
        $sql = "SELECT * FROM books WHERE title LIKE ? OR author LIKE ?";
        $stmt = mysqli_prepare($conn, $sql);
        $searchTerm = "%" . $query . "%";
        mysqli_stmt_bind_param($stmt, "ss", $searchTerm, $searchTerm);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $books = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Books</title>
</head>
<body>

    <button onclick="window.location.href='index.php'"><- Back</button>

    <h2>Search Books</h2>
    <form method="get" action="search_book.php">
        <input type="text" name="query" placeholder="Enter title or author" 
               value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : '' ?>">
        <button type="submit">Search</button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['query'])): ?>
        <?php if (!empty($books)): ?>
            <table border="1" cellspacing="5">
                <tr>
                    <th>Book Title</th>
                    <th>Author</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                        <td><?php echo htmlspecialchars($book['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p style="color:red;">No books found for "<?php echo htmlspecialchars($_GET['query']); ?>"</p>
        <?php endif; ?>
    <?php endif; ?>

</body>
</html>
