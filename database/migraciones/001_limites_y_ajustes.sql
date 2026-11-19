-- Migracion 001: limites por categoria editables y ajustes de presupuesto.
USE db_sistema_web;

-- porcentaje con valor: limite elegido por el usuario (1 a 100 por ciento del ingreso base).
-- porcentaje NULL: el usuario eligio expresamente "Sin limite".
-- Sin fila para la categoria: se usa el porcentaje preestablecido de config/limites.php.
CREATE TABLE IF NOT EXISTS limites (
  categoria           ENUM('alimentacion','transporte','vivienda','servicios',
                           'salud','educacion','ocio','otros') NOT NULL,
  porcentaje          TINYINT UNSIGNED NULL,
  fecha_actualizacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (categoria),
  CONSTRAINT chk_limite_porcentaje CHECK (porcentaje IS NULL OR porcentaje BETWEEN 1 AND 100)
) ENGINE=InnoDB;

-- Fila unica con el modo de calculo del ingreso base y el monto manual.
CREATE TABLE IF NOT EXISTS ajustes_presupuesto (
  id                  TINYINT UNSIGNED NOT NULL DEFAULT 1,
  modo_base           ENUM('manual','promedio') NOT NULL DEFAULT 'manual',
  ingreso_manual      DECIMAL(10,2) NULL,
  fecha_actualizacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT chk_ajustes_fila_unica CHECK (id = 1),
  CONSTRAINT chk_ingreso_manual_positivo CHECK (ingreso_manual IS NULL OR ingreso_manual > 0)
) ENGINE=InnoDB;

INSERT IGNORE INTO ajustes_presupuesto (id) VALUES (1);

-- Permisos minimos por tabla para el usuario de la aplicacion.
GRANT SELECT, INSERT, UPDATE, DELETE ON db_sistema_web.limites TO 'ordenalo_app'@'localhost';
GRANT SELECT, UPDATE ON db_sistema_web.ajustes_presupuesto TO 'ordenalo_app'@'localhost';
FLUSH PRIVILEGES;