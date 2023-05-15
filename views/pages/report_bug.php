<!DOCTYPE html>
<html lang="en">
<?php
    require __DIR__ .'/Nav_bar.php';
    require __DIR__ .'/Sidebar.php';
?>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/report_bug.css"/>
    <link rel="stylesheet" href="../public/css/nav_bar.css"/>
    <link rel="stylesheet" href="../public/css/sidebar.css"/>
    <link rel="stylesheet" href="../public/css/responsive.css"/>
    <title>Report bug</title>
</head>
<body>
    <div class="report-container">
        <h3>Report title</h3>
        <textarea cols="100" rows="1" placeholder="Title of your report:"></textarea>
        <h3>Report description</h3>
        <textarea cols="100" rows="20" placeholder="Explenation of your report:"></textarea>
        <button>Send</button>
    </div>
</body>
</html>