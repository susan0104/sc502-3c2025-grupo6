CREATE DATABASE golden_paws;
USE golden_paws;

/*PRIMERO LAS TABLAS AUXILIARES PARA LOS SELECTS DINÁMICOS*/
CREATE TABLE UsuarioRol (
  Rol_Id SMALLINT AUTO_INCREMENT PRIMARY KEY,
  Nombre VARCHAR(100) NOT NULL
);
CREATE TABLE ClientePlan (
  Plan_Id SMALLINT AUTO_INCREMENT PRIMARY KEY,
  Nombre VARCHAR(100) NOT NULL
);

CREATE TABLE MascotaEspecie (
  Especie_Id SMALLINT AUTO_INCREMENT PRIMARY KEY,
  Nombre VARCHAR(100) NOT NULL
);

CREATE TABLE Servicio (
  Servicio_Id INT AUTO_INCREMENT PRIMARY KEY,
  Nombre VARCHAR(100) NOT NULL,
  Descripcion TEXT,
  Precio DECIMAL(10,2) NOT NULL,
  Duracion_estimada INT  -- minutos
);

/*LAS INSERCIONES DE LAS TABLAS AUXILIARES*/
INSERT INTO UsuarioRol (Nombre) values ('Secretaria');
INSERT INTO UsuarioRol (Nombre) values ('Veterinaria');
INSERT INTO UsuarioRol (Nombre) values ('Asistente');

INSERT INTO ClientePlan (Nombre) values ('Estándar');
INSERT INTO ClientePlan (Nombre) values ('Avanzado');
INSERT INTO ClientePlan (Nombre) values ('Premium');

INSERT INTO MascotaEspecie (Nombre) values ('Perro');
INSERT INTO MascotaEspecie (Nombre) values ('Gato');
INSERT INTO MascotaEspecie (Nombre) values ('Ave');

INSERT INTO Servicio (Nombre, Descripcion, Precio, Duracion_estimada) 
VALUES 
('Revisión General', 'Consulta básica de estado', 18500, 30),
('Vacunación Anual', 'Incluye múltiple, rabia y distemper', 34800, 20),
('Baño Grooming', 'Servicio de baño estándar', 10000, 60);

/*TABLAS BASE DE LA VETERINARIA*/

CREATE TABLE Usuario (
  Usuario_Id INT AUTO_INCREMENT PRIMARY KEY,
  Nombre VARCHAR(150) NOT NULL,
  Usuario VARCHAR(80) NOT NULL UNIQUE,
  Contrasena VARCHAR(255) NOT NULL,
  Correo VARCHAR(100) NOT NULL,
  Rol_Id SMALLINT NOT NULL,
  Fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT FK_USUARIOROL FOREIGN KEY (Rol_Id) REFERENCES UsuarioRol (Rol_Id)
);

CREATE TABLE Cliente (
  Cliente_Id INT AUTO_INCREMENT PRIMARY KEY,
  Nombre VARCHAR(150) NOT NULL,
  Identificacion VARCHAR(20) NOT NULL,
  FechaNacimiento DATE NOT NULL,
  Correo VARCHAR(100) NOT NULL,
  Plan_Id SMALLINT NOT NULL,
  Fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT FK_CLIENTEPLAN FOREIGN KEY (Plan_Id) REFERENCES ClientePlan (Plan_Id)
);

CREATE TABLE Mascota (
  Mascota_Id INT AUTO_INCREMENT PRIMARY KEY,
  Nombre VARCHAR(150) NOT NULL,
  Especie_Id SMALLINT NOT NULL,
  Raza VARCHAR(100) NOT NULL,
  Edad smallint NOT NULL,
  Observaciones varchar(200) NOT NULL,
  Fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
  Cliente_Id INT NOT NULL,
  Foto LONGTEXT NOT NULL,
  CONSTRAINT FK_MASCOTAESPECIE FOREIGN KEY (Especie_Id) REFERENCES MascotaEspecie (Especie_Id),
  CONSTRAINT FK_MASCOTACLIENTE FOREIGN KEY (Cliente_Id) REFERENCES Cliente (Cliente_Id)
);

CREATE TABLE MascotaExpediente (
  Expediente_Id INT AUTO_INCREMENT PRIMARY KEY,
  Mascota_Id INT NOT NULL,
  Fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  Ultima_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  Observaciones TEXT,
  Alergias TEXT,
  Vacunas TEXT,
  Tratamientos TEXT,
  CONSTRAINT fk_expediente_mascota FOREIGN KEY (Mascota_Id) REFERENCES Mascota(Mascota_Id)
);


CREATE TABLE Citas (
  Cita_Id INT AUTO_INCREMENT PRIMARY KEY,
  Mascota_Id INT NOT NULL,
  Servicio_Id INT NOT NULL,
  Usuario_Id INT,
  Fecha DATETIME NOT NULL,
  Estado VARCHAR(50) DEFAULT 'Programada',
  Precio DECIMAL(10,2),
  Observaciones TEXT,

  CONSTRAINT fk_cita_mascota FOREIGN KEY (Mascota_Id) REFERENCES Mascota (Mascota_Id) ON DELETE CASCADE,

  CONSTRAINT fk_cita_servicio FOREIGN KEY (Servicio_Id) REFERENCES Servicio (Servicio_Id),

  CONSTRAINT fk_cita_usuario FOREIGN KEY (Usuario_Id) REFERENCES Usuario (Usuario_Id)
);
