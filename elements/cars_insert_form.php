<?php

require_once __DIR__ . '/../functions/database.php';

$cars_table = $conn -> query('SELECT name, description, price, image, color FROM cars');
$fields = $cars_table -> fetch_fields();

$insert_error = '';

$request_method = strtoupper($_SERVER['REQUEST_METHOD']);

if ($request_method === 'POST') {
    $query = 'INSERT INTO cars (';

    for ($i = 0; $i < count($fields); $i++) {
        $query .= $fields[$i] -> name;
        if ($i < count($fields) - 1)
            $query .= ',';
    }

    $query .= ') VALUES (';

    for ($i = 0; $i < count($fields); $i++) {
        if (!empty($_FILES[$fields[$i] -> name]['name']) && $fields[$i] -> type == 252) {
            $image = $_FILES[$fields[$i] -> name]['tmp_name'];

            $img_content = $conn -> real_escape_string(file_get_contents($image));
            $query .= "'" . $img_content . "'";

            if ($i < count($fields) - 1)
                $query .= ',';

        } else if (!empty($_POST[$fields[$i] -> name])) {
            $query .= "'" . $_POST[$fields[$i] -> name] . "'";

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
        echo '<div> <label for="'. $field -> name .'">'. $field -> name .':</label>';
        if ($field -> type === 252)
            echo '<input type="file" name="'. $field -> name .'">';
        else
            echo '<input type="text" name="'. $field -> name .'" placeholder="'. $field -> name .'">';
        echo '</div>';
    }
    ?>

    <input type="submit">
</form>