<?php

declare(strict_types=1);

namespace Ordenalo\Validation;

interface ReglaValidacion
{
    /** Nombre del campo del formulario al que pertenece el error. */
    public function campo(): string;

    /**
     * @param array<string, mixed> $datos Datos crudos recibidos del formulario.
     * @return string|null Mensaje de error, o null si el dato es válido.
     */
    public function validar(array $datos): ?string;
}