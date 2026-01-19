<?php
require_once "../config/db.php";

$id = (int)$_POST['id'];
$puntos = (int)$_POST['puntos'];

$stmt = $pdo->prepare("
    UPDATE alumnos
    SET puntos = puntos + :puntos
    WHERE id = :id
");

$stmt->execute([
    ':puntos' => $puntos,
    ':id' => $id
]);

header("Location: index.php");
