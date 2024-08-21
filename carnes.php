<?php
require_once 'database.php';

function criarCarnes($data) {
    $valor_total = $data['valor_total'];
    $qtd_parcelas = $data['qtd_parcelas'];
    $data_primeiro_vencimento = $data['data_primeiro_vencimento'];
    $periodicidade = $data['periodicidade'];
    $valor_entrada = isset($data['valor_entrada']) ? $data['valor_entrada'] : 0;

    $parcelas = [];
    $total_parcelas = $valor_total - $valor_entrada;

    if ($valor_entrada > 0) {
        $parcelas[] = [
            'data_vencimento' => $data_primeiro_vencimento,
            'valor' => $valor_entrada,
            'numero' => 1,
            'entrada' => true
        ];
    }

    if ($total_parcelas > 0) {
        $valor_parcela = $total_parcelas / $qtd_parcelas;
        $current_date = $data_primeiro_vencimento;

        for ($i = 1; $i <= $qtd_parcelas; $i++) {
            if ($i > 1) {
                $current_date = calcularProximaData($current_date, $periodicidade);
            }
            $parcelas[] = [
                'data_vencimento' => $current_date,
                'valor' => $valor_parcela,
                'numero' => $i + ($valor_entrada > 0 ? 1 : 0),
                'entrada' => false
            ];
        }
    }

    salvarCarnes($valor_total, $valor_entrada, $parcelas);

    return [
        'total' => $valor_total,
        'valor_entrada' => $valor_entrada,
        'parcelas' => $parcelas
    ];
}

function recuperarParcelas($id) {
    return buscarCarnes($id);
}

function calcularProximaData($data, $periodicidade) {
    $date = new DateTime($data);
    if ($periodicidade === 'mensal') {
        $date->modify('+1 month');
    } elseif ($periodicidade === 'semanal') {
        $date->modify('+1 week');
    }
    return $date->format('Y-m-d');
}
?>
