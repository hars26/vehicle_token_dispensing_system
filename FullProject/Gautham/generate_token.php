<!DOCTYPE html>
<html lang="en">
<head>
    <title>Parking Lot</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e9d1d1;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .info {
            margin-bottom: 10px;
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        .info strong {
            display: inline-block;
            width: 120px;
        }
    </style>
</head>
<body>
<div class="container">
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "harshini26";
    $dbname = "ParkingLot";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    function generateReferenceId($length = 6)
    {
        return substr(str_shuffle(str_repeat('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', $length)), 0, $length);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $vehicle_number = $_POST["vehicle_number"];
    
        if (!preg_match('/^[A-Z]{2} \d{2} [A-Z]{2} \d{4}$/', $vehicle_number)) {
            echo "Invalid vehicle number format.";
            exit;
        }
    
        $PType = isset($_POST['PType']) ? $_POST['PType'] : '';
        $VType = isset($_POST['VType']) ? $_POST['VType'] : '';
        $token = '';

        // Initialize variable to store textual representation of PType
        $PTypeText = [];
    
        // Check if CARRIAGE is selected
        if ($PType == "2") {
            $carriageNum = 1;
            $spaceNum = 0;
            $carriageFound = false;
            
            // Find the first available carriage and space
            for ($carriageNum = 1; $carriageNum <= 5; $carriageNum++) {
                $stmt = $conn->prepare("SELECT space_number FROM Carriage WHERE carriage_number = ? ORDER BY space_number DESC LIMIT 1");
                $stmt->bind_param("i", $carriageNum);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                if ($row) {
                    $spaceNum = $row['space_number'] + 1;
                } else {
                    $spaceNum = 1; // Start from space number 1 if no space is found
                }
    
                if ($spaceNum <= 10) {
                    $carriageFound = true;
                    break;
                }
            }
    
            // Check if a carriage with available space was found
            if ($carriageFound) {
                // Insert into Carriage table
                $stmt = $conn->prepare("INSERT INTO Carriage (carriage_number, space_number, vehicle_number) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $carriageNum, $spaceNum, $vehicle_number);
                if ($stmt->execute()) {
                    $PTypeText[] = "CARRIAGE"; // Add CARRIAGE to $PTypeText
                    $token = "C" . str_pad($carriageNum, 2, '0', STR_PAD_LEFT) . str_pad($spaceNum, 2, '0', STR_PAD_LEFT);
                } else {
                    echo "Error inserting into Carriage table: " . $stmt->error;
                }
            } else {
                echo "Parking is full in all CARRIAGES.";
            }
        }
    
        // Check if VALET is selected
        if ($PType == "1") {
            $PTypeText[] = "VALET"; // Add VALET to $PTypeText
    
            $vehicle_type = ($VType == "1") ? "Two-Wheeler" : "Four-Wheeler";
            $reference_id = generateReferenceId();
    
            // Find the first available slot for valet
            $stmt = $conn->prepare("SELECT MAX(slot_number) AS max_slot FROM Valet");
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $slot_number = $row['max_slot'] ? $row['max_slot'] + 1 : 1;
            $token = "V" . str_pad($slot_number, 3, '0', STR_PAD_LEFT);

            // Insert into Valet table
            $stmt = $conn->prepare("INSERT INTO Valet (reference_id, vehicle_number, vehicle_type, slot_number) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $reference_id, $vehicle_number, $vehicle_type, $slot_number);
            if (!$stmt->execute()) {
                echo "Error inserting into Valet table: " . $stmt->error;
            }
    
            // Insert into ValetRecords table
            $stmt = $conn->prepare("INSERT INTO ValetRecords (reference_id, vehicle_number, vehicle_type, slot_number) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $reference_id, $vehicle_number, $vehicle_type, $slot_number);
            if (!$stmt->execute()) {
                echo "Error inserting into ValetRecords table: " . $stmt->error;
            }
        }
    
        // Implode to create comma-separated string
        $PType = implode(", ", $PTypeText);
    
        // Insert into CustomerRecords table
        if ($_POST['dataNew'] == 'true') {
            $name = $_POST["name"];
            $phone_number = $_POST["phone_number"];
            $email = $_POST["email"];
            $vehicle_type = ($VType == "1") ? "Two-Wheeler" : "Four-Wheeler";
    
            $stmt = $conn->prepare("INSERT INTO CustomerRecords (vehicle_number, name, phone_number, email, Vehicle_type, is_parked) VALUES (?, ?, ?, ?, ?, TRUE)");
            $stmt->bind_param("sssss", $vehicle_number, $name, $phone_number, $email, $vehicle_type);
            if ($stmt->execute()) {
                echo "<h1>TOKEN</h1>";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $stmt = $conn->prepare("UPDATE CustomerRecords SET is_parked=TRUE WHERE vehicle_number=?");
            $stmt->bind_param("s", $vehicle_number);
    
            if ($stmt->execute()) {
                echo "";
            } else {
                echo "Error updating customer record: " . $stmt->error;
            }
    
            $stmt->close();
        }
    
        // Generate reference ID and insert into ParkingRecords table
        $reference_id = generateReferenceId();
    
        $stmt = $conn->prepare("INSERT INTO ParkingRecords (reference_id, vehicle_number, PType, token_number) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $reference_id, $vehicle_number, $PType, $token);
        if ($stmt->execute()) {
            // Retrieve details from CustomerRecords and ParkingRecords for display
            $stmt = $conn->prepare("SELECT cr.name, pr.time_in FROM CustomerRecords cr JOIN ParkingRecords pr ON cr.vehicle_number = pr.vehicle_number WHERE pr.reference_id = ?");
            $stmt->bind_param("s", $reference_id);
            $stmt->execute();
            $stmt->bind_result($name, $time_in);
            $stmt->fetch();
            echo "<div class='info'><strong>Token Number:</strong> $token</div>"; // Display token number
            echo "<div class='info'><strong>Name:</strong> $name</div>";
            echo "<div class='info'><strong>Reference ID:</strong> $reference_id</div>";
            echo "<div class='info'><strong>Vehicle Number:</strong> $vehicle_number</div>";
            echo "<div class='info'><strong>Parking Type:</strong> $PType</div>";
            echo "<div class='info'><strong>Time In:</strong> $time_in</div>";
        } else {
            echo "Error generating reference ID: " . $stmt->error;
        }
        $stmt->close();
    }
    
    $conn->close();
    ?>
</div>
</body>
</html>
