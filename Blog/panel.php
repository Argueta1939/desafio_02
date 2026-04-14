<?php
require_once 'config/database.php';

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$mensaje = '';
$error = '';
$articulo_editar = null;

// Agregar artículo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    $titulo = trim($_POST['titulo']);
    $contenido = trim($_POST['contenido']);
    
    if (empty($titulo) || empty($contenido)) {
        $error = "El título y contenido son obligatorios.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO articulos (usuario_id, titulo, contenido) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['usuario_id'], $titulo, $contenido]);
        $mensaje = "Artículo agregado correctamente.";
    }
}

// Actualizar artículo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    $articulo_id = $_POST['articulo_id'];
    $titulo = trim($_POST['titulo']);
    $contenido = trim($_POST['contenido']);
    
    if (empty($titulo) || empty($contenido)) {
        $error = "El título y contenido son obligatorios.";
    } else {
        $stmt = $pdo->prepare("UPDATE articulos SET titulo = ?, contenido = ? WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$titulo, $contenido, $articulo_id, $_SESSION['usuario_id']]);
        $mensaje = "Artículo actualizado correctamente.";
    }
}

// Eliminar artículo
if (isset($_GET['eliminar'])) {
    $stmt = $pdo->prepare("DELETE FROM articulos WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$_GET['eliminar'], $_SESSION['usuario_id']]);
    $mensaje = "Artículo eliminado correctamente.";
}

// Obtener artículo para editar
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM articulos WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$_GET['editar'], $_SESSION['usuario_id']]);
    $articulo_editar = $stmt->fetch();
}

// Listar artículos
$stmt = $pdo->prepare("SELECT * FROM articulos WHERE usuario_id = ? ORDER BY fecha_creacion DESC");
$stmt->execute([$_SESSION['usuario_id']]);
$articulos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control - Blog Personal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>✍️ Panel de Edición de Artículos</h1>
        <p>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre_completo']); ?></strong></p>
        
        <?php if ($mensaje): ?>
            <div class="success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <!-- Formulario para agregar/editar -->
        <form method="POST">
            <h3><?php echo $articulo_editar ? 'Editar Artículo' : 'Nuevo Artículo'; ?></h3>
            
            <?php if ($articulo_editar): ?>
                <input type="hidden" name="articulo_id" value="<?php echo $articulo_editar['id']; ?>">
            <?php endif; ?>
            
            <input type="text" name="titulo" placeholder="Título del artículo" 
                   value="<?php echo $articulo_editar ? htmlspecialchars($articulo_editar['titulo']) : ''; ?>" required>
            <textarea name="contenido" placeholder="Contenido del artículo..." rows="6" required><?php 
                echo $articulo_editar ? htmlspecialchars($articulo_editar['contenido']) : ''; 
            ?></textarea>
            
            <?php if ($articulo_editar): ?>
                <button type="submit" name="editar">Actualizar Artículo</button>
                <a href="panel.php" style="display: inline-block; margin-left: 10px;">Cancelar</a>
            <?php else: ?>
                <button type="submit" name="agregar">Publicar Artículo</button>
            <?php endif; ?>
        </form>
        
        <hr style="margin: 30px 0;">
        
        <h2>Mis Artículos (<?php echo count($articulos); ?>)</h2>
        
        <?php if (empty($articulos)): ?>
            <p style="color: #666;">Aún no has escrito ningún artículo. ¡Comienza ahora!</p>
        <?php else: ?>
            <?php foreach ($articulos as $articulo): ?>
                <div class="articulo">
                    <h3><?php echo htmlspecialchars($articulo['titulo']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars(substr($articulo['contenido'], 0, 200))); ?>...</p>
                    <small>Publicado: <?php echo date('d/m/Y H:i', strtotime($articulo['fecha_creacion'])); ?></small>
                    <br>
                    <a href="panel.php?editar=<?php echo $articulo['id']; ?>">✏️ Editar</a>
                    <a href="panel.php?eliminar=<?php echo $articulo['id']; ?>" 
                       class="btn-eliminar" 
                       onclick="return confirm('¿Estás seguro de eliminar este artículo?')">Eliminar</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <p style="margin-top: 30px; text-align: center;">
            <a href="bienvenida.php">← Volver al inicio</a> | 
            <a href="logout.php">Cerrar sesión</a>
        </p>
    </div>
</body>
</html>