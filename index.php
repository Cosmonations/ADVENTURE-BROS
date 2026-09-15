<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $_SESSION['first_name'] = trim($_POST['first_name']);
    $_SESSION['last_name']  = trim($_POST['last_name']);
    $_SESSION['email']      = trim($_POST['email']);
    $_SESSION['phone']      = trim($_POST['phone']);

    if (!isset($_SESSION['lives'])) {
        $_SESSION['lives'] = 3;
    }

    header("Location: game.php");
    exit;
}

if (isset($_SESSION['first_name'])) {
    header("Location: game.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ADVENTURE BROS - Login</title>
    <style>
        body { font-family: sans-serif; background: #111; color: white; text-align: center; padding-top: 50px; }
        .box { background: #222; padding: 30px; border-radius: 10px; width: 400px; margin: auto; }
        input { width: 90%; padding: 10px; margin: 10px 0; border-radius: 4px; border: 1px solid #ccc; box-sizing: border-box; }
        button { padding: 10px; margin: 10px 0; cursor: pointer; font-weight: bold; border-radius: 5px; border: none; width: 100%; box-sizing: border-box; }
        .btn-login { background-color: #4CAF50; color: white; }
    </style>
</head>
<body>

<div class="box">
    <h2>Welcome to ADVENTURE BROS</h2>
    <p>Enter your details to start playing.</p>
    
    <form action="index.php" method="POST">
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="tel" name="phone" placeholder="Phone Number" required>
        <button type="submit" name="login" class="btn-login">Start Game</button>
    </form>
</div>

</body>
</html>