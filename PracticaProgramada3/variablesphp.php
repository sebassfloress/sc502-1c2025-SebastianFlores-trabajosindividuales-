<?php
$transacciones = [];

function registroTransaccion($id, $descripcion, $monto) {
    global $transacciones;
    array_push($transacciones, [
        'id' => $id,
        'descripcion' => $descripcion,
        'monto' => $monto
    ]);
    echo "Transacción registrada: $descripcion - $$monto\n";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $descripcion = $_POST['descripcion'];
    $monto = $_POST['monto'];
    registroTransaccion($id, $descripcion, $monto);
}

function generarEstadoCuenta() {
    global $transacciones;
    $montoTotalContado = 0;
    $estadoCuenta = "Estado de Cuenta:\n\n";

    foreach ($transacciones as $transaccion) {
        $montoTotalContado += $transaccion['monto'];
        $estadoCuenta .= "Transacción #{$transaccion['id']}: {$transaccion['descripcion']} - \${$transaccion['monto']}\n";
    }

    $montoConIntereses = $montoTotalContado * 1.026;

    $cashBack = $montoTotalContado * 0.001;

    $montoFinal = $montoConIntereses - $cashBack;

    $estadoCuenta .= "\nMonto Total de Contado: \$$montoTotalContado\n";
    $estadoCuenta .= "Monto con Intereses (2.6%): \$$montoConIntereses\n";
    $estadoCuenta .= "Cash Back (0.1%): \$$cashBack\n";
    $estadoCuenta .= "Monto Final a Pagar: \$$montoFinal\n";

    echo $estadoCuenta;

    file_put_contents('estado_cuenta.txt', $estadoCuenta);
}


registroTransaccion(1, "Cancha de padel", 150.00);
registroTransaccion(2, "Recibo Luz", 200.00);
registroTransaccion(3, "Cena Novillo Alegre", 100.00);

generarEstadoCuenta();
?>