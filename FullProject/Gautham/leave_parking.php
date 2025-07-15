<head>
    <title>Parking Lot Token Checkout</title>
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
        .info:last-child {
            border-bottom: none;
        }
        .info strong {
            display: inline-block;
            width: 150px;
        }
    </style>
</head>
<body>
    <div class="container">
    <?php
        $servername="localhost";
        $username="root";
        $password="harshini26";
        $dbname="ParkingLot";

        $conn=new mysqli($servername,$username,$password,$dbname);
        if($conn->connect_error)
            die("Connection failed: ".$conn->connect_error);

        if($_SERVER['REQUEST_METHOD']=='POST'){
            $token=$_POST["Token"];
            
            if(!preg_match("/^[0-9A-Z]{6}$/",$token)){
                echo "Invalid Token Format";
                exit;
            }
            
            $stmt=$conn->prepare("SELECT pr.vehicle_number, pr.PType, pr.time_in, pr.time_out, cr.Vehicle_type FROM ParkingRecords pr JOIN CustomerRecords cr ON cr.vehicle_number = pr.vehicle_number WHERE pr.reference_id =?");
            $stmt->bind_param("s",$token);

            $stmt->execute();
            $stmt->bind_result($vehicle_number, $PType, $time_in, $time_out, $VType);

            if($stmt->fetch()){
                $stmt->close();
                $updateStmt=$conn->prepare(
                    "UPDATE CustomerRecords 
                    SET is_parked=FALSE 
                    WHERE vehicle_number=?"
                );
                $updateStmt->bind_param("s",$vehicle_number);

                if($updateStmt->execute()){
                    $updateStmt->close();

                    $checkoutStmt=$conn->prepare(
                        "UPDATE ParkingRecords 
                        SET time_out=CURRENT_TIMESTAMP, 
                            duration=TIMESTAMPDIFF(MINUTE,time_in,CURRENT_TIMESTAMP) 
                        WHERE reference_id=?"
                    );
                    $checkoutStmt->bind_param("s",$token);

                    if($checkoutStmt->execute()){
                        $checkoutStmt->close();

                        $displayStmt = $conn->prepare(
                            "SELECT pr.time_in, pr.time_out, pr.duration 
                            FROM ParkingRecords pr 
                            WHERE pr.reference_id=?"
                        );
                        $displayStmt->bind_param("s", $token);
                        $displayStmt->execute();
                        $displayStmt->bind_result($time_in, $time_out, $duration);
                        $displayStmt->fetch();
                        $displayStmt->close();
                    
                        $price = 0;
                        if (strcmp($VType, 'Two-Wheeler') === 0) {
                            $price = 30;
                            if ($duration > 60) {
                                $additional = $duration - 60;
                                $price += 15 * floor($additional / 30);
                            }
                        } elseif (strcmp($VType, 'Four-Wheeler') === 0) {
                            $price = 50;
                            if ($duration > 60) {
                                $additional = $duration - 60;
                                $price += 25 * floor($additional / 30);
                            }
                        }

                        // Update the price in ParkingRecords table
                        $priceUpdateStmt = $conn->prepare(
                            "UPDATE ParkingRecords 
                            SET vehicle_amt=? 
                            WHERE reference_id=?"
                        );
                        $priceUpdateStmt->bind_param("ds", $price, $token);
                        $priceUpdateStmt->execute();
                        $priceUpdateStmt->close();

                        // Check if the vehicle was parked in a carriage
                        if (strpos($PType, 'CARRIAGE') !== false) {
                            // Delete from Carriage table
                            $deleteCarriageStmt = $conn->prepare(
                                "DELETE FROM Carriage WHERE vehicle_number=?"
                            );
                            $deleteCarriageStmt->bind_param("s", $vehicle_number);

                            if ($deleteCarriageStmt->execute()) {
                                $deleteCarriageStmt->close();
                            } else {
                                echo "Error deleting from Carriage: " . $deleteCarriageStmt->error;
                                $deleteCarriageStmt->close();
                            }
                        }

                        // Check if the vehicle was parked with valet
                        if (strpos($PType, 'VALET') !== false) {
                            // Delete from Valet table
                            $deleteValetStmt = $conn->prepare(
                                "DELETE FROM Valet WHERE vehicle_number=?"
                            );
                            $deleteValetStmt->bind_param("s", $vehicle_number);

                            if ($deleteValetStmt->execute()) {
                                $deleteValetStmt->close();
                            } else {
                                echo "Error deleting from Valet: " . $deleteValetStmt->error;
                                $deleteValetStmt->close();
                            }
                        }

                        $displayStmt=$conn->prepare(
                            "SELECT cr.name, pr.reference_id, pr.time_in, pr.time_out, pr.duration, pr.vehicle_amt 
                            FROM CustomerRecords cr JOIN ParkingRecords pr ON cr.vehicle_number=pr.vehicle_number 
                            WHERE pr.reference_id=?"
                        );
                        $displayStmt->bind_param("s",$token);
                        $displayStmt->execute();
                        $displayStmt->bind_result($name,$reference_id,$time_in,$time_out,$duration,$price);
                        $displayStmt->fetch();

                        echo "<div class='info'><strong><h2>Parking Lot Exit</h2></strong></div>";
                        echo "<div class='info'><strong>Name:</strong> $name</div>";
                        echo "<div class='info'><strong>Reference ID:</strong> $reference_id</div>";
                        echo "<div class='info'><strong>Time In:</strong> $time_in</div>";
                        echo "<div class='info'><strong>Time Out:</strong> $time_out</div>";
                        echo "<div class='info'><strong>Duration (minutes):</strong>$duration </div>";
                        echo "<div class='info'><strong>Price:</strong> $price</div>";

                        $displayStmt->close();
                    } else {
                        echo "Error updating ParkingRecords: ".$checkoutStmt->error;
                        $checkoutStmt->close();
                    }
                } else {
                    echo "Error updating CustomerRecords: ".$updateStmt->error;
                    $updateStmt->close();
                }
            } else {
                echo "Vehicle not found";
            }
        }
    ?>
    </div>
</body>
</html>
