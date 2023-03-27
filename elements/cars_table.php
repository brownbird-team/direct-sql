<?php
include __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/database.php';

$fields = [];

$car_res = $conn -> query('SELECT * FROM cars');
while ($field = $car_res -> fetch_field()) {
    $fields[$field -> name] = $field;
}

?>
<div class="table-container">
    <table>
        <tr>
            <?php foreach ($config['table_fields'] as $field)
                echo '<th>'. $field .'</th>';
            ?>
        </tr>
        
        <?php while ($car = $car_res -> fetch_assoc()) {
        
            echo '<tr>';

            for ($i = 0; $i < count($config['table_fields']); $i++) {
                if ($fields[$config['table_fields'][$i]] -> type == 252) {
                    echo '<td><img alt="image" src="data:image/jpg;charset=utf8;base64,'. base64_encode($car[$config['table_fields'][$i]]) .'"></td>';
                }
                else
                    echo '<td>'. $car[$config['table_fields'][$i]] .'</td>';
            }

            echo '</tr>';
            
        } ?>

        <?php if ($car_res -> num_rows == 0): ?>
            <tr>
                <td colspan="<?php echo $car_res -> field_count ?>">No data to display</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

