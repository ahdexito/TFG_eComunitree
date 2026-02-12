DROP DATABASE IF EXISTS ecomunitree;

CREATE DATABASE ecomunitree;

USE ecomunitree;

-- -----------------------------------------
-- USUARIOS
-- -----------------------------------------
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(150),
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('vecino', 'presidente', 'admin') NOT NULL,
    telefono VARCHAR(20),
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -----------------------------------------
-- PUBLICACIONES (CLASE PADRE)
-- -----------------------------------------
CREATE TABLE publicaciones (
    id_publicacion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    tipo ENUM('aviso', 'incidencia', 'votacion') NOT NULL,
    titulo VARCHAR(150),
    contenido TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE
);

-- -----------------------------------------
-- TIPOS DE INCIDENCIAS (CATÁLOGO)
-- -----------------------------------------
CREATE TABLE tipos_incidencia (
    id_tipo_incidencia INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    activo BOOLEAN DEFAULT TRUE
);

-- -----------------------------------------
-- INCIDENCIAS (TIPO PUBLICACIÓN)
-- -----------------------------------------
CREATE TABLE incidencias (
    id_publicacion INT PRIMARY KEY,
    id_tipo_incidencia INT NOT NULL,
    subtitulo VARCHAR(255) NOT NULL,
    estado ENUM('pendiente', 'en_proceso', 'resuelta') DEFAULT 'pendiente',
    proridad ENUM('baja', 'media', 'alta') DEFAULT 'media',
    FOREIGN KEY (id_publicacion) REFERENCES publicaciones(id_publicacion)
        ON DELETE CASCADE,
    FOREIGN KEY (id_tipo_incidencia) REFERENCES tipos_incidencia(id_tipo_incidencia)
);

-- -----------------------------------------
-- AVISOS (TIPO PUBLICACIÓN)
-- -----------------------------------------
CREATE TABLE avisos (
    id_publicacion INT PRIMARY KEY,
    FOREIGN KEY (id_publicacion) REFERENCES publicaciones(id_publicacion)
        ON DELETE CASCADE
);

-- -----------------------------------------
-- VOTACIONES (TIPO PUBLICACIÓN)
-- -----------------------------------------
CREATE TABLE votaciones (
    id_publicacion INT PRIMARY KEY,
    fecha_cierre DATE NOT NULL,
    FOREIGN KEY (id_publicacion) REFERENCES publicaciones(id_publicacion)
        ON DELETE CASCADE
);

-- -----------------------------------------
-- OPCIONES DE VOTACIÓN
-- -----------------------------------------
CREATE TABLE opciones_votacion (
    id_opcion INT AUTO_INCREMENT PRIMARY KEY,
    id_publicacion INT NOT NULL,
    texto VARCHAR(150) NOT NULL,
    FOREIGN KEY (id_publicacion) REFERENCES votaciones(id_publicacion)
        ON DELETE CASCADE
);

-- -----------------------------------------
-- VOTOS
-- -----------------------------------------
CREATE TABLE votos (
    id_usuario INT NOT NULL,
    id_opcion INT NOT NULL,
    fecha_voto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_usuario, id_opcion),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE,
    FOREIGN KEY (id_opcion) REFERENCES opciones_votacion(id_opcion)
        ON DELETE CASCADE
);

-- -----------------------------------------
-- SERVICIOS DEL EDIFICIO
-- -----------------------------------------
CREATE TABLE servicios (
    id_servicio INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(300),
    telefono VARCHAR(30),
    email VARCHAR(150),
    enlace VARCHAR(255),
    activo BOOLEAN DEFAULT TRUE
);