<?php
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];

    if (empty($login) || empty($password)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        $stmt = $pdo->prepare("SELECT id, nombre_completo, password FROM usuarios WHERE username = ? OR email = ?");
        $stmt->execute([$login, $login]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre_completo'] = $usuario['nombre_completo'];

            $update = $pdo->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?");
            $update->execute([$usuario['id']]);

            header("Location: bienvenida.php");
            exit();
        } else {
            $error = "Credenciales incorrectas.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Blog Personal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <form method="POST">
        <h1>🔐 Iniciar Sesión</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <input type="text" name="login" placeholder="Usuario o correo" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Ingresar</button>
        
        <p style="text-align: center; margin-top: 20px;">
            ¿No tienes cuenta? <a href="registro.php">Regístrate</a>
        </p>
    </form>
</body>
</html>