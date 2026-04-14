<?php
require_once 'config/database.php';

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_completo = trim($_POST['nombre_completo']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($nombre_completo) || empty($username) || empty($email) || empty($password)) {
        $error = "Todos los campos son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Correo electrónico inválido.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_completo, username, email, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nombre_completo, $username, $email, $password_hash]);
            $exito = "¡Registro exitoso! <a href='login.php'>Iniciar sesión aquí</a>";
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $error = "El nombre de usuario o correo ya existe.";
            } else {
                $error = "Error al registrar usuario. Intente nuevamente.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Blog Personal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <form method="POST">
        <h1>Registro de Autor</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($exito): ?>
            <div class="success"><?php echo $exito; ?></div>
        <?php endif; ?>
        
        <input type="text" name="nombre_completo" placeholder="Nombre completo" required>
        <input type="text" name="username" placeholder="Nombre de usuario" required>
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Registrarse</button>
        
        <p style="text-align: center; margin-top: 20px;">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
        </p>
    </form>
</body>
</html>