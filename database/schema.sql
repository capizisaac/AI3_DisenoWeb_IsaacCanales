CREATE DATABASE IF NOT EXISTS db_sistema_web
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE db_sistema_web;

CREATE TABLE movimientos (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  tipo             ENUM('ingreso','gasto') NOT NULL,
  categoria        ENUM('alimentacion','transporte','vivienda','servicios',
                        'salud','educacion','ocio','sueldo','extras','otros') NOT NULL,
  monto            DECIMAL(10,2) NOT NULL,
  descripcion      VARCHAR(255) NOT NULL,
  fecha_movimiento DATE NOT NULL,
  fecha_creacion   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT chk_monto_positivo CHECK (monto > 0)
) ENGINE=InnoDB;