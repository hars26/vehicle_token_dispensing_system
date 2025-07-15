<?php
$servername = "localhost";
$username = "root";
$password = "harshini26";
$dbname = "parkinglot";  

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if (isset($_POST['vehicle_no'])) {
    $vehicle_no = $_POST['vehicle_no'];
    $vehicle_no = mysqli_real_escape_string($conn, $vehicle_no);

    $sql = "SELECT reference_id, vehicle_number, PType, time_in, time_out, duration, vehicle_amt 
            FROM ParkingRecords
            WHERE vehicle_number = '$vehicle_no'";

    $result = $conn->query($sql);

    if ($result === false) {
        die("SQL error: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        $message .= "<table border='1'>
                     <tr>
                        <th>Reference ID</th>
                        <th>Vehicle Number</th>
                        <th>Parking Type</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Duration (hours)</th>
                        <th>Amount</th>
                     </tr>";
        while ($row = $result->fetch_assoc()) {
            $duration = $row["duration"];
            if ($duration >= 60) {
                $hours = floor($duration / 60);
                $minutes = $duration % 60;
                $formatted_duration = sprintf('%02d : %02d', $hours, $minutes);
            } else {
                $hours = 0;
                $minutes = $duration;
                $formatted_duration = sprintf('%02d : %02d', $hours, $minutes);
            }

            $message .= "<tr>
                            <td>" . htmlspecialchars($row["reference_id"]) . "</td>
                            <td>" . htmlspecialchars($row["vehicle_number"]) . "</td>
                            <td>" . htmlspecialchars($row["PType"]) . "</td>
                            <td>" . htmlspecialchars($row["time_in"]) . "</td>
                            <td>" . htmlspecialchars($row["time_out"]) . "</td>
                            <td>" . htmlspecialchars($formatted_duration) . "</td>
                            <td>" . htmlspecialchars($row["vehicle_amt"]) . "</td>
                         </tr>";
        }
        $message .= "</table>";
    } else {
        $message = "No results found for the vehicle number: " . htmlspecialchars($vehicle_no);
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Search Results</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: black;
            margin: 0;
            padding: 0;
            color: #f0e5e5;
        }
        .header {
            background-color: rgb(15, 205, 231);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 10px 0;
            text-align: center;
        }
        .header img {
            height: 40px;
        }
        .header h1 {
            display: inline-block;
            margin: 0;
            font-size: 24px;
            color: #030000;
        }
        .result-container {
            padding: 20px;
            text-align: center; 
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            color: white; 
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: rgb(15, 205, 231);
            color: #030000;
        }
        .button-container {
            display: flex;
            justify-content: center; 
            margin-top: 20px;
        }
        .btn {
            --hue: 190;
            position: relative;
            padding: 1rem 3rem;
            font-size: 1rem;
            line-height: 1.5;
            color: white;
            text-decoration: none;
            text-transform: uppercase;
            background-color: hsl(var(--hue), 100%, 41%);
            border: 1px solid hsl(var(--hue), 100%, 41%);
            outline: transparent;
            overflow: hidden;
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
            transition: 0.25s;
            margin: 10px;
        }
        .btn:hover {
            background: hsl(var(--hue), 100%, 31%);
        }
        .btn-primary {
            --hue: 187;
        }
        .btn-ghost {
            color: hsl(var(--hue), 100%, 41%);
            background-color: transparent;
            border-color: hsl(var(--hue), 100%, 41%);
        }
        .btn-ghost:hover {
            color: white;
        }
        .btn-shine {
            color: white;
        }
        .btn-shine::before {
            position: absolute;
            content: "";
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(120deg, transparent, hsla(var(--hue), 100%, 41%, 0.5), transparent);
            transform: translateX(-100%);
            transition: 0.6s;
        }
        .btn-shine:hover {
            background: transparent;
            box-shadow: 0 0 20px 10px hsla(var(--hue), 100%, 41%, 0.5);
        }
        .btn-shine:hover::before {
            transform: translateX(100%);
        }
    </style>
    <script type="text/javascript">
        function RedirectToAdmin() {
            window.location.href = "admin.html";
        }
        function RedirectToSearch() {
            window.location.href = "search.html";
        }
    </script>
</head>
<body>
    <header class="header shadow">
        <div class="header-content d-flex justify-content-center p-2">
            <img src="logo.jpg" alt="Park-to-Go Logo" id="header-logo">
            <h1 id="header-msg" class="ml-5 align-self-center">Park-to-Go</h1>
        </div>
    </header>
    <div class="result-container">
        <?php echo $message; ?>
        <div class="button-container">
            <button class="btn btn-primary btn-ghost btn-shine" onclick="RedirectToAdmin()">OK</button>
            <button class="btn btn-primary btn-ghost btn-shine" onclick="RedirectToSearch()">Back to Search</button>
        </div>
    </div>
</body>
</html>
