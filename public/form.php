<?php
require_once "../config/db.php";

// --------------------
// CONFIGURACIÓN
// --------------------
$cursoActual = '2026/2027';

// --------------------
// OBTENER ALUMNOS DEL CURSO ACTUAL
// --------------------
$stmt = $pdo->prepare("
    SELECT id, nombre, apellido
    FROM alumnos
    WHERE curso = :curso
    ORDER BY nombre, apellido
");

$stmt->execute([
    ':curso' => $cursoActual
]);

$alumnos = $stmt->fetchAll();
?>

<h1>Añadir puntos</h1>

<form action="addpoints.php" method="post">
    <select name="id" required>
        <?php foreach ($alumnos as $a): ?>
            <option value="<?= $a['id'] ?>">
                <?= htmlspecialchars($a['nombre'] . " " . $a['apellido']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="number" name="puntos" min="1" required>
    <button type="submit">Añadir</button>
</form>
