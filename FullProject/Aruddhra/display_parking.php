<?php

require 'carriageps.php';

$parkingSystem = new CarriageParkingSystem('localhost', 'parkinglot', 'root', 'harshini26');

$parkingLot = $parkingSystem->displayParking();
echo json_encode($parkingLot);

?>
