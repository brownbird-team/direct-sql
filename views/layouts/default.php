<html>
    <head>
        <?php CSS::print_links(); ?>
    </head>
    <body>
        <?php require __DIR__ . '/../partials/navbar.php'; ?>
        <?php require __DIR__ . '/../partials/sidebar.php'; ?>

        <?php echo $content; ?>

        <?php require __DIR__ . '/../partials/footer.php'; ?>

        <?php JS::print_links(); ?>
    </body>
</html>