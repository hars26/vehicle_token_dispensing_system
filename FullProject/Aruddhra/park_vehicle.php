<?php

require 'carriageps.php';

$parkingSystem = new CarriageParkingSystem('localhost', 'parkinglot', 'root', 'harshini26');

$data = json_decode(file_get_contents('php://input'), true);
$vehicleNumber = $data['vehicleNumber'];
$vehicleType = $data['vehicleType'];

$token = $parkingSystem->parkVehicle($vehicleNumber, $vehicleType);
echo json_encode(['token' => $token]);

?>
