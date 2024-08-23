<?php
include 'database.php'; // Inclui o arquivo de conexão

// Definir o caminho para a API
$path = $_GET['path'] ?? '';

switch ($path) {
    case 'criar-carnes':
        criarCarnes();
        break;
    case 'recuperar-parcelas':
        recuperarParcelas();
        break;
    default:
        echo json_encode(["error" => "Endpoint não encontrado"]);
        break;
}

function criarCarnes() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['valor_total'], $data['qtd_parcelas'], $data['data_primeiro_vencimento'], $data['periodicidade'])) {
        echo json_encode(["error" => "Parâmetros inválidos"]);
        return;
    }

    $valor_total = $data['valor_total'];
    $qtd_parcelas = $data['qtd_parcelas'];
    $data_primeiro_vencimento = $data['data_primeiro_vencimento'];
    $periodicidade = $data['periodicidade'];
    $valor_entrada = $data['valor_entrada'] ?? 0;

    $parcelas = [];

    if ($valor_entrada > 0) {
        $parcelas[] = [
            'data_vencimento' => $data_primeiro_vencimento,
            'valor' => $valor_entrada,
            'numero' => 1,
            'entrada' => true
        ];
        $valor_total -= $valor_entrada;
        $qtd_parcelas--;
    }

    $valor_parcela = $valor_total / $qtd_parcelas;
    $current_date = new DateTime($data_primeiro_vencimento);

    for ($i = 1; $i <= $qtd_parcelas; $i++) {
        if ($periodicidade == 'mensal') {
            $current_date->add(new DateInterval('P1M'));
        } elseif ($periodicidade == 'semanal') {
            $current_date->add(new DateInterval('P1W'));
        }
        $parcelas[] = [
            'data_vencimento' => $current_date->format('Y-m-d'),
            'valor' => $valor_parcela,
            'numero' => $i + 1,
            'entrada' => false
        ];

        // Preparar e executar a inserção no banco de dados
        $stmt = $conn->prepare("INSERT INTO parcelas (valor, data_vencimento, numero, entrada) VALUES (?, ?, ?, ?)");
        
        // Definir variáveis para bind_param
        $valor = $valor_parcela;
        $data_vencimento = $current_date->format('Y-m-d');
        $numero = $i + 1;
        $entrada = $i === 0 && $valor_entrada > 0; // Correção para a entrada

        // Use bind_param passando as variáveis
        $stmt->bind_param("dsii", $valor, $data_vencimento, $numero, $entrada);

        // Executar a instrução
        $stmt->execute();
    }

    $response = [
        'total' => $data['valor_total'],
        'valor_entrada' => $valor_entrada,
        'parcelas' => $parcelas
    ];

    echo json_encode($response);
}

function recuperarParcelas() {
    global $conn;
    $id = $_GET['id'] ?? 0;

    $stmt = $conn->prepare("SELECT * FROM parcelas WHERE id_carnes = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $parcelas = [];
    while ($row = $result->fetch_assoc()) {
        $parcelas[] = [
            'data_vencimento' => $row['data_vencimento'],
            'valor' => $row['valor'],
            'numero' => $row['numero'],
            'entrada' => (bool)$row['entrada']
        ];
    }

    $response = [
        'total' => 0, // Aqui você deve calcular o total corretamente
        'valor_entrada' => 0, // Aqui você deve calcular o valor de entrada corretamente
        'parcelas' => $parcelas
    ];

    echo json_encode($response);
}
?>
