<?php
session_start();
include_once "database.php";

$error = "";
$success = "";


$book = null;
if (isset($_POST['choose_id'])) {
    $choose_id = (int)$_POST['choose_id'];
    $stmt = $conn->prepare("SELECT * FROM book WHERE id=?");
    $stmt->bind_param("i", $choose_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
    $stmt->close();
}

if (isset($_POST['save_changes'])) {
    $id     = (int)$_POST['id'];
    $title  = trim($_POST['title']);
    $author = trim($_POST['author']);
    $isbn   = trim($_POST['isbn']);
    $genre  = trim($_POST['genre']);
    $status = trim($_POST['status']);

    if ($title=="" || $author=="" || $isbn=="" || $genre=="" || $status=="") {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("UPDATE book SET title=?, author=?, isbn=?, genre=?, status=? WHERE id=?");
        $stmt->bind_param("sssssi", $title, $author, $isbn, $genre, $status, $id);
        if ($stmt->execute()) {
            $success = "Book updated successfully!";
        } else {
            $error = "Error updating book: " . $conn->error;
        }
        $stmt->close();
        $book = null; 
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Books</title>
<style>
body { font-family: Arial, sans-serif; margin: 40px; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
th { background: #f4f4f4; }
button { padding: 4px 10px; }
.error { color: red; }
.success { color: green; }
form.inline { display:inline; margin:0; }
form.edit-form { max-width: 400px; margin: 20px auto; }
form.edit-form input[type=text], form.edit-form select {
    width: 100%; padding: 8px; margin: 5px 0 15px 0;
}
form.edit-form input[type=submit] { padding: 10px 20px; }
</style>
</head>
<body>

<h2>Manage Books</h2>

<?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<?php if ($success): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>

<!-- Table of books -->
<table>
    <tr>
        <th>ID</th><th>Title</th><th>Author</th>
        <th>ISBN</th><th>Genre</th><th>Status</th><th>Action</th>
    </tr>
<?php
$result = $conn->query("SELECT * FROM book ORDER BY id ASC");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>".htmlspecialchars($row['id'])."</td>
                <td>".htmlspecialchars($row['title'])."</td>
                <td>".htmlspecialchars($row['author'])."</td>
                <td>".htmlspecialchars($row['isbn'])."</td>
                <td>".htmlspecialchars($row['genre'])."</td>
                <td>".htmlspecialchars($row['status'])."</td>
                <td>
                   <form class='inline' method='post'>
                      <input type='hidden' name='choose_id' value='".$row['id']."'>
                      <button type='submit'>Edit</button>
                   </form>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='7'>No books found.</td></tr>";
}
?>
</table>

<?php if ($book): ?>
<form method="post" class="edit-form">
    <h3>Edit Book ID <?= htmlspecialchars($book['id']) ?></h3>
    <input type="hidden" name="id" value="<?= htmlspecialchars($book['id']) ?>">

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

    <input type="submit" name="save_changes" value="Save Changes">
</form>
<?php endif; ?>

</body>
</html>
