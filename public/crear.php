<?php

declare(strict_types=1);

use Ordenalo\Validation\ValidacionException;
use Ordenalo\Views\Vista;

$servicio = require __DIR__ . '/../config/contenedor.php';
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
        error_log('Error al gu
        ardar el movimiento: ' . $e->getMessage());
        http_response_code(500);
        exit('No se pudo guardar el movimiento. Inténtalo más tarde.');
    }
}

echo $vista->renderizar('formulario', ['valores' => $valores, 'errores' => $errores], 'Registrar movimiento');

