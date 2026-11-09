<?php

declare(strict_types=1);

namespace Ordenalo\Views;

use RuntimeException;
use Throwable;

final class Vista
{
    public function __construct(private string $directorio)
    {
    }

    /**
     * @param array<string, mixed> $datos Variables disponibles en la plantilla.
     */
    public function renderizar(string $plantilla, array $datos = [], string $titulo = 'ORDENALO'): string
    {
        $contenido = $this->cargar($plantilla, $datos);

        return $this->cargar('layout', ['titulo' => $titulo, 'contenido' => $contenido]);
    }

    /**
     * @param array<string, mixed> $datos
     */
    private function cargar(string $plantilla, array $datos): string
    {
        $ruta = $this->directorio . '/' . $plantilla . '.php';

        if (!preg_match('/^[a-z_]+$/D', $plantilla) || !is_file($ruta)) {
            throw new RuntimeException('Plantilla no encontrada.');
        }

        ob_start();

        try {
            (static function (string $rutaPlantilla, array $variables): void {
                extract($variables, EXTR_SKIP);
                require $rutaPlantilla;
            })($ruta, $datos);
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        return (string) ob_get_clean();
    }
}