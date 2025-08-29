<?php
include ("connection.php");

if (isset($_POST['apply'])) {
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    $amount = isset($_POST['amount']) ? $_POST['amount'] : '';
    $loan_plan = isset($_POST['loan_plan']) ? $_POST['loan_plan'] : '';
    $loan_type = isset($_POST['loan_type']) ? $_POST['loan_type'] : '';

    if (!is_numeric($amount) || $amount <= 0) {
        echo "<script>alert('Please enter a valid loan amount')</script>";
        exit();
    }

    switch ($loan_plan) {
        case '36-Months 8% Interest':
            $interest = ($amount * 8) / 100;
            break;
        case '24-Months 7% Interest':
            $interest = ($amount * 7) / 100;
            break;
        case '12-Months 6% Interest':
            $interest = ($amount * 6) / 100;
            break;
        case '6-Months 5% Interest':
            $interest = ($amount * 5) / 100;
            break;
        default:
            $interest = 0;
            echo "<script>alert('Invalid loan plan selected')</script>";
            exit();
    }

    $total_loan = $amount + $interest;
  //  echo $total_loan;

    // Check if any of the fields are empty
    if (empty($user_id) || empty($amount) || empty($loan_plan) || empty($loan_type)) {
        echo "<script>alert('Fill all the fields properly')</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO loan (user_id, loan_amount, loan_plan, loan_type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdss", $user_id, $total_loan, $loan_plan, $loan_type);

        if ($stmt->execute()) {
            echo "<script>alert('Successfully Applied')</script>";
        } else {
            echo "Failed to apply: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>
