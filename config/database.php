<?php
// Configuración del servidor de base de datos

//Localhost
if ($_SERVER['SERVER_NAME'] == 'localhost') {
    $servername = "localhost"; // Cambia esto si es necesario, es el nombre del servidor de la base de datos
    $username = "root"; // Cambia esto por tu usuario de la base de datos
    $password = ""; // Cambia esto por tu contraseña de la base de datos
    $dbname = "pos"; // Cambia esto por el nombre de tu base de datos
}

// remoto
if (($_SERVER['SERVER_NAME'] == 'stackcodelab.com') or ($_SERVER['SERVER_NAME'] == 'www.stackcodelab.com')) {
    $servername = "localhost"; // Cambia esto si es necesario, es el nombre del servidor de la base de datos
    $username = "stackcod_operador"; // Cambia esto por tu usuario de la base de datos
    $password = "1234567890qwerty"; // Cambia esto por tu contraseña de la base de datos
    $dbname = "stackcod_pos"; // Cambia esto por el nombre de tu base de datos
}

try {
    // Crear conexión con la base de datos usando MySQLi
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar si hubo un error al conectar con la base de datos
    if ($conn->connect_error) {
        throw new Exception('Error de conexión: ' . $conn->connect_error);
    }

    // Establecer el conjunto de caracteres a UTF-8 para evitar problemas de codificación
    $conn->set_charset('utf8');
} catch (Exception $e) {
    // Manejo de errores con excepción
    header('Content-Type: application/json');
    error_log($e->getMessage()); // Registra el error en un archivo de log
    echo json_encode(["error" => "No se pudo conectar a la base de datos. Por favor, intente nuevamente más tarde."]);
    exit;
}
