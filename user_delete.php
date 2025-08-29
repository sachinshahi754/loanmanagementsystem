<?php
include ("connection.php");
$id = $_GET['id'];

$delete_user = "DELETE FROM user WHERE user_id='$id'";
$user_data = mysqli_query($conn, $delete_user);
if ($user_data) {
    echo "<script>alert('User delete successfully')</script>";
    header("location:admin_dashboard.php");
} else {
    echo "failed to delete";
}
?>