<?php

declare(strict_types=1);

use Ordenalo\Seguridad\Sesion;
use Ordenalo\Validation\ValidacionException;
use Ordenalo\Views\Vista;

require_once __DIR__ . '/../vendor/autoload.php';

Sesion::iniciar();

$servicios = require __DIR__ . '/../config/contenedor.php';
$servicio = $servicios['movimientos'];
$csrf = $servicios['csrf'];
$vista = new Vista(__DIR__ . '/../src/Views/plantillas');

$valores = [
    'tipo' => '',
    'categoria' => '',
    'monto' => '',
    'descripcion' => '',
    'fecha_movimiento' => date('Y-m-d'),
];
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$csrf->validar($_POST['csrf'] ?? null)) {
        http_response_code(403);
        exit('Solicitud no válida o caducada. Vuelve a cargar la página e inténtalo de nuevo.');
    }

    foreach (array_keys($valores) as $campo) {
        $dato = $_POST[$campo] ?? '';
        $valores[$campo] = is_string($dato) ? $dato : '';
    }

    try {
        $servicio->registrar($valores);
        header('Location: listar.php?guardado=1');
        exit;
    } catch (ValidacionException $e) {
        $errores = $e->errores();
    } catch (PDOException $e) {
        error_log('Error al guardar el movimiento: ' . $e->getMessage());
        http_response_code(500);
        exit('No se pudo guardar el movimiento. Inténtalo más tarde.');
    }
}

echo $vista->renderizar(
    'formulario',
    ['valores' => $valores, 'errores' => $errores, 'csrf' => $csrf->token()],
    'Registrar movimiento'
);