<?php
session_start();
include_once "database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['book_id'])) {
    header("Location: index.php?error=No book ID provided");
    exit;
}

$book_id = (int)$_POST['book_id'];

$stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);

if ($stmt->execute()) {
    header("Location: index.php?success=Book deleted successfully");
    exit;
} else {
    header("Location: index.php?error=Error deleting book");
    exit;
}

?>
