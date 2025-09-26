<?php
session_start();
include_once "database.php";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $book_id = $_POST['book_id'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$book_id || !$user_id) {
        header("Location: index.php?error=Invalid request");
        exit;
    }

   
    $sql = "SELECT id, status FROM books WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $book_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $book = mysqli_fetch_assoc($result);

    if (!$book) {
        header("Location: index.php?error=Book not found");
        exit;
    }

    
    if ($book['status'] === "Available") {
        $sql = "INSERT INTO borrow_history (book_id, user_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $book_id, $user_id);

        if (mysqli_stmt_execute($stmt)) {
           
            $sql = "UPDATE books SET status = 'Borrowed' WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $book_id);
            mysqli_stmt_execute($stmt);

            header("Location: index.php?success=Book borrowed successfully");
            exit;
        } else {
            header("Location: index.php?error=Failed to borrow book");
            exit;
        }
    } else {
        header("Location: index.php?error=Book is already borrowed");
        exit;
    }
}
?>
