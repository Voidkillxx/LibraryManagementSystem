<?php
session_start();
include_once "database.php";

if (!isset($_GET['id'])) {
    header("Location: index.php?error=No+book+ID+provided");
    exit;
}

$book_id = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
$book   = $result->fetch_assoc();
$stmt->close();

if (!$book) {
    header("Location: index.php?error=Book+not+found");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title  = trim($_POST['title']);
    $author = trim($_POST['author']);
    $isbn   = trim($_POST['isbn']);
    $genre  = trim($_POST['genre']);
    $status = trim($_POST['status']);

    if (!empty($title) && !empty($author) && !empty($isbn) && !empty($genre) && !empty($status)) {
        $stmt = $conn->prepare("UPDATE books SET title=?, author=?, isbn=?, genre=?, status=? WHERE id=?");
        $stmt->bind_param("sssssi", $title, $author, $isbn, $genre, $status, $book_id);

        if ($stmt->execute()) {
            header("Location: index.php?success=Book+updated+successfully");
            exit;
        } else {
            header("Location: index.php?error=Error+updating+book");
            exit;
        }

        $stmt->close();
    } else {
        header("Location: index.php?error=All+fields+are+required");
        exit;
    }
}

include_once "header.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Book</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        form {
            width: 100%;
        }
        label {
            font-weight: bold;
        }
        input[type=text], select {
            width: 100%;
            padding: 10px;
            margin: 8px 0 16px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type=submit] {
            padding: 10px 20px;
            background: #2c3e50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type=submit]:hover {
            background: #1a242f;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Book</h2>
        <form method="post">
            <label>Title:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" required>

            <label>Author:</label>
            <input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>" required>

            <label>ISBN:</label>
            <input type="text" name="isbn" value="<?= htmlspecialchars($book['isbn']) ?>" required>

            <label>Genre:</label>
            <input type="text" name="genre" value="<?= htmlspecialchars($book['genre']) ?>" required>

            <label>Status:</label>
            <select name="status" required>
                <option value="available" <?= $book['status'] === 'available' ? 'selected' : '' ?>>Available</option>
                <option value="borrowed"  <?= $book['status'] === 'borrowed'  ? 'selected' : '' ?>>Borrowed</option>
            </select>

            <input type="submit" value="Save Changes">
        </form>
    </div>
</body>
</html>
