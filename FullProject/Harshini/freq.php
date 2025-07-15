<?php
$servername = "localhost";
$username = "root";
$password = "harshini26";
$dbname = "parkinglot";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT pr.vehicle_number, COUNT(*) AS frequency
        FROM ParkingRecords pr
        GROUP BY pr.vehicle_number
        ORDER BY frequency DESC
        LIMIT 1";

$result = $conn->query($sql);

$message = "";

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $message .= "Vehicle Number: " . $row["vehicle_number"] . " - Total number of times parked: " . $row["frequency"];
    }
} else {
    $message = "0 results";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./CSS/bootstrap.min.css">
    <title>Most Frequent Vehicle</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: black;
            margin: 0;
            padding: 0;
            color: white;
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
        .message {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .message-container {
            margin: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            text-align: center;
            border-radius: 5px;
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
        function redirectToAdmin() {
            window.location.href = "admin.html";
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
    <div class="message-container" id="message-container">
        <p><?php echo $message; ?></p>
    </div>
    <div class="message">
        <button class="btn btn-primary btn-ghost btn-shine" onclick="redirectToAdmin()">OK</button>
        <button class="btn btn-primary btn-ghost btn-shine" onclick="redirectToAdmin()">BACK TO MAIN PAGE</button>
    </div>
</body>
</html>
