<?php
require_once 'config/database.php';

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener datos del usuario
$stmt = $pdo->prepare("SELECT COUNT(*) FROM articulos WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$total_articulos = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT ultimo_login, fecha_registro FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario_data = $stmt->fetch();

$ultimo_login = $usuario_data['ultimo_login'];
$fecha_registro = $usuario_data['fecha_registro'];

// Obtener últimos 5 artículos
$stmt = $pdo->prepare("SELECT titulo, fecha_creacion FROM articulos WHERE usuario_id = ? ORDER BY fecha_creacion DESC LIMIT 5");
$stmt->execute([$_SESSION['usuario_id']]);
$ultimos_articulos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenida - Blog Personal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>✨ ¡Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre_completo']); ?>!</h1>
        
        <div class="stats">
            <h3>Tu actividad</h3>
            <p>Total de artículos escritos: <strong><?php echo $total_articulos; ?></strong></p>
            <p>Último inicio de sesión: <?php echo $ultimo_login ? date('d/m/Y H:i:s', strtotime($ultimo_login)) : 'Primera vez'; ?></p>
            <p>Miembro desde: <?php echo date('d/m/Y', strtotime($fecha_registro)); ?></p>
        </div>
        
        <?php if (count($ultimos_articulos) > 0): ?>
            <div class="stats">
                <h3>📖 Tus últimos artículos</h3>
                <ul style="margin-left: 20px;">
                    <?php foreach ($ultimos_articulos as $articulo): ?>
                        <li><?php echo htmlspecialchars($articulo['titulo']); ?> 
                            <small>(<?php echo date('d/m/Y', strtotime($articulo['fecha_creacion'])); ?>)</small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px; text-align: center;">
            <p><a href="panel.php" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 24px; border-radius: 8px; margin: 10px;">✍️ Panel de edición de artículos</a></p>
            <p><a href="logout.php" style="color: #e74c3c;">Cerrar sesión</a></p>
        </div>
    </div>
</body>
</html>