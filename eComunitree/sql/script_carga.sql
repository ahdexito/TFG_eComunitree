USE ecomunitree;

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
-- CARGAR DE SERVICIOS
-- ------------------------------------
INSERT INTO servicios (nombre, descripcion, telefono, email, enlace, activo) VALUES

-- Emergencias
('Emergencias 112', 'Teléfono general de emergencias', '112', NULL, NULL, TRUE),
('Policía Nacional', 'Atención policial', '091', 'contacto@policia.es', NULL, TRUE),
('Guardia Civil', 'Atención Guardia Civil', '062', 'info@guardiacivil.es', NULL, TRUE),
('Bomberos', 'Servicio de bomberos', '080', NULL, NULL, TRUE),
('Ambulancias', 'Emergencias sanitarias', '061', NULL, NULL, TRUE),
('Protección Civil', 'Atención protección civil', '1006', 'proteccioncivil@seguridad.es', NULL, TRUE),

-- Electricistas
('Electricista Gómez', 'Instalaciones y reparaciones eléctricas', '600123001', 'contacto@electricistagomez.es', NULL, TRUE),
('ElectroServicios Martínez', 'Especialistas en averías eléctricas', '600123002', 'info@electromartinez.es', NULL, TRUE),
('Reparaciones Eléctricas López', 'Servicio urgente 24h', '600123003', NULL, NULL, TRUE),

-- Fontaneros
('Fontanería Rápida SL', 'Reparaciones de fugas y tuberías', '600123004', 'info@fontaneriarapida.es', NULL, TRUE),
('Fontanero Pérez', 'Servicio económico', '600123005', NULL, NULL, TRUE),
('AquaServicios', 'Instalaciones de agua y desagües', '600123006', 'contacto@aquaservicios.es', NULL, TRUE),

-- Cerrajeros
('Cerrajería Segura', 'Apertura de puertas 24h', '600123007', 'urgencias@cerrajeriasegura.es', NULL, TRUE),
('Cerrajero Express', 'Urgencias en cerraduras', '600123008', NULL, NULL, TRUE),

-- Ascensores
('Ascensores Levanta', 'Mantenimiento y reparación de ascensores', '600123009', 'soporte@ascensoreslevanta.es', NULL, TRUE),
('Elevadores Modernos', 'Servicio técnico ascensores', '600123010', 'info@elevadoresmodernos.es', NULL, TRUE),

-- Limpieza
('Limpiezas Brillo', 'Limpieza de comunidades', '600123011', 'contacto@limpiezasbrillo.es', NULL, TRUE),
('Servicios Integrales Clean', 'Mantenimiento y limpieza integral', '600123012', NULL, NULL, TRUE),

-- Jardinería
('Jardinería VerdeVida', 'Mantenimiento de jardines', '600123013', 'info@verdevida.es', NULL, TRUE),
('Paisajismo Urbano', 'Diseño y cuidado de zonas verdes', '600123014', NULL, NULL, TRUE),

-- Telecomunicaciones
('Antenas Digitales SL', 'Instalación y reparación de antenas', '600123015', 'soporte@antenasdigitales.es', NULL, TRUE),
('Telecom Comunidad', 'Problemas de señal TV', '600123016', NULL, NULL, TRUE),

-- Seguridad
('Seguridad Total', 'Instalación de cámaras y alarmas', '600123017', 'info@seguridadtotal.es', NULL, TRUE),
('Alarmas Vecinales SL', 'Sistemas de seguridad', '600123018', NULL, NULL, TRUE),

-- Sistema contra incendios
('Extintores Seguros', 'Revisión de extintores', '600123019', 'contacto@extintoresseguros.es', NULL, TRUE),
('Protección Fuego SL', 'Sistemas antiincendios', '600123020', NULL, NULL, TRUE),

-- Garaje
('Puertas Automáticas García', 'Reparación de puertas de garaje', '600123021', 'info@puertasgarcia.es', NULL, TRUE),
('Motores Garaje 24h', 'Instalación y mantenimiento', '600123022', NULL, NULL, TRUE),

-- Carpintería
('Carpintería MaderaFina', 'Puertas y arreglos en madera', '600123023', 'contacto@maderafina.es', NULL, TRUE),
('Reformas López', 'Reparaciones estructurales', '600123024', NULL, NULL, TRUE),

-- Pintura
('Pinturas Modernas', 'Pintura de zonas comunes', '600123025', 'info@pinturasmodernas.es', NULL, TRUE),
('Decoraciones Ruiz', 'Acabados decorativos', '600123026', NULL, NULL, TRUE),

-- Otros
('Cristalería Central', 'Sustitución de cristales', '600123027', 'contacto@cristaleriacentral.es', NULL, TRUE),
('Control de Plagas Iberia', 'Tratamientos contra plagas', '600123028', 'info@plagasiberia.es', NULL, TRUE),
('Empresa Desatascos Norte', 'Desatascos urgentes', '600123029', NULL, NULL, TRUE),
('Climatización FríoCalor', 'Instalación aire acondicionado', '600123030', 'soporte@friocalor.es', NULL, TRUE),
('Administrador de Fincas Asociado', 'Gestión administrativa externa', '600123031', 'info@adminfincas.es', NULL, TRUE),
('Arquitecto Técnico Comunidad', 'Informes técnicos y peritajes', '600123032', 'contacto@arquitectotecnico.es', NULL, TRUE),
('Seguro Comunidad HogarPlus', 'Atención compañía aseguradora', '900123456', 'siniestros@hogarplus.es', NULL, TRUE);