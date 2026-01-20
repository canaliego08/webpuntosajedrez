<?php
session_start();

/* =========================
   CONFIGURACIÓN CONTRASEÑA
   ========================= */
define('ADMIN_PASSWORD', 'ajedrez123'); // 🔴 CAMBIA ESTA CONTRASEÑA

/* =========================
   LOGIN
   ========================= */
if (isset($_POST['password'])) {
    if ($_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['admin'] = true;
    } else {
        $error = "Contraseña incorrecta";
    }
}

/* =========================
   LOGOUT
   ========================= */
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

/* =========================
   BLOQUEO DE ACCESO
   ========================= */
if (!isset($_SESSION['admin'])):
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Acceso administrador</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #eef1f5;
}
.login {
    max-width: 350px;
    margin: 120px auto;
    background: white;
    padding: 30px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 8px 20px rgba(0,0,0,.15);
}
input {
    padding: 10px;
    width: 100%;
    margin: 10px 0;
}
button {
    padding: 10px;
    width: 100%;
    background: #2c3e50;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
.error {
    color: red;
}
</style>
</head>
<body>

<div class="login">
    <h2>🔐 Acceso admin</h2>

    <?php if (isset($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="password" name="password" placeholder="Contraseña" required>
        <button>Entrar</button>
    </form>
</div>

</body>
</html>
<?php
exit;
endif;

/* =========================
   CONEXIÓN BASE DE DATOS
   ========================= */
$host = getenv("DB_HOST");
$dbname = getenv("DB_NAME");
$user = getenv("DB_USER");
$password = getenv("DB_PASS");

try {
    $pdo = new PDO(
       "pgsql:host=$host;dbname=$dbname;sslmode=require",
       $user,
       $password,
       [
           PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
           PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
       ]
   );

} catch (PDOException $e) {
    die("Error de conexión");
}

/* =========================
   SUMAR / RESTAR PUNTOS
   ========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $id = (int)$_POST['id'];
    $puntos = (int)$_POST['puntos'];
    $accion = $_POST['accion'];

    if ($puntos > 0) {
        if ($accion === 'sumar') {
            $sql = "UPDATE alumnos
                    SET puntos = puntos + :puntos
                    WHERE id = :id";
        } else {
            $sql = "UPDATE alumnos
                    SET puntos = GREATEST(puntos - :puntos, 0)
                    WHERE id = :id";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'puntos' => $puntos,
            'id' => $id
        ]);
    }
}

/* =========================
   CLASES DISPONIBLES
   ========================= */
$clases = $pdo->query(
    "SELECT DISTINCT clase FROM alumnos ORDER BY clase"
)->fetchAll(PDO::FETCH_COLUMN);

/* =========================
   FILTRO
   ========================= */
$claseSeleccionada = $_GET['clase'] ?? '';

if ($claseSeleccionada) {
    $stmt = $pdo->prepare(
        "SELECT id, nombre, apellido, puntos, clase
         FROM alumnos
         WHERE clase = :clase
         ORDER BY nombre"
    );
    $stmt->execute(['clase' => $claseSeleccionada]);
} else {
    $stmt = $pdo->query(
        "SELECT id, nombre, apellido, puntos, clase
         FROM alumnos
         ORDER BY clase, nombre"
    );
}

$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Gestión de puntos</title>

<style>
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #eef1f5;
}

.container {
    max-width: 950px;
    margin: 40px auto;
    background: white;
    padding: 30px;
    border-radius: 14px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

h1 {
    text-align: center;
}

.logout {
    text-align: center;
    margin-bottom: 20px;
}

select {
    padding: 10px;
    border-radius: 8px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #34495e;
    color: white;
    padding: 10px;
}

td {
    padding: 8px;
    border-bottom: 1px solid #ddd;
}

input[type="number"] {
    width: 70px;
    padding: 6px;
}

button {
    padding: 6px 10px;
    border: none;
    border-radius: 6px;
    color: white;
    cursor: pointer;
}

.btn-sumar {
    background: #27ae60;
}

.btn-restar {
    background: #c0392b;
}

.puntos {
    font-weight: bold;
}
</style>
</head>

<body>
<div class="container">

<h1>➕➖ Gestión de puntos</h1>

<div class="logout">
    <a href="?logout=1">🚪 Cerrar sesión</a>
</div>

<form method="get" style="text-align:center; margin-bottom:20px;">
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
    <th>Alumno</th>
    <th>Clase</th>
    <th>Puntos</th>
    <th>Acción</th>
</tr>

<?php foreach ($alumnos as $a): ?>
<tr>
    <td><?= htmlspecialchars($a['nombre'].' '.$a['apellido']) ?></td>
    <td><?= htmlspecialchars($a['clase']) ?></td>
    <td class="puntos"><?= $a['puntos'] ?></td>
    <td>
        <form method="post" style="display:flex; gap:6px;">
            <input type="hidden" name="id" value="<?= $a['id'] ?>">
            <input type="number" name="puntos" min="1" required>
            <button class="btn-sumar" name="accion" value="sumar">➕</button>
            <button class="btn-restar" name="accion" value="restar">➖</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>

</div>
</body>
</html>
