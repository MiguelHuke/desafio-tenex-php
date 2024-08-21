<?php
header('Content-Type: application/json');
require_once 'carnes.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = isset($_GET['path']) ? $_GET['path'] : '';

switch ($method) {
    case 'POST':
        if ($path === 'criar-carnes') {
            $data = json_decode(file_get_contents('php://input'), true);
            $response = criarCarnes($data);
            echo json_encode($response);
        }
        break;

    case 'GET':
        if ($path === 'recuperar-parcelas') {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $response = recuperarParcelas($id);
            echo json_encode($response);
        }
        break;

    default:
        echo json_encode(['error' => 'Método não suportado']);
        break;
}
?>
