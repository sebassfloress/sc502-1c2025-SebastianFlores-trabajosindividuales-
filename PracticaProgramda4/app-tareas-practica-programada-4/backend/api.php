<?php
session_start();
if (!isset($_SESSION['usuario_logueado'])) {
    echo json_encode(["success" => false, "mensaje" => "Acceso denegado. Sesión no activa."]);
    exit;
}


$conexion = new mysqli("localhost", "root", "", "tareas_db_modificada");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}


$accion = $_GET['accion'] ?? $_POST['accion'] ?? null;

switch ($accion) {
    case 'crearComentario':  
        $tarea_id = $_POST['tarea_id'];
        $texto = $_POST['texto'];

        $sql = "INSERT INTO comentarios (tarea_id, texto) VALUES (?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("is", $tarea_id, $texto);
        $stmt->execute();

        echo json_encode(["success" => true, "id" => $conexion->insert_id]);
        break;

    case 'borrarComentario':  
        $id = $_POST['id'];

        $sql = "DELETE FROM comentarios WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        echo json_encode(["success" => true]);
        break;

    case 'traerComentarios':  
        $tarea_id = $_GET['tarea_id'];

        $sql = "SELECT * FROM comentarios WHERE tarea_id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $tarea_id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $comentarios = [];
        while ($fila = $resultado->fetch_assoc()) {
            $comentarios[] = $fila;
        }

        echo json_encode($comentarios);
        break;

    case 'actualizar':
        parse_str(file_get_contents("php://input"), $_PUT);
        $id = $_PUT['id'];
        $texto = $_PUT['texto'];

        $sql = "UPDATE comentarios SET texto = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("si", $texto, $id);
        $stmt->execute();

        echo json_encode(["success" => true]);
        break;

    default:
        echo json_encode(["success" => false, "mensaje" => "Acción no válida"]);
        break;
}
?>
