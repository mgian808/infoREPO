<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN</title>
    <link rel="stylesheet" href="\SkillSwap\style.css">
    
</head>
<body>
    <?php require_once 'header_home.php'; ?>
    <h1>Login Page</h1>
    <form action="login.php" method="post">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email"><br><br>
        <label for="psw">Password:</label>
        <input type="password" id="psw" name="psw"><br><br>
        <button type="submit">Submit</button>
    </form>
</body>
</html>