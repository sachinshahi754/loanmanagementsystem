<?php
include ("connection.php");

$loan_id = $_GET['id'];

$update_query = "UPDATE loan SET status='approved' WHERE loan_id='$loan_id'";
$update_data = mysqli_query($conn, $update_query);
if ($update_data) {
    echo "<script>alert('Request Approved')</script>";
    header("location:admin_dashboard.php");
} else {
    echo "failed";
}

?>