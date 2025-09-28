<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once "database.php";

<<<<<<< HEAD
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $isbn = trim($_POST['isbn']);
    $genre = trim($_POST['genre']);
=======
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
>>>>>>> feature/add-book
    $publication_year = trim($_POST['publication_year']);

    if (empty($title) || empty($author) || empty($isbn) || empty($genre) || empty($publication_year)) {
        $error = "All fields are required.";
    } elseif (!is_numeric($publication_year) || $publication_year <= 1000) {
        $error = "Publication year must be greater than 1000.";
<<<<<<< HEAD
=======
    } elseif (!in_array($genre, $genres)) {
        $error = "Please select a valid genre.";
>>>>>>> feature/add-book
    } else {
        $status = "available";

        $stmt = $conn->prepare("INSERT INTO books (title, author, isbn, genre, publication_year, status) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssssis", $title, $author, $isbn, $genre, $publication_year, $status);

            if ($stmt->execute()) {
<<<<<<< HEAD
                $success = "Book added successfully and is available!";
=======
                $success = "✅ Book added successfully and is available!";
                // Clear form after success
                $title = $author = $isbn = $genre = $publication_year = "";
>>>>>>> feature/add-book
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
<<<<<<< HEAD
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
=======
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
>>>>>>> feature/add-book
</body>
</html>
