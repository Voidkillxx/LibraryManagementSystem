<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once "database.php";

$error   = "";
$success = "";

$title = $author = $isbn = $genre = $publication_year = "";

$genres = [
    "Fiction",
    "Science Fiction",
    "Romance",
    "Adventure",
    "Drama",
    "Historical Fiction",
    "Philosophical Fiction"
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title            = trim($_POST['title']);
    $author           = trim($_POST['author']);
    $isbn             = trim($_POST['isbn']);
    $genre            = trim($_POST['genre']);
    $publication_year = trim($_POST['publication_year']);

    if (empty($title) || empty($author) || empty($isbn) || empty($genre) || empty($publication_year)) {
        $error = "All fields are required.";
    } elseif (!is_numeric($publication_year) || $publication_year <= 1000) {
        $error = "Publication year must be greater than 1000.";
    } elseif (!in_array($genre, $genres)) {
        $error = "Please select a valid genre.";
    } else {
        $status = "available";

        $stmt = $conn->prepare("INSERT INTO books (title, author, isbn, genre, publication_year, status) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssssis", $title, $author, $isbn, $genre, $publication_year, $status);

            if ($stmt->execute()) {
                $success = "✅ Book added successfully and is available!";
                // Clear form after success
                $title = $author = $isbn = $genre = $publication_year = "";
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
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 420px;
            margin: 40px auto;
            background: #fff;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #555;
        }
        input[type=text], input[type=number], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        input[type=submit] {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            background-color: #28a745;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        input[type=submit]:hover {
            background-color: #218838;
        }
        .error {
            color: #b71c1c;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }
        .success {
            color: #1b5e20;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Book</h2>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <label>Title:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($title ?? '') ?>" required>

            <label>Author:</label>
            <input type="text" name="author" value="<?= htmlspecialchars($author ?? '') ?>" required>

            <label>ISBN:</label>
            <input type="text" name="isbn" value="<?= htmlspecialchars($isbn ?? '') ?>" required>

            <label>Genre:</label>
            <select name="genre" required>
                <option hidden value="">-- Select Genre --</option>
                <?php foreach ($genres as $g): ?>
                    <option value="<?= htmlspecialchars($g) ?>" <?= ($genre === $g) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Publication Year:</label>
            <input type="number" name="publication_year" min="1001" value="<?= htmlspecialchars($publication_year ?? '') ?>" required>

            <input type="submit" value="Add Book">
        </form>
    </div>
</body>
</html>
