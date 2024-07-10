<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

$validCommands = array("Matikan Lampu", "Nyalakan Lampu Merah", "Nyalakan Lampu Hijau", "Nyalakan Lampu Biru");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

$file = 'data.csv';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $data = readDataFromCSV($file);
    echo json_encode($data);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);

    if (isset($input['command']) && in_array($input['command'], $validCommands)) {
        $data = array(
            "command" => $input['command'],
            "timestamp" => date('Y-m-d H:i:s')
        );

        saveDataToCSV($file, $data);

        echo json_encode(array(
            "status" => "success",
            "message" => "Data saved successfully",
            "data" => $data
        ));
    } else {
        http_response_code(400);
        echo json_encode(array(
            "status" => "error",
            "message" => "Invalid input"
        ));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method Not Allowed"));
}

function readDataFromCSV($file) {
    $data = array();
    if (($handle = fopen($file, "r")) !== false) {
        while (($row = fgetcsv($handle)) !== false) {
            $data[] = array(
                "command" => $row[0],
                "timestamp" => $row[1]
            );
        }
        fclose($handle);
    }
    return $data;
}

function saveDataToCSV($file, $data) {
    $fp = fopen($file, 'w');
    fputcsv($fp, $data);
    fclose($fp);
}

?>
