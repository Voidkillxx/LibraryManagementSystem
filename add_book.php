<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once "database.php";

$error = "";
$success = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $isbn = trim($_POST['isbn']);
    $genre = trim($_POST['genre']);

    if (empty($title) || empty($author) || empty($isbn) || empty($genre)) {
        $error = "All fields are required.";
    } else {
        $status = "available";

        $stmt = $conn->prepare("INSERT INTO book (title, author, isbn, genre, status) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sssss", $title, $author, $isbn, $genre, $status);

            if ($stmt->execute()) {
                $success = "Book added successfully and is available!";
            } else {
                $error = ($conn->errno == 1062) 
                    ? "A book with this ISBN already exists." 
                    : "Error adding book: " . $conn->error;
            }

            $stmt->close(); 
        } else {
            $error = "Failed to prepare statement: " . $conn->error;
        }
    }
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Book</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        form { max-width: 400px; margin: auto; }
        input[type=text] { width: 100%; padding: 8px; margin: 5px 0 15px 0; }
        input[type=submit] { padding: 10px 20px; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h2>Add New Book</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label>Title:</label>
        <input type="text" name="title" required>

        <label>Author:</label>
        <input type="text" name="author" required>

        <label>ISBN:</label>
        <input type="text" name="isbn" required>

        <label>Genre:</label>
        <input type="text" name="genre" required>

        <input type="submit" value="Add Book">
    </form>
</body>
</html>
<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once "database.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $isbn = trim($_POST['isbn']);
    $genre = trim($_POST['genre']);

    if (empty($title) || empty($author) || empty($isbn) || empty($genre)) {
        $error = "All fields are required.";
    } else {
        $status = "available";

        $stmt = $conn->prepare("INSERT INTO book (title, author, isbn, genre, status) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sssss", $title, $author, $isbn, $genre, $status);

            if ($stmt->execute()) {
                $success = "Book added successfully and is available!";
            } else {
                $error = ($conn->errno == 1062) 
                    ? "A book with this ISBN already exists." 
                    : "Error adding book: " . $conn->error;
            }

            $stmt->close(); 
        } else {
            $error = "Failed to prepare statement: " . $conn->error;
        }
    }
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Book</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        form { max-width: 400px; margin: auto; }
        input[type=text] { width: 100%; padding: 8px; margin: 5px 0 15px 0; }
        input[type=submit] { padding: 10px 20px; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h2>Add New Book</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label>Title:</label>
        <input type="text" name="title" required>

        <label>Author:</label>
        <input type="text" name="author" required>

        <label>ISBN:</label>
        <input type="text" name="isbn" required>

        <label>Genre:</label>
        <input type="text" name="genre" required>

        <input type="submit" value="Add Book">
    </form>
</body>
</html>
