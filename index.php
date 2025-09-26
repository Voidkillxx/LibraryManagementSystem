<?php
    session_start();
    include_once "database.php";

    $search = $_GET['search'] ?? '';

    if ($_SESSION['role'] === 'User') {
       
        if ($search !== '') {
            $sql = "SELECT * FROM book WHERE status = 'Available' 
                    AND (title LIKE ? OR author LIKE ? OR isbn LIKE ?)";
            $stmt = mysqli_prepare($conn, $sql);
            $searchLike = "%$search%";
            mysqli_stmt_bind_param($stmt, "sss", $searchLike, $searchLike, $searchLike);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        } else {
            $sql = "SELECT * FROM book WHERE status = 'Available'";
            $result = mysqli_query($conn, $sql);
        }
    } else {
        
        if ($search !== '') {
            $sql = "SELECT * FROM book WHERE title LIKE ? OR author LIKE ? OR isbn LIKE ?";
            $stmt = mysqli_prepare($conn, $sql);
            $searchLike = "%$search%";
            mysqli_stmt_bind_param($stmt, "sss", $searchLike, $searchLike, $searchLike);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        } else {
            $sql = "SELECT * FROM book";
            $result = mysqli_query($conn, $sql);
        }
}

$books = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="logout.php" method="post" onclick="return confirm('Are you sure you want to logout?')">
        <button type="submit">Logout</button>
    </form>
    <?php if($_SESSION['role'] === "Librarian"):?>
        <button type="button" onclick="window.location.href='add_book.php'">Add book</button>
    <?php elseif($_SESSION['role'] === "User"):?>   
        <button type="button" onclick="window.location.href='return_book.php'">Return Book</button>
    <?php endif?>

    <form method="get"> 
        <input type="text" size="30" name="search" placeholder="Search Book, Author, ISBN" value="<?php echo $_GET['search'] ?? ''?>">
        <button type="submit">Search</button>
    </form>

    <?php if(!empty($books)) :?>
        <table border="1" cellspacing = "5" >
            <tr>
                <th>Book Title</th>
                <th>Author</th>
                <th>ISBN</th>
                <th>Status</th>
            </tr>
            <?php foreach($books as $book):?>
                <tr>
                    <td><?php echo $book['title']?></td>
                    <td><?php echo $book['author']?></td>
                    <td><?php echo $book['isbn']?></td>
                    <td><?php echo $book['status']?>
                        <?php if($_SESSION['role'] === "Librarian"):?>
                            <form method="post" action="delete_book.php" onsubmit="return confirm('Are you sure you want to delete this book') " style="display: inline;">
                                <input type="hidden" name="book_id" value="<?php echo $book['id']?>">
                                <button type="submit">Delete</button>
                            </form>
                        <?php elseif ($_SESSION['role'] === "User" && $book['status'] === "Available"): ?>
                            <form method="post" action="borrow_book.php" style="display:inline;">
                                <input type="hidden" name="book_id" value="<?php echo (int)$book['id'] ?>">
                                <button type="submit">Borrow</button>
                            </form>
                        
                        <?php endif;?>
                    </td>
                </tr>
            <?php endforeach?>
        </table>
    <?php else:?>
        <?php if($_GET['search'] !== ''): ?>
            <h2>Book with '<?php echo htmlspecialchars($_GET['search'])?>' not found</h2>
        <?php else:?>
            <h2>No book in library</h2>
        <?php endif?>
    <?php endif?>
</body>
</html>