<?php

declare(strict_types=1);

namespace Ordenalo\Services;

use DateTimeImmutable;
use Ordenalo\Models\Categoria;
use Ordenalo\Models\Dinero;
use Ordenalo\Models\EstadoCategoria;
use Ordenalo\Models\NivelPresupuesto;
use Ordenalo\Models\ResumenMensual;
use Ordenalo\Models\Tipo;
use Ordenalo\Repositories\MovimientoRepositorio;

final class ResumenService
{
    private const UMBRAL_CERCA = 80;

    /** @var array<string, Dinero> Límite mensual por valor de categoría. */
    private array $limites = [];

    /**
     * @param array<string, string> $limites Valor de categoría => monto en texto (ej. '250.00').
     */
    public function __construct(
        private MovimientoRepositorio $repositorio,
        array $limites,
    ) {
        foreach ($limites as $categoria => $monto) {
            $this->limites[Categoria::from($categoria)->value] = Dinero::desdeTexto($monto);
        }
    }

    public function delMes(DateTimeImmutable $referencia): ResumenMensual
    {
        $inicio = $referencia->modify('first day of this month')->setTime(0, 0);
        $fin = $referencia->modify('last day of this month')->setTime(0, 0);

        $ingresos = Dinero::cero();
        $gastos = Dinero::cero();
        $gastoPorCategoria = [];

        foreach ($this->repositorio->listarPorPeriodo($inicio, $fin) as $movimiento) {
            if ($movimiento->tipo === Tipo::Ingreso) {
                $ingresos = $ingresos->sumar($movimiento->monto);
                continue;
            }

            $gastos = $gastos->sumar($movimiento->monto);
            $clave = $movimiento->categoria->value;
            $gastoPorCategoria[$clave] = ($gastoPorCategoria[$clave] ?? Dinero::cero())
                ->sumar($movimiento->monto);
        }

        return new ResumenMensual(
            $inicio,
            $ingresos,
            $gastos,
            $ingresos->restar($gastos),
            $this->evaluarLimites($gastoPorCategoria),
        );
    }

    /**
     * @param array<string, Dinero> $gastoPorCategoria
     * @return list<EstadoCategoria>
     */
    private function evaluarLimites(array $gastoPorCategoria): array
    {
        $estados = [];

        foreach (Categoria::permitidasPara(Tipo::Gasto) as $categoria) {
            $limite = $this->limites[$categoria->value] ?? null;

            if ($limite === null) {
                continue;
            }

            $gastado = $gastoPorCategoria[$categoria->value] ?? Dinero::cero();
            $porcentaje = $gastado->porcentajeDe($limite);

            $estados[] = new EstadoCategoria(
                $categoria,
                $gastado,
                $limite,
                $porcentaje,
                $this->nivelPara($gastado, $limite, $porcentaje),
            );
        }

        return $estados;
    }

    private function nivelPara(Dinero $gastado, Dinero $limite, int $porcentaje): NivelPresupuesto
    {
        if ($gastado->esMayorQue($limite)) {
            return NivelPresupuesto::Excedido;
        }

        if ($porcentaje >= self::UMBRAL_CERCA) {
            return NivelPresupuesto::Cerca;
        }

        return NivelPresupuesto::Normal;
    }
}