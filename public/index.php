<?php
// --------------------
// CONEXIÓN BD
// --------------------
$host = getenv("DB_HOST");
$dbname = getenv("DB_NAME");
$user = getenv("DB_USER");
$password = getenv("DB_PASS");

try {
    $pdo = new PDO(
        "pgsql:host=$host;dbname=$dbname",
        $user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Error de conexión");
}

// --------------------
// OBTENER CLASES
// --------------------
$clases = $pdo->query("SELECT DISTINCT clase FROM alumnos ORDER BY clase")->fetchAll(PDO::FETCH_COLUMN);

// --------------------
// FILTRO
// --------------------
$claseSeleccionada = $_GET['clase'] ?? '';

if ($claseSeleccionada) {
    $stmt = $pdo->prepare(
        "SELECT nombre, apellido, puntos, clase 
         FROM alumnos 
         WHERE clase = :clase 
         ORDER BY puntos DESC"
    );
    $stmt->execute(['clase' => $claseSeleccionada]);
} else {
    $stmt = $pdo->query(
        "SELECT nombre, apellido, puntos, clase 
         FROM alumnos 
         ORDER BY puntos DESC"
    );
}

$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="UTF-8">
<title>Clasificación Ajedrez</title>

<style>
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #f4f6f8;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 900px;
    margin: 40px auto;
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

h1 {
    text-align: center;
    margin-bottom: 25px;
}

form {
    margin-bottom: 20px;
    text-align: center;
}

select {
    padding: 10px 15px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 16px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #2c3e50;
    color: white;
    padding: 12px;
    text-align: left;
}

td {
    padding: 10px;
    border-bottom: 1px solid #eee;
}

tr:hover {
    background: #f1f1f1;
}

.puntos {
    font-weight: bold;
    color: #27ae60;
}

.footer {
    text-align: center;
    margin-top: 20px;
    color: #888;
    font-size: 14px;
}
/* =========================
   RESPONSIVE
   ========================= */
@media (max-width: 768px) {

    .container {
        padding: 15px;
        margin: 15px;
    }

    table {
        font-size: 14px;
    }

    th, td {
        padding: 6px;
    }

    input[type="number"] {
        width: 60px;
    }

    button {
        padding: 8px;
        font-size: 16px;
    }

    h1 {
        font-size: 22px;
    }
}
</style>
</head>

<body>
<div class="container">

<h1>🏆 Clasificación de Ajedrez</h1>

<form method="get">
    <select name="clase" onchange="this.form.submit()">
        <option value="">Todas las clases</option>
        <?php foreach ($clases as $clase): ?>
            <option value="<?= htmlspecialchars($clase) ?>"
                <?= $clase === $claseSeleccionada ? 'selected' : '' ?>>
                <?= htmlspecialchars($clase) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<table>
    <tr>
        <th>#</th>
        <th>Alumno</th>
        <th>Clase</th>
        <th>Puntos</th>
    </tr>

    <?php $pos = 1; ?>
    <?php foreach ($alumnos as $a): ?>
    <tr>
        <td><?= $pos++ ?></td>
        <td><?= htmlspecialchars($a['nombre'] . ' ' . $a['apellido']) ?></td>
        <td><?= htmlspecialchars($a['clase']) ?></td>
        <td class="puntos"><?= $a['puntos'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<div class="footer">
    WebPuntosAjedrez · Proyecto educativo
</div>

</div>
</body>
</html>




