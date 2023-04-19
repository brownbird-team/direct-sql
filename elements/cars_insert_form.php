<?php

require_once __DIR__ . '/../functions/database.php';

$fields = [];

$cars_table_fields = $conn -> query('SHOW FIELDS FROM cars WHERE extra NOT LIKE \'%AUTO_INCREMENT%\'');
while ($field = $cars_table_fields -> fetch_assoc())
    $fields[] = $field;

$insert_error = '';
$request_method = strtoupper($_SERVER['REQUEST_METHOD']);

if ($request_method === 'POST') {
    $query = 'INSERT INTO cars (';

    for ($i = 0; $i < count($fields); $i++) {
        $query .= $fields[$i]['Field'];
        if ($i < count($fields) - 1)
            $query .= ',';
    }

    $query .= ') VALUES (';

    for ($i = 0; $i < count($fields); $i++) {
        if (!empty($_FILES[$fields[$i]['Field']]['name']) && $fields[$i]['Type'] == 'longblob') {
            $image = $_FILES[$fields[$i]['Field']]['tmp_name'];

            $img_content = $conn -> real_escape_string(file_get_contents($image));
            $query .= "'" . $img_content . "'";

            if ($i < count($fields) - 1)
                $query .= ',';

        } else if (!empty($_POST[$fields[$i]['Field']])) {
            $query .= "'" . $_POST[$fields[$i]['Field']] . "'";

            if ($i < count($fields) - 1)
                $query .= ',';
        }
    }

    $query .= ')';

    try {
        $conn -> query($query);
    } catch (mysqli_sql_exception $e) {
        $insert_error = $e -> getMessage();
    }
}

?>

<p><?php echo $insert_error ?></p>

<form enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
    <?php
    foreach ($fields as $field) {
        echo '<div> <label for="'. $field['Field'] .'">'. $field['Field'] .':</label>';
        if ($field['Type'] == 'longblob')
            echo '<input type="file" name="'. $field['Field'] .'">';
        else
            echo '<input type="text" name="'. $field['Field'] .'" placeholder="'. $field['Field'] .'">';
        echo '</div>';
    }
    ?>

    <input type="submit">
    <script>
        const burger = document.querySelector('.burger');    
        const navLinks = document.querySelector('.nav-links');

        burger.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            burger.classList.toggle('active');
        });
    </script>
</form>