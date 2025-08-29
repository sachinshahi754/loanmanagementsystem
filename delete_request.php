<?php
include ("connection.php");

$loan_id = $_GET['id'];

$delete_query = "DELETE FROM loan WHERE loan_id='$loan_id'";
$delete_data = mysqli_query($conn, $delete_query);
if ($delete_data) {
    echo "<script>alert('Request Reject')</script>";
    header("location:admin_dashboard.php");
} else {
    echo "failed to delete" . mysqli_error($conn);
}
?>