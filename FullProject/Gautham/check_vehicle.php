<?php
$servername="localhost";
$username="root";
$password="harshini26";
$dbname="parkinglot";

$conn=new mysqli($servername,$username,$password,$dbname);
if($conn->connect_error)
    die("Connection failed: ".$conn->connect_error);

if(isset($_POST['vehicle_number'])){
    $vehicle_number=$_POST['vehicle_number'];
 
    if(!preg_match('/^[A-Z]{2} \d{2} [A-Z]{2} \d{4}$/',$vehicle_number)){
        echo "invalid_format";
        exit;
    }

    $stmt = $conn->prepare("SELECT is_parked FROM CustomerRecords WHERE vehicle_number = ?");
    $stmt->bind_param("s", $vehicle_number);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($is_parked);

    if($stmt->num_rows>0){
        $stmt->fetch();
        if($is_parked)
            echo "exists_parked";
        else
            echo "exists_not_parked";
    }
    else
        echo "not_exists";
    $stmt->close();
}
$conn->close();
?>