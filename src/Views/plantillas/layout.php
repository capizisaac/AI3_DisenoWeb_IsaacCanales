<?php

declare(strict_types=1);

use Ordenalo\Views\Html;

/** @var string $titulo */
/** @var string $contenido HTML ya generado por otra plantilla (de confianza). */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::e($titulo) ?> | ORDENALO</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
    <script src="assets/js/validacion.js" defer></script>
</head>
<body>
    <header class="encabezado">
        <a class="marca" href="index.php">ORDENALO</a>
        <nav class="navegacion">
            <a href="crear.php">Registrar</a>
            <a href="listar.php">Movimientos</a>
        </nav>
    </header>

    <main class="contenedor">
        <?= $contenido ?>
    </main>

    <footer class="pie">
        <p>ORDENALO · Presupuesto personal</p>
    </footer>
</body>
</html>