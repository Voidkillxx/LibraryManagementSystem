<?php
session_start();
include_once "database.php";

$error = "";
$success = "";

if (!isset($_GET['id'])) {
    die("No book ID provided.");
}
$book_id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM book WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();
if (!$book) {
    die("Book not found.");
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title  = trim($_POST['title']);
    $author = trim($_POST['author']);
    $isbn   = trim($_POST['isbn']);
    $genre  = trim($_POST['genre']);
    $status = trim($_POST['status']);

    if (empty($title) || empty($author) || empty($isbn) || empty($genre) || empty($status)) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("UPDATE book SET title=?, author=?, isbn=?, genre=?, status=? WHERE id=?");
        $stmt->bind_param("sssssi", $title, $author, $isbn, $genre, $status, $book_id);

        if ($stmt->execute()) {
            $success = "Book updated successfully!";
            $book = ['title'=>$title,'author'=>$author,'isbn'=>$isbn,'genre'=>$genre,'status'=>$status];
        } else {
            $error = "Error updating book: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Book</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        form { max-width: 400px; margin: auto; }
        input[type=text], select { width: 100%; padding: 8px; margin: 5px 0 15px 0; }
        input[type=submit] { padding: 10px 20px; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
<h2>Edit Book</h2>

<?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<?php if ($success): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>

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
        <option value="available" <?= $book['status']==='available'?'selected':'' ?>>available</option>
        <option value="borrowed"  <?= $book['status']==='borrowed'?'selected':'' ?>>borrowed</option>
    </select>

    <input type="submit" value="Save Changes">
</form>
</body>
</html>
