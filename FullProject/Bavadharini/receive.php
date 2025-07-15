<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Lot</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .parking-container {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            justify-items: center;
            margin: 20px;
        }
        .parking-space {
            width: 120px;
            height: 120px;
            border: 1px solid #ccc;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 4px;
            font-weight: bold;
            position: relative;
        }
        .occupied {
            background-color: #f0f0f0;
            color: #666;
        }
        .available {
            background-color: #b3ffb3;
            color: #333;
        }
        .tooltip-inner {
            max-width: none;
            white-space: nowrap;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Parking Spaces</h1>
    <div class="parking-container">
        <?php
        $servername = "localhost";
        $username = "root";
        $password = "harshini26";
        $dbname = "ParkingLot";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Total number of parking spaces
        $totalSpaces = 20;
        $occupiedSpaces = [];

        // Retrieve all occupied spaces and their vehicle numbers
        $result = $conn->query("SELECT vehicle_number FROM Valet LIMIT $totalSpaces");
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $occupiedSpaces[] = $row['vehicle_number'];
            }
        }

        // Loop through each parking space and determine if it's occupied
        for ($i = 1; $i <= $totalSpaces; $i++) {
            $class = isset($occupiedSpaces[$i - 1]) ? 'occupied' : 'available';
            $vehicleNumber = isset($occupiedSpaces[$i - 1]) ? $occupiedSpaces[$i - 1] : '';
            $tooltip = $vehicleNumber ? "data-toggle='tooltip' title='Vehicle: $vehicleNumber'" : '';
            echo "<div class='parking-space $class' $tooltip>Space $i</div>";
        }

        $conn->close();
        ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
</body>
</html>
