<?php

declare(strict_types=1);

use Ordenalo\Models\Categoria;
use Ordenalo\Models\Tipo;
use Ordenalo\Views\Html;

/** @var array<string, string> $valores Valores a mostrar en los campos. */
/** @var array<string, string> $errores Mensajes de error por campo. */
?>
<section class="formulario-registro">
    <h1>Registrar movimiento</h1>

    <?php if ($errores !== []): ?>
        <div class="alerta" role="alert">
            Revisa los campos marcados e inténtalo de nuevo.
        </div>
    <?php endif; ?>

    <form id="formulario-movimiento" action="crear.php" method="post">
        <fieldset class="campo">
            <legend>Tipo</legend>
            <?php foreach (Tipo::cases() as $tipo): ?>
                <label class="opcion">
                    <input type="radio" name="tipo" value="<?= Html::e($tipo->value) ?>"
                        <?= $valores['tipo'] === $tipo->value ? 'checked' : '' ?> required>
                    <?= Html::e($tipo->etiqueta()) ?>
                </label>
            <?php endforeach; ?>
            <?php if (isset($errores['tipo'])): ?>
                <p class="error"><?= Html::e($errores['tipo']) ?></p>
            <?php endif; ?>
        </fieldset>

        <div class="campo">
            <label for="categoria">Categoría</label>
            <select id="categoria" name="categoria" required>
                <option value="">Seleccione una categoría</option>
                <?php foreach (Categoria::cases() as $categoria): ?>
                    <?php
                    $tiposPermitidos = array_filter(
                        Tipo::cases(),
                        static fn (Tipo $t): bool => in_array($categoria, Categoria::permitidasPara($t), true)
                    );
                    $dataTipos = implode(' ', array_map(static fn (Tipo $t): string => $t->value, $tiposPermitidos));
                    ?>
                    <option value="<?= Html::e($categoria->value) ?>"
                        data-tipos="<?= Html::e($dataTipos) ?>"
                        <?= $valores['categoria'] === $categoria->value ? 'selected' : '' ?>>
                        <?= Html::e($categoria->etiqueta()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errores['categoria'])): ?>
                <p class="error"><?= Html::e($errores['categoria']) ?></p>
            <?php endif; ?>
        </div>

        <div class="campo">
            <label for="monto">Monto (USD)</label>
            <input type="number" id="monto" name="monto" step="0.01" min="0.01" max="99999999.99"
                   inputmode="decimal" required value="<?= Html::e($valores['monto']) ?>">
            <?php if (isset($errores['monto'])): ?>
                <p class="error"><?= Html::e($errores['monto']) ?></p>
            <?php endif; ?>
        </div>

        <div class="campo">
            <label for="descripcion">Descripción</label>
            <input type="text" id="descripcion" name="descripcion" minlength="3" maxlength="255"
                   required value="<?= Html::e($valores['descripcion']) ?>">
            <?php if (isset($errores['descripcion'])): ?>
                <p class="error"><?= Html::e($errores['descripcion']) ?></p>
            <?php endif; ?>
        </div>

        <div class="campo">
            <label for="fecha_movimiento">Fecha del movimiento</label>
            <input type="date" id="fecha_movimiento" name="fecha_movimiento"
                   min="2000-01-01" max="2100-12-31" required
                   value="<?= Html::e($valores['fecha_movimiento']) ?>">
            <?php if (isset($errores['fecha_movimiento'])): ?>
                <p class="error"><?= Html::e($errores['fecha_movimiento']) ?></p>
            <?php endif; ?>
        </div>

        <div class="acciones">
            
            <button type="submit" class="boton">Guardar movimiento</button>
            <a class="enlace-secundario" href="listar.php">Cancelar</a>
        </div>
    </form>
</section>