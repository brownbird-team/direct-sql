<!DOCTYPE html>
<html lang="en">
<?php
    require __DIR__ .'/Nav_bar.php';
?>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/accounts.css"/>
    <link rel="stylesheet" href="../public/css/nav_bar.css"/>
    <link rel="stylesheet" href="../public/css/sidebar.css"/>
    <link rel="stylesheet" href="../public/css/responsive.css"/>
    <title>Accounts</title>
</head>
<body>
    <div class="account_options">
        <form>
            <div class="acc_container">
                <h1>Sign up</h1>
                <label for="e-mail">E-mail</label>
                <input name="e-mail" placeholder="Enter your e-mail: "/>
                <label>Username</label>
                <input placeholder="Username: "/>
                <label>Password</label>
                <input placeholder="Password: "/>
                <label>Confirm Password</label>
                <input placeholder="Password: "/>
                <button type="submit">Submit
            </div>
        </form>
        <form>
            <div class="acc_container">
                <h1>Log in</h1>
                <label>Username</label>
                <input placeholder="Username: "/>
                <label>Password</label>
                <input placeholder="Password: "/>
                <button type="submit">Submit
            </div>
        </form>
    </div>
</body>
</html>