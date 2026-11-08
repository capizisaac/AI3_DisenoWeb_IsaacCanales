<?php

declare(strict_types=1);

namespace Ordenalo\Models;

enum Categoria: string
{
    case Alimentacion = 'alimentacion';
    case Transporte = 'transporte';
    case Vivienda = 'vivienda';
    case Servicios = 'servicios';
    case Salud = 'salud';
    case Educacion = 'educacion';
    case Ocio = 'ocio';
    case Sueldo = 'sueldo';
    case Extras = 'extras';
    case Otros = 'otros';

    /**
     * @return list<self>
     */
    public static function permitidasPara(Tipo $tipo): array
    {
        return match ($tipo) {
            Tipo::Ingreso => [self::Sueldo, self::Extras, self::Otros],
            Tipo::Gasto => [
                self::Alimentacion,
                self::Transporte,
                self::Vivienda,
                self::Servicios,
                self::Salud,
                self::Educacion,
                self::Ocio,
                self::Otros,
            ],
        };
    }

    public function etiqueta(): string
    {
        return match ($this) {
            self::Alimentacion => 'Alimentación',
            self::Transporte => 'Transporte',
            self::Vivienda => 'Vivienda',
            self::Servicios => 'Servicios',
            self::Salud => 'Salud',
            self::Educacion => 'Educación',
            self::Ocio => 'Ocio',
            self::Sueldo => 'Sueldo',
            self::Extras => 'Extras',
            self::Otros => 'Otros',
        };
    }
}