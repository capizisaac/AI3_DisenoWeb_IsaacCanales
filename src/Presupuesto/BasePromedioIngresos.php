<?php

declare(strict_types=1);

namespace Ordenalo\Presupuesto;

use DateTimeImmutable;
use Ordenalo\Models\AjustesPresupuesto;
use Ordenalo\Models\Dinero;
use Ordenalo\Models\Tipo;
use Ordenalo\Repositories\MovimientoRepositorio;

/**
 * Promedio mensual de los ingresos registrados en los últimos tres meses completos.
 * Se divide entre los meses transcurridos desde el primer ingreso de esa ventana.
 */
final class BasePromedioIngresos implements BaseIngreso
{
    private const MESES = 3;

    public function __construct(private MovimientoRepositorio $movimientos)
    {
    }

    public function calcular(AjustesPresupuesto $ajustes, DateTimeImmutable $referencia): ?Dinero
    {
        $inicioMesActual = $referencia->modify('first day of this month')->setTime(0, 0);
        $desde = $inicioMesActual->modify('-' . self::MESES . ' months');
        $hasta = $inicioMesActual->modify('-1 day');

        $porMes = [];

        foreach ($this->movimientos->listarPorPeriodo($desde, $hasta) as $movimiento) {
            if ($movimiento->tipo !== Tipo::Ingreso) {
                continue;
            }

            $mes = $movimiento->fechaMovimiento->format('Y-m');
            $porMes[$mes] = ($porMes[$mes] ?? Dinero::cero())->sumar($movimiento->monto);
        }

        if ($porMes === []) {
            return null;
        }

        $total = Dinero::cero();

        foreach ($porMes as $montoMensual) {
            $total = $total->sumar($montoMensual);
        }

        ksort($porMes);
        [$anioInicial, $mesInicial] = array_map('intval', explode('-', (string) array_key_first($porMes)));
        $meses = ((int) $hasta->format('Y') * 12 + (int) $hasta->format('n'))
            - ($anioInicial * 12 + $mesInicial) + 1;

        return Dinero::desdeCentavos(intdiv($total->centavos() * 2 + $meses, $meses * 2));
    }
}