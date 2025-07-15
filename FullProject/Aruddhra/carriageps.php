<?php

class CarriageParkingSystem {
    private $conn;

    public function __construct($host, $dbname, $username, $password) {
        $this->conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function parkVehicle($vehicleNum, $vehicleType) {
        $startCarriage = ($vehicleType == 'four_wheeler') ? 1 : 3;
        $endCarriage = ($vehicleType == 'four_wheeler') ? 2 : 5;

        for ($carriageNum = $startCarriage; $carriageNum <= $endCarriage; $carriageNum++) {
            $stmt = $this->conn->prepare("SELECT space_number FROM carriage WHERE carriage_number = ? ORDER BY space_number DESC LIMIT 1");
            $stmt->execute([$carriageNum]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $spaceNum = ($result) ? $result['space_number'] + 1 : 1;

            if ($spaceNum <= 10) {
                $stmt = $this->conn->prepare("INSERT INTO carriage (carriage_number, space_number, vehicle_number, vehicle_type) VALUES (?, ?, ?, ?)");
                $stmt->execute([$carriageNum, $spaceNum, $vehicleNum, $vehicleType]);
                return "Token: Vehicle Number: $vehicleNum, Carriage Number: $carriageNum, Space Number: $spaceNum";
            }
        }
        return "Parking is full!";
    }

    public function displayParking() {
        $stmt = $this->conn->query("SELECT * FROM carriage ORDER BY carriage_number, space_number");
        $parkingLot = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $carriages = array_fill(0, 5, array_fill(0, 10, ["status" => "Empty", "vehicle_number" => "", "token" => ""]));
        foreach ($parkingLot as $row) {
            $carriages[$row['carriage_number'] - 1][$row['space_number'] - 1] = [
                "status" => $row['vehicle_number'],
                "vehicle_number" => $row['vehicle_number'],
                "token" => "Carriage " . $row['carriage_number'] . ", Space " . $row['space_number']
            ];
        }

        return $carriages;
    }
}
?>
