<?php
// conexion.php

$servername = "localhost";
$username   = "root";       // usuario de phpMyAdmin (por defecto en XAMPP es root)
$password   = "";           // contraseña (en XAMPP normalmente está vacía)
$dbname     = "alumnos";    // aquí va el nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>