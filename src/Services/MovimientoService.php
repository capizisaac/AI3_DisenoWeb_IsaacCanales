<?php

declare(strict_types=1);

namespace Ordenalo\Services;

use DateTimeImmutable;
use Ordenalo\Models\Categoria;
use Ordenalo\Models\Dinero;
use Ordenalo\Models\Movimiento;
use Ordenalo\Models\Tipo;
use Ordenalo\Repositories\MovimientoRepositorio;
use Ordenalo\Validation\ReglaValidacion;
use Ordenalo\Validation\ValidacionException;

final class MovimientoService
{
    /** @param list<ReglaValidacion> $reglas */
    public function __construct(
        private MovimientoRepositorio $repositorio,
        private array $reglas,
    ) {
    }

    /**
     * @param array<string, mixed> $datos Datos crudos del formulario.
     * @return int Id del movimiento guardado.
     * @throws ValidacionException Si algún dato no es válido.
     */
    public function registrar(array $datos): int
    {
        $errores = [];

        foreach ($this->reglas as $regla) {
            $mensaje = $regla->validar($datos);

            if ($mensaje !== null) {
                $errores[$regla->campo()] = $mensaje;
            }
        }

        if ($errores !== []) {
            throw new ValidacionException($errores);
        }

        return $this->repositorio->guardar(new Movimiento(
            tipo: Tipo::from($datos['tipo']),
            categoria: Categoria::from($datos['categoria']),
            monto: Dinero::desdeTexto($datos['monto']),
            descripcion: trim($datos['descripcion']),
            fechaMovimiento: new DateTimeImmutable($datos['fecha_movimiento']),
        ));
    }

    /** @return list<Movimiento> */
    public function listar(): array
    {
        return $this->repositorio->listarTodos();
    }
}