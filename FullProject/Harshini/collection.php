<?php
$servername = "localhost";
$username = "root";
$password = "harshini26";
$dbname = "parkinglot";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getDayWiseCollection($conn) {
    $sql = "SELECT DATE(time_in) as date, SUM(vehicle_amt) as total_amount
            FROM ParkingRecords
            GROUP BY DATE(time_in)
            ORDER BY DATE(time_in)";
    return $conn->query($sql);
}

function getWeekWiseCollection($conn) {
    $sql = "SELECT YEARWEEK(time_in, 1) as week, SUM(vehicle_amt) as total_amount
            FROM ParkingRecords
            GROUP BY YEARWEEK(time_in, 1)
            ORDER BY YEARWEEK(time_in, 1)";
    return $conn->query($sql);
}

function getMonthWiseCollection($conn) {
    $sql = "SELECT DATE_FORMAT(time_in, '%Y-%m') as month, SUM(vehicle_amt) as total_amount
            FROM ParkingRecords
            GROUP BY DATE_FORMAT(time_in, '%Y-%m')
            ORDER BY DATE_FORMAT(time_in, '%Y-%m')";
    return $conn->query($sql);
}

function getYearWiseCollection($conn) {
    $sql = "SELECT DATE_FORMAT(time_in, '%Y') as year, SUM(vehicle_amt) as total_amount
            FROM ParkingRecords
            GROUP BY DATE_FORMAT(time_in, '%Y')
            ORDER BY DATE_FORMAT(time_in, '%Y')";
    return $conn->query($sql);
}

function getCustomCollection($conn, $start_date, $end_date) {
    $sql = "SELECT DATE(time_in) as date, SUM(vehicle_amt) as total_amount
            FROM ParkingRecords
            WHERE DATE(time_in) BETWEEN '$start_date' AND '$end_date'
            GROUP BY DATE(time_in)
            ORDER BY DATE(time_in)";
    return $conn->query($sql);
}

function displayTable($result, $key) {
    if ($result->num_rows > 0) {
        echo "<table border='1'>
              <tr><th>Date</th><th>Total Amount</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . htmlspecialchars($row[$key]) . "</td><td>" . htmlspecialchars($row["total_amount"]) . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No results found.";
    }
}

$selected_option = isset($_POST['time_period']) ? $_POST['time_period'] : '';
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collection Reports</title>
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
</head>
<body>
    <header class="header shadow">
        <div class="header-content d-flex justify-content-center p-2">
            <img src="logo.jpg" alt="Park-to-Go Logo" id="header-logo">
            <h1 id="header-msg" class="ml-5 align-self-center">Park-to-Go</h1>
        </div>
    </header>
    <div class="result-container">
        <form method="post" action="" id="reportForm">
            <label for="time_period">Select Time Period:</label>
            <select id="time_period" name="time_period">
                <option value="day" <?php if ($selected_option == 'day') echo 'selected'; ?>>Day-wise</option>
                <option value="week" <?php if ($selected_option == 'week') echo 'selected'; ?>>Week-wise</option>
                <option value="month" <?php if ($selected_option == 'month') echo 'selected'; ?>>Month-wise</option>
                <option value="year" <?php if ($selected_option == 'year') echo 'selected'; ?>>Year-wise</option>
                <option value="custom" <?php if ($selected_option == 'custom') echo 'selected'; ?>>Custom Date Range</option>
            </select>
            <br><br>
            <div id="custom_dates" style="display: none;">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                <br><br>
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                <br><br>
            </div>
            <button type="submit" class="btn btn-primary btn-ghost btn-shine">Submit</button>
            <button type="button" class="btn btn-primary btn-ghost btn-shine" onclick="clearForm()">Clear</button>
        </form>
        
        <?php
        if ($selected_option == 'day') {
            echo "<h2>Day-Wise Collection</h2>";
            $dayWiseResult = getDayWiseCollection($conn);
            displayTable($dayWiseResult, 'date');
        } elseif ($selected_option == 'week') {
            echo "<h2>Week-Wise Collection</h2>";
            $weekWiseResult = getWeekWiseCollection($conn);
            displayTable($weekWiseResult, 'week');
        } elseif ($selected_option == 'month') {
            echo "<h2>Month-Wise Collection</h2>";
            $monthWiseResult = getMonthWiseCollection($conn);
            displayTable($monthWiseResult, 'month');
        } elseif ($selected_option == 'year') {
            echo "<h2>Year-Wise Collection</h2>";
            $yearWiseResult = getYearWiseCollection($conn);
            displayTable($yearWiseResult, 'year');
        } elseif ($selected_option == 'custom') {
            if ($start_date && $end_date) {
                echo "<h2>Custom Date Range Collection from $start_date to $end_date</h2>";
                $customResult = getCustomCollection($conn, $start_date, $end_date);
                displayTable($customResult, 'date');
            } else {
                echo "<p>Please select a valid date range.</p>";
            }
        }
        ?>
        <div class="button-container">
            <button class="btn btn-primary btn-ghost btn-shine" onclick="window.location.href='admin.html'">OK</button>
            <button class="btn btn-primary btn-ghost btn-shine" onclick="window.location.href='admin.html'">Back to Main Page</button>
        </div>
    </div>

    <script>
        document.getElementById('time_period').addEventListener('change', function () {
            var customDates = document.getElementById('custom_dates');
            if (this.value === 'custom') {
                customDates.style.display = 'block';
            } else {
                customDates.style.display = 'none';
            }
        });

        window.onload = function() {
            var selectedOption = document.getElementById('time_period').value;
            if (selectedOption === 'custom') {
                document.getElementById('custom_dates').style.display = 'block';
            }
        };

        function clearForm() {
            document.getElementById('reportForm').reset();
            document.getElementById('custom_dates').style.display = 'none';
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
