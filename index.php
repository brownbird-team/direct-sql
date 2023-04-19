<?php require __DIR__ . '/config/config.php' ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hello Cars</title>
    <link rel='stylesheet' type='text/css' href='styles/main.css'/>
    <link rel='stylesheet' type='text/css' href='styles/insert_form.css'/>
    <link rel='stylesheet' type='text/css' href='styles/table.css'/>
    <link rel='stylesheet' type='text/css' href='styles/nav_bar.css'/>
    <link rel='stylesheet' type='text/css' href='styles/responsive.css'/>
</head>
<body>
    <?php require __DIR__ . '/elements/navigation_bar.php' ?>

    <div class="container">
        <h1><?php echo ucwords($config['table_name']) ?> Table</h1>

        <?php require __DIR__ . '/elements/cars_insert_form.php' ?>
        <?php require __DIR__ . '/elements/cars_table.php' ?>
    </div>
</body>
</html>