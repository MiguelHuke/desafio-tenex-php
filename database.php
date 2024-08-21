<?php
// Simula um "banco de dados" com um array
$database = [];

function salvarCarnes($valor_total, $valor_entrada, $parcelas) {
    global $database;
    $id = count($database) + 1;
    $database[$id] = [
        'total' => $valor_total,
        'valor_entrada' => $valor_entrada,
        'parcelas' => $parcelas
    ];
    return $id;
}

function buscarCarnes($id) {
    global $database;
    if (isset($database[$id])) {
        return $database[$id];
    } else {
        return ['error' => 'Carnê não encontrado'];
    }
}
?>
