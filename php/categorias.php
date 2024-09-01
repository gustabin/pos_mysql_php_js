<?php
// Establecer el tipo de contenido de la respuesta como JSON
header('Content-Type: application/json');

// Incluir el archivo de conexión a la base de datos
require_once '../config/database.php';

try {
    // Verificar si se ha enviado una acción a través de la URL
    if (isset($_GET['action'])) {
        $action = $_GET['action']; // Obtener la acción especificada en la URL

        switch ($action) {
            case 'getAll':
                $columnas =   getAllCategories();
                echo json_encode(['data' => $columnas]);
                break;

            case 'create':
                if (isset($_POST['nombre'])) {
                    $nombre = trim($_POST['nombre']);
                    if (!empty($nombre)) {
                        if (createCategory($nombre)) {
                            echo json_encode(['status' => 'success', 'message' => 'Categoría creada exitosamente.']);
                        } else {
                            echo json_encode(['status' => 'error', 'message' => 'Error al crear la categoría.']);
                        }
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'El nombre no puede estar vacío.']);
                    }
                }
                break;

            case 'getById':
                // Verificar si se ha enviado un ID para obtener una categoría específica
                if (isset($_GET['id'])) {
                    $id = intval($_GET['id']); // Convertir el ID a un entero
                    $category = getCategoryById($id); // Obtener la categoría por ID                   
                    // Enviar la categoría obtenida en formato JSON
                    echo json_encode($category);
                } else {
                    // Enviar un mensaje de error si el ID no fue proporcionado
                    echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado.']);
                }
                break;

            case 'update':
                if (isset($_POST['id']) && isset($_POST['nombre'])) {
                    $id = intval($_POST['id']);
                    $nombre = trim($_POST['nombre']);
                    if (!empty($nombre)) {
                        $result = updateCategory($id, $nombre);
                        echo json_encode($result);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'El nombre no puede estar vacío.']);
                    }
                }
                break;

            case 'delete':
                // Verificar si se ha enviado un ID para eliminar una categoría
                if (isset($_POST['id'])) {
                    $id = intval($_POST['id']); // Convertir el ID a un entero
                    $result = deleteCategory($id); // Intentar eliminar la categoría
                    // Enviar el resultado de la eliminación como respuesta JSON
                    echo json_encode($result);
                }
                break;

            default:
                // Responder con un mensaje de error si la acción no es válida
                echo json_encode(['status' => 'error', 'message' => 'Acción no válida.']);
                break;
        }
    } else {
        // Enviar un mensaje de error si no se ha proporcionado una acción
        echo json_encode(['status' => 'error', 'message' => 'Acción no proporcionada.']);
    }
} catch (Exception $e) {
    // Capturar y mostrar cualquier error que ocurra durante la ejecución
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

// Función para obtener todas las categorías de la base de datos
function getAllCategories()
{
    global $conn; // Usar la conexión global a la base de datos
    $stmt = $conn->prepare('SELECT CategoriaID, Nombre FROM categorias'); // Preparar la consulta SQL
    $stmt->execute(); // Ejecutar la consulta
    $result = $stmt->get_result(); // Obtener el resultado de la consulta

    if (!$result) {
        // Lanzar una excepción si ocurre un error al obtener el resultado
        throw new Exception('Error al obtener el resultado: ' . $stmt->error);
    }

    $columnas = []; // Crear un array para almacenar las categorías
    while ($row = $result->fetch_assoc()) {
        // Agregar cada fila de resultado al array
        $columnas[] = [
            'CategoriaID' => htmlspecialchars($row['CategoriaID']), // Escapar HTML en el ID
            'Nombre' => htmlspecialchars($row['Nombre']), // Escapar HTML en el nombre
            'Acciones' => '<div class="text-right"><button class="btn btn-warning edit-btn" data-id="' . htmlspecialchars($row['CategoriaID']) . '"><i class="fas fa-pencil" aria-hidden="true"></i> Editar</button>' .
                ' &nbsp; <button class="btn btn-danger delete-btn" data-id="' . htmlspecialchars($row['CategoriaID']) . '"><i class="fas fa-trash" aria-hidden="true"></i> Eliminar</button></div>'
            // Agregar botones para editar y eliminar la categoría
        ];
    }
    $result->free(); // Liberar el resultado de la consulta
    $stmt->close(); // Cerrar la consulta
    $conn->close(); // Cerrar la conexión a la base de datos

    return $columnas; // Retornar el array de categorías
}

// Función para crear una nueva categoría
function createCategory($nombre)
{
    global $conn; // Usar la conexión global a la base de datos
    $sql = "INSERT INTO categorias (nombre) VALUES (?)"; // Preparar la consulta SQL
    $stmt = $conn->prepare($sql); // Preparar la consulta
    $stmt->bind_param("s", $nombre); // Vincular el parámetro nombre
    $result = $stmt->execute(); // Ejecutar la consulta y obtener el resultado
    $stmt->close(); // Cerrar la consulta
    return $result; // Retornar el resultado de la creación (true o false)
}

// Función para obtener una categoría por su ID
function getCategoryById($id)
{
    global $conn; // Usar la conexión global a la base de datos
    $sql = "SELECT * FROM categorias WHERE CategoriaID = ?"; // Preparar la consulta SQL
    $stmt = $conn->prepare($sql); // Preparar la consulta
    $stmt->bind_param("i", $id); // Vincular el parámetro ID
    $stmt->execute(); // Ejecutar la consulta
    $result = $stmt->get_result(); // Obtener el resultado de la consulta
    $data = $result->fetch_assoc(); // Obtener la fila de resultado
    $stmt->close(); // Cerrar la consulta
    return $data; // Retornar la categoría obtenida
}

// Función para actualizar una categoría existente
function updateCategory($id, $nombre)
{
    global $conn; // Usar la conexión global a la base de datos
    // Verificar si el CategoriaID existe
    $stmt = $conn->prepare('SELECT COUNT(*) FROM categorias WHERE CategoriaID = ?'); // Preparar la consulta
    $stmt->bind_param('i', $id); // Vincular el parámetro ID
    $stmt->execute(); // Ejecutar la consulta
    $stmt->bind_result($count); // Obtener el resultado
    $stmt->fetch(); // Obtener el valor del resultado
    $stmt->close(); // Cerrar la consulta

    if ($count === 0) {
        // Retornar error si la categoría no existe
        return ['status' => 'error', 'message' => 'La categoría no existe.'];
    }

    $sql = "UPDATE categorias SET nombre = ? WHERE CategoriaID = ?"; // Preparar la consulta SQL
    $stmt = $conn->prepare($sql); // Preparar la consulta
    $stmt->bind_param("si", $nombre, $id); // Vincular los parámetros nombre e ID
    $result = $stmt->execute(); // Ejecutar la consulta y obtener el resultado
    $stmt->close(); // Cerrar la consulta
    return $result ? ['status' => 'success', 'message' => 'Categoría actualizada exitosamente.'] : ['status' => 'error', 'message' => 'Error al actualizar la categoría.'];
}

// Función para eliminar una categoría existente
function deleteCategory($id)
{
    global $conn; // Usar la conexión global a la base de datos
    // Verificar si el CategoriaID existe
    $stmt = $conn->prepare('SELECT COUNT(*) FROM categorias WHERE CategoriaID = ?'); // Preparar la consulta
    $stmt->bind_param('i', $id); // Vincular el parámetro ID
    $stmt->execute(); // Ejecutar la consulta
    $stmt->bind_result($count); // Obtener el resultado
    $stmt->fetch(); // Obtener el valor del resultado
    $stmt->close(); // Cerrar la consulta

    if ($count === 0) {
        // Retornar error si la categoría no existe
        return ['status' => 'error', 'message' => 'La categoría no existe.'];
    }

    $sql = "DELETE FROM categorias WHERE CategoriaID = ?"; // Preparar la consulta SQL
    $stmt = $conn->prepare($sql); // Preparar la consulta
    $stmt->bind_param("i", $id); // Vincular el parámetro ID
    $result = $stmt->execute(); // Ejecutar la consulta y obtener el resultado
    $stmt->close(); // Cerrar la consulta
    return $result ? ['status' => 'success', 'message' => 'Categoría eliminada exitosamente.'] : ['status' => 'error', 'message' => 'Error al eliminar la categoría.'];
}
