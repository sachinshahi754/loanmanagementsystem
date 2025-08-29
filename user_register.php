<?php
require ('connection.php');

if (isset($_POST['register_btn'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $pan = $_POST['pan'];
    $gender = $_POST['gender'];
    $profession = $_POST['profession'];
    $password = $_POST['password'];

    if (strlen($password) < 5) {
        echo "<script>alert('password must be atleast 5 char long')</script>";
    } elseif (!preg_match('/^[a-zA-z\s]+$/', $name)) {
        echo "<script>alert('invalid name')</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email')</script>";
    } else {
        $select = "SELECT * FROM user WHERE email='$email'";
        $data = mysqli_query($conn, $select);
        if (mysqli_num_rows($data) == 1) {
            echo "<script>alert('user with this emial is already exist')</script>";
        } else {
            $insert_query = "INSERT INTO user(user_name, address, gender, email, password, pan_no, profession) VALUES('$name', '$address','$gender', '$email', '$password', '$pan', '$profession')";
            $insert_data = mysqli_query($conn, $insert_query);
            if ($insert_data) {
                echo "<script>alert('successfull')</script>";
            } else {
                echo "failed" . mysqli_error($conn);
            }
        }
    }

}
?>