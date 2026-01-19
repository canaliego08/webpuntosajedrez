<?php
require_once "../config/db.php";

$stmt = $pdo->query("
    SELECT nombre, apellido, clase, curso, puntos
    FROM alumnos
    ORDER BY puntos DESC
");
$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Clasificación de alumnos</h1>

<table border="1" cellpadding="5">
<tr>
    <th>Nombre</th>
    <th>Apellido</th>
    <th>Clase</th>
    <th>Curso</th>
    <th>Puntos</th>
</tr>

<?php foreach ($alumnos as $a): ?>
<tr>
    <td><?= htmlspecialchars($a['nombre']) ?></td>
    <td><?= htmlspecialchars($a['apellido']) ?></td>
    <td><?= htmlspecialchars($a['clase']) ?></td>
    <td><?= htmlspecialchars($a['curso']) ?></td>
    <td><?= $a['puntos'] ?></td>
</tr>
<?php endforeach; ?>
</table>



