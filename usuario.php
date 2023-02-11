<?php 

// Importar la conexion
require './includes/config/database.php';
$db = conectarDB();

// Crear un email y password
$email = "correo@correo.com";
$password = "123456";

// Hashear el password
$passwordHash = password_hash($password, PASSWORD_BCRYPT);

// Query para crear el usuario
$query = "INSERT INTO usuarios (email, password) VALUES ('$email', '$passwordHash')";

// Agregarlo a la base de datos
mysqli_query($db, $query);

mysqli_close($db);
