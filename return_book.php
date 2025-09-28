<?php
session_start();
include_once "database.php";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $borrowing_id = $_POST['borrowing_id'];
    $book_id      = $_POST['book_id'];
    $currentDate  = date("Y-m-d H:i:s");

    
    $sql = "UPDATE borrow_history SET status = 'returned', return_date = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $currentDate, $borrowing_id);

    if (mysqli_stmt_execute($stmt)) {
       
        $sql2 = "UPDATE books SET status = 'available' WHERE id = ?";
        $stmt2 = mysqli_prepare($conn, $sql2);
        mysqli_stmt_bind_param($stmt2, "i", $book_id);
        mysqli_stmt_execute($stmt2);

       
        $redirect = $_SERVER['HTTP_REFERER'] ?? 'home.php';
        header("Location: $redirect");
        exit;
    } else {
        $redirect = $_SERVER['HTTP_REFERER'] ?? 'home.php';
        header("Location: $redirect?error=Error returning book");
        exit;
    }
}

