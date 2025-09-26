<?php
session_start();
include_once "database.php";


if($_SERVER['REQUEST_METHOD'] === "POST"){
    $borrowing_id = $_POST['borrowing_id'];
    $book_id = $_POST['book_id'];
    $currentDate = date("Y-m-d H:i:s"); 

    $sql = "UPDATE borrow_history set status = 'Returned' , returned_date = ? where id = ?";
    $stmt = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt,"si",$currentDate,$borrowing_id);
    if(mysqli_execute($stmt)){
        $sql = "UPDATE books set status = 'Available' where id = ?";
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt,"i",$book_id);
        mysqli_execute($stmt);
        header("Location: return_book.php?success=Book return successfully");
    }else{
        header("Location: return_book.php?error in returing book");
        exit;
    }

}

$user_id = $_SESSION['user_id'];
$sql = "SELECT MAX(bh.id) as borrowing_id ,b.id as book_id,b.title,b.author,b.status as status from borrow_history bh
        JOIN books b on b.id = bh.book_id
        where bh.user_id = $user_id and b.status = 'Borrowed' 
        Group by b.id,b.title,b.author,b.status
        ";

$result = mysqli_query($conn,$sql);
$books = mysqli_fetch_all($result,MYSQLI_ASSOC);



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    

    <button onclick="window.location.href='index.php'"><- Back</button>

   <?php if (isset($_GET['success'])): ?>
        <p style="color: green;"><?php echo htmlspecialchars($_GET['success']); ?></p>
    <?php elseif (isset($_GET['error'])): ?>
        <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>

    <?php if(!empty($books)):?>
        <table border="1" cellspacing="5">
            <tr>
                <th>Book Title</th>
                <th>Author</th>
                <th>Status</th>
            </tr>
            <?php foreach($books as $book):?>
                <tr>
                    <td><?php echo htmlspecialchars($book['title'])?> </td>
                    <td><?php echo htmlspecialchars($book['author'])?> </td>
                    <td><?php echo htmlspecialchars($book['status'])?>
                        <form method="post" onsubmit="return confirm('Are you sure you want to return this book')" style="display: inline;">
                            <input type="hidden" name="book_id" value="<?php echo $book['book_id']?>">
                            <input type="hidden" name="borrowing_id" value="<?php echo $book['borrowing_id']?>">
                            <button type="submit" >Return</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach?>
        </table>
    <?php else:?>
        <h2>No books are currently borrowed</h2>
    <?php endif?>


</body>
</html>