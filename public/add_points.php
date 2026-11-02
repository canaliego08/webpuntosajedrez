<?php
require_once "../config/db.php";

// --------------------
// CURSO ACTUAL
// --------------------
$cursoActual = '2026/2027';

// --------------------
// COMPROBAR DATOS
// --------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acceso no permitido");
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$puntos = filter_input(INPUT_POST, 'puntos', FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id <= 0 ||
    $puntos === false || $puntos === null || $puntos <= 0) {
    die("Datos incorrectos");
}

// --------------------
// AÑADIR PUNTOS
// --------------------
$stmt = $pdo->prepare("
    UPDATE alumnos
    SET puntos = puntos + :puntos
    WHERE id = :id
    AND curso = :curso
");

$stmt->execute([
    ':puntos' => $puntos,
    ':id' => $id,
    ':curso' => $cursoActual
]);

// --------------------
// VOLVER AL INDEX
// --------------------
header("Location: index.php");
exit;
