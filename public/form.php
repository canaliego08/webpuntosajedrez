<?php
require_once "../config/db.php";
$alumnos = $pdo->query("SELECT id, nombre, apellido FROM alumnos")->fetchAll();
?>

<h1>Añadir puntos</h1>

<form action="add_points.php" method="post">
    <select name="id" required>
        <?php foreach ($alumnos as $a): ?>
            <option value="<?= $a['id'] ?>">
                <?= $a['nombre'] . " " . $a['apellido'] ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="number" name="puntos" required>
    <button type="submit">Añadir</button>
</form>
