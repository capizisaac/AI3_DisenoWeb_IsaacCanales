<?php

declare(strict_types=1);

use Ordenalo\Models\Movimiento;
use Ordenalo\Models\ResumenMensual;
use Ordenalo\Models\Tipo;
use Ordenalo\Views\Html;

/** @var list<Movimiento> $movimientos */
/** @var ResumenMensual $resumen */
/** @var bool $guardado */

$meses = [
    '', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
    'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre',
];
$nombreMes = ucfirst($meses[(int) $resumen->mes->format('n')]) . ' de ' . $resumen->mes->format('Y');
$saldoNegativo = $resumen->saldo->centavos() < 0;
?>
<section class="consulta">
    <div class="consulta-cabecera">
        <h1>Movimientos</h1>
        <a class="boton" href="crear.php">Registrar movimiento</a>
    </div>

    <?php if ($guardado): ?>
        <div class="alerta alerta-exito" role="status">Movimiento guardado correctamente.</div>
    <?php endif; ?>

    <h2>Resumen de <?= Html::e($nombreMes) ?></h2>
    <div class="resumen">
        <div class="resumen-dato">
            <span class="resumen-etiqueta">Ingresos</span>
            <strong><?= Html::e($resumen->ingresos->formatear()) ?></strong>
        </div>
        <div class="resumen-dato">
            <span class="resumen-etiqueta">Gastos</span>
            <strong><?= Html::e($resumen->gastos->formatear()) ?></strong>
        </div>
        <div class="resumen-dato<?= $saldoNegativo ? ' saldo-negativo' : '' ?>">
            <span class="resumen-etiqueta">Saldo</span>
            <strong><?= Html::e($resumen->saldo->formatear()) ?></strong>
        </div>
    </div>

    <h2>Límites por categoría</h2>
    <ul class="limites">
        <?php foreach ($resumen->estados as $estado): ?>
            <li class="limite limite-<?= Html::e($estado->nivel->value) ?>">
                <div class="limite-texto">
                    <span class="limite-nombre"><?= Html::e($estado->categoria->etiqueta()) ?></span>
                    <span>
                        <?= Html::e($estado->gastado->formatear()) ?> de
                        <?= Html::e($estado->limite->formatear()) ?>
                        (<?= Html::e((string) $estado->porcentaje) ?> %)
                    </span>
                </div>
                <progress max="100" value="<?= Html::e((string) min(100, $estado->porcentaje)) ?>"></progress>
                <span class="limite-nivel"><?= Html::e($estado->nivel->etiqueta()) ?></span>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2>Todos los movimientos</h2>
    <?php if ($movimientos === []): ?>
        <p class="vacio">Aún no hay movimientos registrados.</p>
    <?php else: ?>
        <div class="tabla-contenedor">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Categoría</th>
                        <th>Descripción</th>
                        <th class="numero">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movimientos as $movimiento): ?>
                        <tr>
                            <td data-etiqueta="Fecha">
                                <?= Html::e($movimiento->fechaMovimiento->format('d/m/Y')) ?>
                            </td>
                            <td data-etiqueta="Tipo">
                                <span class="etiqueta etiqueta-<?= Html::e($movimiento->tipo->value) ?>">
                                    <?= Html::e($movimiento->tipo->etiqueta()) ?>
                                </span>
                            </td>
                            <td data-etiqueta="Categoría">
                                <?= Html::e($movimiento->categoria->etiqueta()) ?>
                            </td>
                            <td data-etiqueta="Descripción">
                                <?= Html::e($movimiento->descripcion) ?>
                            </td>
                            <td class="numero monto-<?= Html::e($movimiento->tipo->value) ?>" data-etiqueta="Monto">
                                <?= $movimiento->tipo === Tipo::Gasto ? '-' : '+' ?><?= Html::e($movimiento->monto->formatear()) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>