<?php

require __DIR__ . '/__test_inputs__/test.php';

?>

<h1 style="color: <?php echo $test_data['color'] ?>">Hello <?php echo $test_data['username'] ?></h1>

<?php

echo strtoupper('hello');
echo '<h2>Hello '. $test_data['username'] .' '. $test_data['color'] .' everyone !</h2>' ;
echo __DIR__;

?>

<style>
    p {
        padding: 2rem;
        color: white;
        background-color: red;
    }

</style>

<?php if (!empty($test_data['error'])) { ?>
    <p><?php echo $test_data['error'] ?? ''; ?></p>
<?php } ?>

<ul>

    <?php foreach ($test_data['fruits'] as $fruit): ?>

        <li><?php echo $fruit ?></li>

    <?php endforeach; ?>

</ul>

<ul>

    <?php foreach ($test_data['fruits'] as $fruit) { ?>

        <li><?php echo $fruit ?></li>

    <?php } ?>

</ul>

<ul>

    <?php foreach ($test_data['fruits'] as $fruit) {

        echo '<li>' . $fruit . '</li>';

    } ?>

</ul>

<?php require __DIR__ . '/fancy_header.php'; ?>