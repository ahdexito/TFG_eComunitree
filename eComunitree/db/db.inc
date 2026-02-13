<?php
    //especifico para bases de datos mysql

    $host = "localhost";//Dirección del servidor
    $nombreBD = "ecomunitree";//Nombre de la BD
    $usuario = "root";//usuario con acceso a la BD
    $password = "";//Contraseña del usuario
    //crear conexión
    $conn = new mysqli($host, $usuario, $password, $nombreBD);
    //verificar conexión
    if ($conn->connect_error) {
        die("Conexión fallida - ERROR DE CONEXIÓN: " . $conn -> connect_error);
    }
?>