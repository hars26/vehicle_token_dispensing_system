<?php
$servername = "localhost";
$username = "root";
$password = "harshini26";
$dbname = "parkinglot";
$total_slots_Valet = 20;
$total_slots_Carriage = 50;

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$ValetFree=0;
$CarriageFree=0;

$sql = "SELECT COUNT(*) AS count FROM Valet";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $count = $row["count"];
    $ValetFree = $total_slots_Valet - $count;
}

$sql = "SELECT COUNT(*) AS count FROM Carriage";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $count = $row["count"];
    $CarriageFree= $total_slots_Carriage - $count;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Available Parking Slots</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./CSS/bootstrap.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            flex-direction: row;
            width: 100%;
            margin-top: 20px;
        }
        .message-container {
            flex: 1;
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
    <header class="header shadow">
        <div class="header-content d-flex justify-content-center p-2">
            <img src="logo.jpg" alt="Park-to-Go Logo" id="header-logo">
            <h1 id="header-msg" class="ml-5 align-self-center">Park-to-Go</h1>
        </div>
    </header>
    <script type="text/javascript">
        function redirectToAdmin() {
            window.location.href = "admin.html";
        }
    </script>
</head>
<body>
<div class="message-container" id="message-container">
    <div class="message-container" id="message-container">
        Total available slots in Valet: <?php echo $ValetFree?>
    </div>
    <div class="message-container" id="message-container">
        Total available slots in Carriage: <?php echo $CarriageFree?>
    </div>
</div>
    <div class="message">
        <button class="btn btn-primary btn-ghost btn-shine" onclick="redirectToAdmin()">OK</button><br><br>
        <button class="btn btn-primary btn-ghost btn-shine" onclick="redirectToAdmin()">BACK TO MAIN PAGE</button>
    </div>
</body>
</html>
