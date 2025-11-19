CREATE DATABASE golden_paws;
USE golden_paws;

CREATE TABLE usuarios (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  usuario VARCHAR(80) NOT NULL UNIQUE,
  contrasena VARCHAR(255) NOT NULL,
  correo VARCHAR(100),
  fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE mascotas (
  id_mascota INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  especie VARCHAR(50),
  raza VARCHAR(100),
  edad INT,
  peso DECIMAL(5,2),

  CONSTRAINT fk_mascota_usuario
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

CREATE TABLE expedientes (
  id_expediente INT AUTO_INCREMENT PRIMARY KEY,
  id_mascota INT NOT NULL,
  fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  ultima_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  observaciones TEXT,
  alergias TEXT,
  vacunas TEXT,
  tratamientos TEXT,

  CONSTRAINT fk_expediente_mascota
    FOREIGN KEY (id_mascota) REFERENCES mascotas(id_mascota)
);

CREATE TABLE servicios (
  id_servicio INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  descripcion TEXT,
  precio DECIMAL(10,2) NOT NULL,
  duracion_estimada INT  -- minutos
);

CREATE TABLE empleados (
  id_empleado INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  cargo VARCHAR(80),
  telefono VARCHAR(20),
  correo VARCHAR(100)
);

CREATE TABLE citas (
  id_cita INT AUTO_INCREMENT PRIMARY KEY,
  id_mascota INT NOT NULL,
  id_servicio INT NOT NULL,
  id_empleado INT,
  fecha DATETIME NOT NULL,
  estado VARCHAR(50) DEFAULT 'Programada',
  precio DECIMAL(10,2),
  observaciones TEXT,

  CONSTRAINT fk_cita_mascota
    FOREIGN KEY (id_mascota) REFERENCES mascotas(id_mascota)
    ON DELETE CASCADE,

  CONSTRAINT fk_cita_servicio
    FOREIGN KEY (id_servicio) REFERENCES servicios(id_servicio),

  CONSTRAINT fk_cita_empleado
    FOREIGN KEY (id_empleado) REFERENCES empleados(id_empleado)
);
