<?php
session_start();

// Credenciales simuladas
$email = $_POST['email'];
$password = $_POST['password'];

if ($email === 'test@example.com' && $password === 'password123') {
    $_SESSION['usuario_logueado'] = $email;
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "mensaje" => "Credenciales inválidas"]);
}
?>

