<?php
session_start();
header('Content-Type: application/json');

require_once 'config/database.php';
require_once 'middleware/auth.php';
require_once 'controllers/comments.php';

$url = isset($_GET['url']) ? explode('/', rtrim($_GET['url'], '/')) : [];
$method = $_SERVER['REQUEST_METHOD'];

if ($url[0] === 'api' && $url[1] === 'comments') {
    if (!isAuthenticated()) {
        http_response_code(401);
        echo json_encode(['error' => 'No hay sesión activa. Inicie sesión.']);
        exit;
    }

    switch ($method) {
        case 'POST':
            if (empty($url[2])) {
                addComment($pdo);
            }
            break;
        case 'GET':
            if (empty($url[2])) {
                getComments($pdo);
            }
            break;
        case 'PUT':
            if (isset($url[2])) {
                updateComment($pdo, $url[2]);
            }
            break;
        case 'DELETE':
            if (isset($url[2])) {
                deleteComment($pdo, $url[2]);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            break;
    }
} elseif ($url[0] === 'api' && $url[1] === 'login') {
    if ($method === 'POST') {
        login($pdo);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Ruta no encontrada']);
}
?>