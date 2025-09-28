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
    $publication_year = trim($_POST['publication_year']);

    if (eWmpty($title) || empty($author) || empty($isbn) || empty($genre) || empty($publication_year)) {
        $error = "All fields are required.";
    } elseif (!is_numeric($publication_year) || $publication_year <= 1000) {
        $error = "Publication year must be greater than 1000.";
    } else {
        $status = "available";

        $stmt = $conn->prepare("INSERT INTO books (title, author, isbn, genre, publication_year, status) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssssis", $title, $author, $isbn, $genre, $publication_year, $status);

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
        input[type=text], input[type=number], select { width: 100%; padding: 8px; margin: 5px 0 15px 0; }
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
        <select name="genre" required>
            <option value="">-- Select Genre --</option>
            <option value="Fiction">Fiction</option>
            <option value="Science Fiction">Science Fiction</option>
            <option value="Romance">Romance</option>
            <option value="Adventure">Adventure</option>
            <option value="Drama">Drama</option>
            <option value="Historical Fiction">Historical Fiction</option>
            <option value="Philosophical Fiction">Philosophical Fiction</option>
        </select>

        <label>Publication Year:</label>
        <input type="number" name="publication_year" min="1001" required>

        <input type="submit" value="Add Book">
    </form>
</body>
</html>
