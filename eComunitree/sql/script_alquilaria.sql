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
    vivienda VARCHAR(20),
    foto VARCHAR(255) DEFAULT 'default.jpg',
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




-- ------------------------------------
-- CARGAR TIPOS DE INCIDENCIAS
-- ------------------------------------
INSERT INTO tipos_incidencia (nombre, descripcion, activo) VALUES
('Desperfecto estético', 'Deterioro visual en zonas comunes', TRUE),
('Avería eléctrica', 'Problemas relacionados con la instalación eléctrica', TRUE),
('Fontanería', 'Fugas o problemas con tuberías y agua', TRUE),
('Humedades', 'Filtraciones o condensaciones en paredes o techos', TRUE),
('Ascensor', 'Fallos o incidencias en el ascensor', TRUE),
('Iluminación zonas comunes', 'Problemas con luces del edificio', TRUE),
('Puerta de acceso', 'Fallos en puertas principales o portales', TRUE),
('Garaje', 'Problemas en puertas o instalaciones del garaje', TRUE),
('Limpieza', 'Incidencias relacionadas con el servicio de limpieza', TRUE),
('Seguridad', 'Problemas que afecten a la seguridad del edificio', TRUE),
('Jardinería', 'Incidencias en zonas verdes o patios', TRUE),
('Ruidos', 'Molestias acústicas en zonas comunes', TRUE),
('Sistema contra incendios', 'Problemas con extintores o alarmas', TRUE),
('Antena / Telecomunicaciones', 'Problemas de señal o antena comunitaria', TRUE),
('Otros', 'Cualquier otra incidencia no clasificada', TRUE);


-- ------------------------------------
-- CARGAR SERVICIOS
-- ------------------------------------
INSERT INTO servicios (nombre, descripcion, telefono, email, enlace, activo) VALUES

-- Emergencias
('Emergencias 112', 'Teléfono general de emergencias', '112', NULL, 'https://112.es', TRUE),
('Policía Nacional', 'Atención policial', '091', 'contacto@policia.es', 'https://www.policia.es', TRUE),
('Guardia Civil', 'Atención Guardia Civil', '062', 'info@guardiacivil.es', NULL, TRUE),
('Bomberos', 'Servicio de bomberos', '080', NULL, NULL, TRUE),
('Ambulancias', 'Emergencias sanitarias', '061', NULL, NULL, TRUE),
('Protección Civil', 'Atención protección civil', '1006', 'proteccioncivil@seguridad.es', NULL, TRUE),

-- Electricistas
('Electricista Gómez', 'Instalaciones y reparaciones eléctricas', '600123001', 'contacto@electricistagomez.es', NULL, TRUE),
('ElectroServicios Martínez', 'Especialistas en averías eléctricas', '600123002', 'info@electromartinez.es', 'https://electromartinez.es', TRUE),
('Reparaciones Eléctricas López', 'Servicio urgente 24h', '600123003', NULL, NULL, TRUE),

-- Fontaneros
('Fontanería Rápida SL', 'Reparaciones de fugas y tuberías', '600123004', 'info@fontaneriarapida.es', 'https://fontaneriarapida.es', TRUE),
('Fontanero Pérez', 'Servicio económico', '600123005', NULL, NULL, TRUE),
('AquaServicios', 'Instalaciones de agua y desagües', '600123006', 'contacto@aquaservicios.es', 'https://aquaservicios.es', TRUE),

-- Cerrajeros
('Cerrajería Segura', 'Apertura de puertas 24h', '600123007', 'urgencias@cerrajeriasegura.es', 'https://cerrajeriasegura.es', TRUE),
('Cerrajero Express', 'Urgencias en cerraduras', '600123008', NULL, NULL, TRUE),

-- Ascensores
('Ascensores Levanta', 'Mantenimiento y reparación de ascensores', '600123009', 'soporte@ascensoreslevanta.es', 'https://ascensoreslevanta.es', TRUE),
('Elevadores Modernos', 'Servicio técnico ascensores', '600123010', 'info@elevadoresmodernos.es', 'https://elevadoresmodernos.com', TRUE),

-- Limpieza
('Limpiezas Brillo', 'Limpieza de comunidades', '600123011', 'contacto@limpiezasbrillo.es', NULL, TRUE),
('Servicios Integrales Clean', 'Mantenimiento y limpieza integral', '600123012', NULL, 'https://serviciosclean.es', TRUE),

-- Jardinería
('Jardinería VerdeVida', 'Mantenimiento de jardines', '600123013', 'info@verdevida.es', 'https://verdevida.es', TRUE),
('Paisajismo Urbano', 'Diseño y cuidado de zonas verdes', '600123014', NULL, NULL, TRUE),

-- Telecomunicaciones
('Antenas Digitales SL', 'Instalación y reparación de antenas', '600123015', 'soporte@antenasdigitales.es', 'https://antenasdigitales.es', TRUE),
('Telecom Comunidad', 'Problemas de señal TV', '600123016', NULL, NULL, TRUE),

-- Seguridad
('Seguridad Total', 'Instalación de cámaras y alarmas', '600123017', 'info@seguridadtotal.es', 'https://seguridadtotal.es', TRUE),
('Alarmas Vecinales SL', 'Sistemas de seguridad', '600123018', NULL, 'https://alarmasvecinales.es', TRUE),

-- Sistema contra incendios
('Extintores Seguros', 'Revisión de extintores', '600123019', 'contacto@extintoresseguros.es', 'https://extintoresseguros.com', TRUE),
('Protección Fuego SL', 'Sistemas antiincendios', '600123020', NULL, NULL, TRUE),

-- Garaje
('Puertas Automáticas García', 'Reparación de puertas de garaje', '600123021', 'info@puertasgarcia.es', NULL, TRUE),
('Motores Garaje 24h', 'Instalación y mantenimiento', '600123022', NULL, 'https://motoresgaraje24h.es', TRUE),

-- Carpintería
('Carpintería MaderaFina', 'Puertas y arreglos en madera', '600123023', 'contacto@maderafina.es', NULL, TRUE),
('Reformas López', 'Reparaciones estructurales', '600123024', NULL, NULL, TRUE),

-- Pintura
('Pinturas Modernas', 'Pintura de zonas comunes', '600123025', 'info@pinturasmodernas.es', 'https://pinturasmodernas.es', TRUE),
('Decoraciones Ruiz', 'Acabados decorativos', '600123026', NULL, NULL, TRUE),

-- Otros
('Cristalería Central', 'Sustitución de cristales', '600123027', 'contacto@cristaleriacentral.es', NULL, TRUE),
('Control de Plagas Iberia', 'Tratamientos contra plagas', '600123028', 'info@plagasiberia.es', 'https://plagasiberia.es', TRUE),
('Empresa Desatascos Norte', 'Desatascos urgentes', '600123029', NULL, NULL, TRUE),
('Climatización FríoCalor', 'Instalación aire acondicionado', '600123030', 'soporte@friocalor.es', 'https://friocalor.es', TRUE),
('Administrador de Fincas Asociado', 'Gestión administrativa externa', '600123031', 'info@adminfincas.es', 'https://adminfincas.es', TRUE),
('Arquitecto Técnico Comunidad', 'Informes técnicos y peritajes', '600123032', 'contacto@arquitectotecnico.es', NULL, TRUE),
('Seguro Comunidad HogarPlus', 'Atención compañía aseguradora', '900123456', 'siniestros@hogarplus.es', 'https://hogarplus-seguros.es', TRUE);


-- ------------------------------------
-- CARGAR USARIOS
-- ------------------------------------
INSERT INTO usuarios 
(nombre, apellidos, email, password, rol, telefono, vivienda, foto, activo) VALUES

-- JUNTA DIRECTIVA --
('Antonio', 'Recio', 'antonio@pescaderia.es',
'$2y$10$KYSgjEXRQgY/AnoqR0zAzes35VHCEpbal5J4TIjb2YRcjdkecmo3y',
'presidente', '600111002', '1ºC', 'antonio.jpg', TRUE),

('Coque', 'Calatrava', 'coque@mirador.es',
'$2y$10$KYSgjEXRQgY/AnoqR0zAzes35VHCEpbal5J4TIjb2YRcjdkecmo3y',
'admin', '600111005', 'Caravana', 'coque.jpg', TRUE),

-- VECINOS --
('Enrique', 'Pastor', 'enrique@mirador.es',
'$2y$10$KYSgjEXRQgY/AnoqR0zAzes35VHCEpbal5J4TIjb2YRcjdkecmo3y',
'vecino', '600111001', '2ºB', 'enrique.jpg', TRUE),

('Amador', 'Rivas', 'amador@mirador.es',
'$2y$10$KYSgjEXRQgY/AnoqR0zAzes35VHCEpbal5J4TIjb2YRcjdkecmo3y',
'vecino', '600111003', 'Bajo A', 'default.jpg', TRUE),

('Maite', 'Figueroa', 'maite@mirador.es',
'$2y$10$KYSgjEXRQgY/AnoqR0zAzes35VHCEpbal5J4TIjb2YRcjdkecmo3y',
'vecino', '600111004', '2ºA', 'default.jpg', TRUE),

('Judith', 'Becker', 'judith@mirador.es',
'$2y$10$KYSgjEXRQgY/AnoqR0zAzes35VHCEpbal5J4TIjb2YRcjdkecmo3y',
'vecino', '600111006', 'Ático A', 'default.jpg', TRUE),

('Javi', 'Maroto', 'javi@mirador.es',
'$2y$10$KYSgjEXRQgY/AnoqR0zAzes35VHCEpbal5J4TIjb2YRcjdkecmo3y',
'vecino', '600111007', 'Bajo B', 'default.jpg', TRUE),

('Lola', 'Trujillo', 'lola@mirador.es',
'$2y$10$KYSgjEXRQgY/AnoqR0zAzes35VHCEpbal5J4TIjb2YRcjdkecmo3y',
'vecino', '600111008', 'Bajo B', 'default.jpg', TRUE),

('Vicente', 'Maroto', 'vicente@mirador.es',
'$2y$10$KYSgjEXRQgY/AnoqR0zAzes35VHCEpbal5J4TIjb2YRcjdkecmo3y',
'vecino', '600111009', '2ºB', 'default.jpg', TRUE),

('Fermín', 'Trujillo', 'fermin@mirador.es',
'$2y$10$KYSgjEXRQgY/AnoqR0zAzes35VHCEpbal5J4TIjb2YRcjdkecmo3y',
'vecino', '600111010', 'Bajo B', 'default.jpg', TRUE),

('Nines', 'Chacón', 'nines@mirador.es',
'$2y$10$KYSgjEXRQgY/AnoqR0zAzes35VHCEpbal5J4TIjb2YRcjdkecmo3y',
'vecino', '600111011', '1ºA', 'default.jpg', TRUE),

('Raquel', 'Villanueva', 'raquel@mirador.es',
'$2y$10$KYSgjEXRQgY/AnoqR0zAzes35VHCEpbal5J4TIjb2YRcjdkecmo3y',
'vecino', '600111012', '1ºA', 'default.jpg', TRUE),

('Estela', 'Reynolds', 'estela@mirador.es',
'$2y$10$KYSgjEXRQgY/AnoqR0zAzes35VHCEpbal5J4TIjb2YRcjdkecmo3y',
'vecino', '600111013', 'Bajo B', 'default.jpg', TRUE),

('Rebeca', 'Ortiz', 'rebeca@mirador.es',
'$2y$10$KYSgjEXRQgY/AnoqR0zAzes35VHCEpbal5J4TIjb2YRcjdkecmo3y',
'vecino', '600111014', 'Ático A', 'default.jpg', TRUE),

('Bruno', 'Quiroga', 'bruno@mirador.es',
'$2y$10$KYSgjEXRQgY/AnoqR0zAzes35VHCEpbal5J4TIjb2YRcjdkecmo3y',
'vecino', '600111015', '1ºB', 'default.jpg', TRUE),

('Yoli', 'Morcillo', 'yoli@mirador.es',
'$2y$10$KYSgjEXRQgY/AnoqR0zAzes35VHCEpbal5J4TIjb2YRcjdkecmo3y',
'vecino', '600111016', '2ºB', 'default.jpg', TRUE),

('Izaskun', 'Sagastume', 'izaskun@mirador.es',
'$2y$10$KYSgjEXRQgY/AnoqR0zAzes35VHCEpbal5J4TIjb2YRcjdkecmo3y',
'vecino', '600111017', 'Bajo A', 'izaskun.jpg', TRUE),

('Leo', 'Castañeda', 'leo@mirador.es',
'$2y$10$KYSgjEXRQgY/AnoqR0zAzes35VHCEpbal5J4TIjb2YRcjdkecmo3y',
'vecino', '600111019', '1ºB', 'default.jpg', TRUE),

('Berta', 'Escobar', 'berta@mirador.es',
'$2y$10$KYSgjEXRQgY/AnoqR0zAzes35VHCEpbal5J4TIjb2YRcjdkecmo3y',
'vecino', '600111020', '1ºC', 'default.jpg', TRUE),

('Araceli', 'Madariaga', 'araceli@mirador.es',
'$2y$10$KYSgjEXRQgY/AnoqR0zAzes35VHCEpbal5J4TIjb2YRcjdkecmo3y',
'vecino', '600111018', 'Bajo A', 'default.jpg', TRUE); 