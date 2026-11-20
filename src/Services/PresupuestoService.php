<?php

declare(strict_types=1);

namespace Ordenalo\Services;

use DateTimeImmutable;
use InvalidArgumentException;
use Ordenalo\Models\AjustesPresupuesto;
use Ordenalo\Models\Categoria;
use Ordenalo\Models\Dinero;
use Ordenalo\Models\ModoBase;
use Ordenalo\Models\Tipo;
use Ordenalo\Presupuesto\CalculadoraBase;
use Ordenalo\Presupuesto\ConfiguracionPresupuesto;
use Ordenalo\Presupuesto\LimiteConfigurado;
use Ordenalo\Repositories\AjustesRepositorio;
use Ordenalo\Repositories\LimiteRepositorio;
use Ordenalo\Validation\ReglaValidacion;
use Ordenalo\Validation\ValidacionException;

final class PresupuestoService
{
    /**
     * @param array<string, int> $sugeridos Porcentaje sugerido por valor de categoría.
     * @param list<ReglaValidacion> $reglas
     */
    public function __construct(
        private AjustesRepositorio $ajustes,
        private LimiteRepositorio $limites,
        private CalculadoraBase $calculadora,
        private array $sugeridos,
        private array $reglas,
    ) {
        foreach (Categoria::permitidasPara(Tipo::Gasto) as $categoria) {
            $valor = $sugeridos[$categoria->value] ?? null;

            if (!is_int($valor) || $valor < 1 || $valor > 100) {
                throw new InvalidArgumentException(
                    'Falta un porcentaje sugerido válido para ' . $categoria->value . '.'
                );
            }
        }
    }

    public function configuracion(DateTimeImmutable $referencia): ConfiguracionPresupuesto
    {
        $ajustes = $this->ajustes->obtener();
        $base = $this->calculadora->calcular($ajustes, $referencia);
        $personalizados = $this->limites->personalizados();
        $lineas = [];

        foreach (Categoria::permitidasPara(Tipo::Gasto) as $categoria) {
            $clave = $categoria->value;
            $tieneFila = array_key_exists($clave, $personalizados);
            $personalizado = $tieneFila ? $personalizados[$clave] : null;
            $sinLimite = $tieneFila && $personalizado === null;
            $efectivo = $sinLimite ? null : ($personalizado ?? $this->sugeridos[$clave]);
            $limite = ($efectivo !== null && $base !== null) ? $base->tomarPorcentaje($efectivo) : null;

            $lineas[] = new LimiteConfigurado(
                $categoria,
                $personalizado,
                $sinLimite,
                $this->sugeridos[$clave],
                $efectivo,
                $limite,
            );
        }

        return new ConfiguracionPresupuesto($ajustes->modo, $ajustes->ingresoManual, $base, $lineas);
    }

    /**
     * @return array<string, Dinero> Límite en dinero por valor de categoría; vacío si no hay base.
     */
    public function limitesEnDinero(DateTimeImmutable $referencia): array
    {
        $limites = [];

        foreach ($this->configuracion($referencia)->limites as $linea) {
            if ($linea->limite !== null) {
                $limites[$linea->categoria->value] = $linea->limite;
            }
        }

        return $limites;
    }

    /**
     * @param array<string, mixed> $datos Datos crudos del formulario.
     * @throws ValidacionException Si algún dato no es válido.
     */
    public function guardar(array $datos): void
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

        $textoIngreso = trim((string) ($datos['ingreso_manual'] ?? ''));
        $ingreso = $textoIngreso === '' ? null : Dinero::desdeTexto($textoIngreso);

        $personalizados = [];

        foreach (Categoria::permitidasPara(Tipo::Gasto) as $categoria) {
            $clave = $categoria->value;

            if (($datos['sin_limite_' . $clave] ?? '') === '1') {
                $personalizados[$clave] = null;
                continue;
            }

            $porcentaje = trim((string) ($datos['pct_' . $clave] ?? ''));

            if ($porcentaje !== '') {
                $personalizados[$clave] = (int) $porcentaje;
            }
        }

        $this->ajustes->guardar(new AjustesPresupuesto(ModoBase::from((string) $datos['modo_base']), $ingreso));
        $this->limites->reemplazar($personalizados);
    }
}