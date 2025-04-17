<?php
function addComment($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $task_id = isset($data['task_id']) ? (int)$data['task_id'] : null;
    $content = isset($data['content']) ? trim($data['content']) : null;
    $user_id = $_SESSION['user_id'];

    if (!$task_id || !$content) {
        http_response_code(400);
        echo json_encode(['error' => 'Faltan campos requeridos']);
        return;
    }
    if (strlen($content) < 5) {
        http_response_code(400);
        echo json_encode(['error' => 'El comentario debe tener al menos 5 caracteres']);
        return;
    }

    try {
        $stmt = $pdo->prepare('SELECT id FROM tasks WHERE id = ?');
        $stmt->execute([$task_id]);
        if (!$stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['error' => 'La tarea no existe']);
            return;
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al verificar tarea: ' . $e->getMessage()]);
        return;
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO comments (task_id, user_id, content) VALUES (?, ?, ?)');
        $stmt->execute([$task_id, $user_id, $content]);
        http_response_code(201);
        echo json_encode(['id' => $pdo->lastInsertId(), 'task_id' => $task_id, 'user_id' => $user_id, 'content' => $content]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al agregar comentario: ' . $e->getMessage()]);
    }
}

function getComments($pdo) {
    $task_id = isset($_GET['task_id']) ? (int)$_GET['task_id'] : null;

    if (!$task_id) {
        http_response_code(400);
        echo json_encode(['error' => 'El task_id es requerido']);
        return;
    }

    try {
        $stmt = $pdo->prepare('SELECT * FROM comments WHERE task_id = ?');
        $stmt->execute([$task_id]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($comments);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al listar comentarios: ' . $e->getMessage()]);
    }
}

function updateComment($pdo, $id) {
    $data = json_decode(file_get_contents('php://input'), true);
    $content = isset($data['content']) ? trim($data['content']) : null;
    $user_id = $_SESSION['user_id'];

    if (!$content) {
        http_response_code(400);
        echo json_encode(['error' => 'El contenido es requerido']);
        return;
    }
    if (strlen($content) < 5) {
        http_response_code(400);
        echo json_encode(['error' => 'El comentario debe tener al menos 5 caracteres']);
        return;
    }

    try {
        $stmt = $pdo->prepare('UPDATE comments SET content = ? WHERE id = ? AND user_id = ?');
        $stmt->execute([$content, $id, $user_id]);
        if ($stmt->rowCount() === 0) {
            http_response_code(403);
            echo json_encode(['error' => 'No autorizado o comentario no encontrado']);
            return;
        }
        echo json_encode(['message' => 'Comentario actualizado']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al actualizar comentario: ' . $e->getMessage()]);
    }
}

function deleteComment($pdo, $id) {
    $user_id = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare('DELETE FROM comments WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $user_id]);
        if ($stmt->rowCount() === 0) {
            http_response_code(403);
            echo json_encode(['error' => 'No autorizado o comentario no encontrado']);
            return;
        }
        echo json_encode(['message' => 'Comentario eliminado']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al eliminar comentario: ' . $e->getMessage()]);
    }
}

function login($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = isset($data['username']) ? trim($data['username']) : null;
    $password = isset($data['password']) ? trim($data['password']) : null;

    if (!$username || !$password) {
        http_response_code(400);
        echo json_encode(['error' => 'Faltan credenciales']);
        return;
    }

    try {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? AND password = ?');
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            echo json_encode(['message' => 'Sesión iniciada']);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Credenciales inválidas']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al iniciar sesión: ' . $e->getMessage()]);
    }
}
?>