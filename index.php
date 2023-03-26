<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="stlyes/main.css">
    <title>Hello Cars</title>
    <link rel='stylesheet' type='text/css' href='styles/main.css'/>
    <link rel='stylesheet' type='text/css' href='styles/responsive.css'/>
    <link rel='stylesheet' type='text/css' href='styles/insert_form.css'/>
    <link rel='stylesheet' type='text/css' href='styles/table.css'/>
</head>
<body>
    <iframe src="navigation_bar.php" width="100%" frameborder="0"></iframe>
    <img src="images/logo.png" class="logo">
    <div class="container">
        <h1>Cars Database</h1>

        <?php require __DIR__ . '/elements/cars_insert_form.php' ?>
        <?php require __DIR__ . '/elements/cars_table.php' ?>
    </div>
</body>
</html>