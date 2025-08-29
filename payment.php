<?php
include ("connection.php");

if (isset($_POST['payment'])) {
    $user_id = $_POST['user_id'];
    $loan_id = $_POST['loan_id'];
    $date = date("Y/M/d l", strtotime($_POST['payment_date']));
    $amount = $_POST['amount'];
    $loan_plan = $_POST['loan_plan'];
    $loan_amount = $_POST['loan_amount'];
    $bill_no = $_POST['bill_no'];

    //retrive the data from loan table
    $loan = "SELECT * FROM loan WHERE loan_id='$loan_id'";
    $data = mysqli_query($conn, $loan);
    $result = mysqli_fetch_assoc($data);
    $remaining = $result['remaining_loan'];

    if ($remaining == 0) {
        if ($loan_plan == '36-Months 8% Interest') {
            $monthly_instalment = ($loan_amount) / 36;
            $remaining_loan = (float) $loan_amount - round($monthly_instalment, 0);
            $remain = round($remaining_loan);
        } elseif ($loan_plan == '24-Months 7% Interest') {
            $monthly_instalment = ($loan_amount) / 24;
            $remaining_loan = (float) $loan_amount - round($monthly_instalment, 0);
            $remain = round($remaining_loan);

        } elseif ($loan_plan = '12-Months 6% Interest') {
            $monthly_instalment = ($loan_amount) / 12;
            $remaining_loan = (float) $loan_amount - round($monthly_instalment, 0);
            $remain = round($remaining_loan);

        } elseif ($loan_plan == '6-Months 5% Interest') {
            $monthly_instalment = ($loan_amount) / 6;
            $remaining_loan = (float) $loan_amount - round($monthly_instalment, 0);
            $remain = round($remaining_loan);

        }
    } else {
        if ($loan_plan == '36-Months 8% Interest') {
            $monthly_instalment = ($loan_amount) / 36;
            $remaining_loan = (float) $remaining - round($monthly_instalment, 0);
            $remain = round($remaining_loan);
        } elseif ($loan_plan == '24-Months 7% Interest') {
            $monthly_instalment = ($loan_amount) / 24;
            $remaining_loan = (float) $remaining - round($monthly_instalment, 0);
            $remain = round($remaining_loan);

        } elseif ($loan_plan = '12-Months 6% Interest') {
            $monthly_instalment = ($loan_amount) / 12;
            $remaining_loan = (float) $remaining - round($monthly_instalment, 0);
            $remain = round($remaining_loan);

        } elseif ($loan_plan == '6-Months 5% Interest') {
            $monthly_instalment = ($loan_amount) / 6;
            $remaining_loan = (float) $remaining - round($monthly_instalment, 0);
            $remain = round($remaining_loan);

        }
    }
    $update_loan = "UPDATE loan SET remaining_loan='$remain' WHERE loan_id='$loan_id'";
    $update_data = mysqli_query($conn, $update_loan);
    if ($update_data) {
        //  echo "successfully update";
    } else {
        echo "failed to update";
    }


    $payment_query = "INSERT INTO payment(user_id, loan_id, amount, date, bill_no) VALUES('$user_id', '$loan_id', '$amount', '$date', '$bill_no')";
    $payment_data = mysqli_query($conn, $payment_query);
    if ($payment_data) {
        echo "<script>alert('successfully payed')</script>";
    } else {
        echo "failed" . mysqli_error($conn);
    }


}
?>