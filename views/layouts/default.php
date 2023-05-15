<html>
    <head>
        <?php \AssetLoader\CSS::print_links(); ?>
    </head>
    <body>
        <?php require __DIR__ . '/../partials/navbar.php'; ?>
        <?php require __DIR__ . '/../partials/sidebar.php'; ?>

        <?php echo $content; ?>

        <?php \AssetLoader\JS::print_links(); ?>
    </body>
</html>