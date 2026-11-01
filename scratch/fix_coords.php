<?php
require_once __DIR__ . '/../paozin-app/config/Database.php';
require_once __DIR__ . '/../paozin-app/models/Padaria.php';

$db = Database::getConnection();
// Atualiza as coordenadas da padaria #3 para a localização real
$stmt = $db->prepare("UPDATE padarias SET latitude = ?, longitude = ? WHERE id = ?");
$stmt->execute([-23.680265, -46.780685, 3]);

$lista = $db->query("SELECT id, nome, endereco, latitude, longitude FROM padarias")->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($lista, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
