<?php

require_once __DIR__ . '/../functions/database.php';

$car_res = $conn -> query('SELECT * FROM cars');
$fields = $car_res -> fetch_fields();

?>
<div class="table-container">
    <table>
        <tr>
            <?php foreach ($fields as $field)
                echo '<th>'. $field -> name .'</th>';
            ?>
        </tr>
        
        <?php while ($car = $car_res -> fetch_assoc()) {
        
            echo '<tr>';

            for ($i = 0; $i < count($fields); $i++) {
                if ($fields[$i] -> type == 252)
                    echo '<td><img alt="image" src="data:image/jpg;charset=utf8;base64,'. base64_encode($car[$fields[$i] -> name]) .'"></td>';
                else
                    echo '<td>'. $car[$fields[$i] -> name] .'</td>';
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

