<?php
$servername = "localhost";
$username = "root";
$password = "harshini26";
$dbname = "parkinglot";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['Token'])) {
    $reference_id = $_POST['Token'];

    if (strlen($reference_id) != 6) {
        echo json_encode(["status" => "invalid_format"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT pr.vehicle_number, pr.time_in, pr.PType, cr.vehicle_type, cr.name FROM ParkingRecords pr JOIN CustomerRecords cr ON pr.vehicle_number = cr.vehicle_number WHERE pr.reference_id = ? AND cr.is_parked = true");
    $stmt->bind_param("s", $reference_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($vehicle_number, $time_in, $PType, $VType, $name);
        $stmt->fetch();

        // Calculate the duration and price
        $time_in = new DateTime($time_in);
        $time_out = new DateTime(); // Assuming current time as time_out
        $duration = $time_in->diff($time_out)->i + ($time_in->diff($time_out)->h * 60);
        
        $price = 0;
        if (strcmp($VType, 'Two-Wheeler') === 0) {
            $price=30;
            if($duration>60){
                $Additional=$duration-60;
                $price+=15* floor($Additional/30);
            }
        } elseif (strcmp($VType, 'Four-Wheeler') === 0) {
            $price=50;
            if($duration>60){
                $price=10;
                $Additional=$duration-60;
                $price+=25*floor($Additional/30);
            }
        }

        echo json_encode([
            "status" => "exists",
            "duration" => $duration,
            "price" => $price,
            "name" => $name,
            "time_in" => $time_in->format('Y-m-d H:i:s')
        ]);
    } else {
        echo json_encode(["status" => "not_exists"]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "SQL error", "error" => $conn->error]);
}
$conn->close();
?>
