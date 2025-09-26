<?php
    
    session_start();
    include_once "database.php";
   

    if($_SERVER['REQUEST_METHOD'] === "POST"){
        $username = $_POST['username'];
        $password = mysqli_real_escape_string($conn, $_POST['password']);


        $sql = "SELECT id,role,password from users where username = ?";
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt,"s",$username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if($user){
            if($password === $user['password']){
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                header("Location: index.php?success=Login Successfully");
                exit;
            }else{
                 header("Location: login.php?error=Wrong password");
            }
        }else{
            header("Location: login.php?error=Account Invalid");
            exit;
        }
        

    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>