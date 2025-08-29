<?php
include ("connection.php");
require ("loan_apply.php");
require ("payment.php");
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM user WHERE user_id='$user_id'";
    $data = mysqli_query($conn, $query);
    if (mysqli_num_rows($data) == 1) {
        $user_data = mysqli_fetch_assoc($data);
        //echo $user_data['user_name'];
    } else {
        header("loction: login.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User dashboard</title>
    <link rel="stylesheet" href="css/users.css" />
    <!-- Link to Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <!-- Import Google font - Poppins  -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" />

    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
    <style>
        .status-approved {
            color: green;
        }

        .status-pending {
            color: red;
        }
    </style>
</head>

<body>
    <div class="body_container">
        <div class="sidebar">
            <div class="logo_img">
                <img src="image/loan.png">
                <p>GETLOAN</p>
            </div>

            <div class="menu_container">
                <div class="menu active" id="dashboard" onclick="dashboardShow()">
                    <i class="fa-solid fa-house"></i>
                    <p>Dashboard</p>
                </div>
                <div class="menu" id="myloan" onclick="loanDetails()">
                    <i class="fa-solid fa-money-check"></i>
                    <p>My Loans</p>
                </div>
                <a href="#">
                    <div class="menu" id="newloan" onclick="newLoan()">
                        <i class="fa-solid fa-landmark"></i>
                        <p>New Loan</p>
                    </div>
                </a>

                <a href="#">
                    <div class="menu" id="payment" onclick="paymentShow()">
                        <i class="fa-solid fa-cash-register"></i>
                        <p>Payment</p>
                    </div>
                </a>
                <a href="#">
                    <div class="menu" id="payment_details" onclick="detailsShow()">
                        <i class="fa-solid fa-cash-register"></i>
                        <p>Payment Details</p>
                    </div>
                </a>
            </div>
            <div class="line"></div>

            <div class="logout_btn">
                <a href="logout.php">
                    <div class="menu">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <p>Logout</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- main section -->
        <div class="main_section">

            <div class="header">
                <div class="search_btn">
                    <input type="text" placeholder="Search here">
                    <button>search</button>
                </div>
                <div class="profile_btn">
                    <div class="letter">
                        <?php echo substr($user_data['user_name'], 0, 1); ?>
                    </div>
                    <p><?php echo $user_data['user_name'] ?></p>
                </div>
            </div>

            <!-- dashboard -->
            <div class="dashboard">
                <?php
                //total user
                $select_user = "SELECT * FROM user";
                $user_data = mysqli_query($conn, $select_user);
                $total_user = mysqli_num_rows($user_data);
                //total amount loan with approved
                $select_loan = "SELECT * FROM loan WHERE status='approved'";
                $loan_data = mysqli_query($conn, $select_loan);
                $total_loan = mysqli_num_rows($loan_data);
                //total loan with pending loan
                $select_loan = "SELECT * FROM loan WHERE status='pending'";
                $loan_data = mysqli_query($conn, $select_loan);
                $total_pending = mysqli_num_rows($loan_data);

                //total sum of loan loan amount
                $select_loan_sum = "SELECT SUM(loan_amount) AS total_loan_amount, SUM(amount) AS total_loan_payed
                 FROM loan as l
                 LEFT JOIN payment as p ON l.loan_id=p.loan_id 
                 WHERE l.user_id='$user_id' AND l.status='approved'";
                $loan_sum_result = mysqli_query($conn, $select_loan_sum);

                $loan_sum_row = mysqli_fetch_assoc($loan_sum_result);
                $total_loan_amount = $loan_sum_row['total_loan_amount'];
                $total_loan_payed = $loan_sum_row['total_loan_payed'];
                ?>
                <div class="card_container">
                    <div class="card" style="background-color:rgb(244, 178, 241)">
                        <div>
                            <p>TOTAL USERS</p>
                            <span><?php echo $total_user ?></span>
                        </div>
                    </div>
                    <div class="card" style="background-color:rgb(152, 235, 228)">
                        <div>
                            <p>TOTAL LOANS</p>
                            <span><?php echo $total_loan ?></span>
                            <div class="pending">
                                <p>Pending</p>
                                <span><?php echo $total_pending ?></span>
                            </div>

                        </div>
                    </div>
                    <div class="card" style="background-color:rgb(227, 235, 152)">
                        <div>
                            <p>TOTAL LOAN BORROWED</p>
                            <span>Rs.<?php echo $total_loan_amount ?></span>

                            <div class="recived_pending">
                                <p>Payed
                                    <span>Rs.<?php echo $total_loan_payed ?></span>
                                </p>
                                <p>Remaining
                                    <span>Rs.<?php echo $total_loan_amount - $total_loan_payed ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MY loan -->
            <div class="myloan hide">
                <h2>Loan Deatails</h2>
                <div class="details">
                    <table>
                        <thead>
                            <th>Loan Id</th>
                            <th>Loan Amount</th>
                            <th>Loan Type</th>
                            <th>Loan Plan</th>
                            <th>Remaining Due</th>
                            <th>Loan Status</th>
                        </thead>
                        <tbody>
                            <?php
                            error_reporting(E_ALL);
                            //retriving data from loan and payment table
                            
                            $loan_query = "SELECT l.loan_id, l.loan_amount, l.remaining_loan, l.loan_type, l.loan_plan, l.status
                           FROM loan as l
                           WHERE l.user_id='$user_id' ORDER BY loan_id";
                            $loan_data = mysqli_query($conn, $loan_query);
                            if ($loan_data) {
                                if (mysqli_num_rows($loan_data) > 0) {
                                    while ($row_loan = mysqli_fetch_assoc($loan_data)) {

                                        ?>
                                        <tr>
                                            <td>
                                                <div class="loan_data"><?php echo $row_loan['loan_id'] ?></div>
                                            </td>
                                            <td>
                                                <div class="loan_data"><?php echo $row_loan['loan_amount'] ?></div>
                                            </td>
                                            <td>
                                                <div class="loan_data"><?php echo $row_loan['loan_type'] ?></div>
                                            </td>
                                            <td>
                                                <div class="loan_data"><?php echo $row_loan['loan_plan'] ?></div>
                                            </td>
                                            <td>
                                                <div class="loan_data"><?php
                                                if ($row_loan['remaining_loan'] == 0) {
                                                    echo $row_loan['loan_amount'];
                                                } else {
                                                    echo $row_loan['remaining_loan'];
                                                }
                                                ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="loan_data" style="
                                                  <?php
                                                  if ($row_loan['status'] == 'approved') {
                                                      echo 'color: green;';
                                                  } elseif ($row_loan['status'] == 'pending') {
                                                      echo 'color: red;';
                                                  }
                                                  ?>">
                                                    <?php echo $row_loan['status']; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!--New loan-->
            <div class="loan_container hide">
                <h2>Loan Application Form</h2>

                <div class="loan_form">
                    <form action="" method="post">
                        <?php

                        $search_query = "SELECT * FROM user WHERE user_id='$user_id'";
                        $search_data = mysqli_query($conn, $search_query);
                        if (mysqli_num_rows($search_data) == 1) {
                            $row_search = mysqli_fetch_assoc($search_data);
                            //echo $row_search['user_name'];
                            ?>
                            <div class="fields">
                                <div class="input_field">
                                    <input type="text" value="<?php echo $row_search['user_name'] ?>"
                                        placeholder="enter your name">
                                </div>
                                <div class="input_field">
                                    <input type="text" value="<?php echo $row_search['email'] ?>"
                                        placeholder="enter your name">
                                </div>
                                <div class="input_field">
                                    <input type="text" value="<?php echo $row_search['gender'] ?>"
                                        placeholder="enter your name">
                                </div>
                                <div class="input_field">
                                    <input type="text" value="<?php echo $row_search['profession'] ?>"
                                        placeholder="enter your name">
                                </div>
                                <div class="input_field">
                                    <input type="text" value="<?php echo $row_search['pan_no'] ?>"
                                        placeholder="enter your name">
                                </div>
                                <div class="input_field">
                                    <input type="text" value="<?php echo $row_search['address'] ?>"
                                        placeholder="enter your name">
                                </div>
                                <div class="input_field">
                                    <div>
                                        <label>Amount:</label>
                                    </div>
                                    <input type="text" name="amount" placeholder="enter your loan amount" required>
                                </div>
                                <div class="input_field">
                                    <select name="loan_plan" required>
                                        <option selected disabled>select your loan plan</option>
                                        <option>36-Months 8% Interest</option>
                                        <option>24-Months 7% Interest</option>
                                        <option>12-Months 6% Interest</option>
                                        <option>6-Months 5% Interest</option>
                                    </select>

                                </div>
                                <div class="input_field">
                                    <select name="loan_type" required>
                                        <option selected disabled>select your loan type</option>
                                        <option>Business Loan</option>
                                        <option>Education Loan</option>
                                        <option>Small Business Loan</option>
                                        <option>Personal Loan</option>
                                    </select>
                                </div>
                                <input type="hidden" name="user_id" value="<?php echo $row_search['user_id'] ?>">
                            </div>
                            <div class="apply_btn">
                                <button type="submit" name="apply">Apply</button>
                            </div>
                            <?php
                        } else {
                            echo "<script>alert('User Id not Found')</script>";
                        }
                        ?>

                    </form>
                </div>
            </div>

            <!-- payment -->
            <div class="payment_container hide">

                <h2>Loan Payment</h2>
                <div class="loan_search">
                    <form action="" method="post" autocomplete="off">
                        <input type="text" name="loan_id" placeholder="enter your loan id" required>
                        <button type="submit" name="loan_search" style="margin-left:10px;">Search</button>
                    </form>
                </div>
                <div class="payment_form">
                    <form action="" method="post">
                        <?php
                        $bill_no= rand(0000, 9999);
                        if (isset($_POST['loan_search'])) {
                            $loan_id = $_POST['loan_id'];

                            $select = "SELECT * FROM loan WHERE loan_id='$loan_id' AND status='approved'";
                            $select_data = mysqli_query($conn, $select);
                            if (mysqli_num_rows($select_data) == 1) {
                                $result_loan = mysqli_fetch_assoc($select_data);
                                $loan_plan = $result_loan['loan_plan'];
                                $loan_amount = $result_loan['loan_amount'];
                                $remaining = $result_loan['remaining_loan'];
                                ?>
                                <div class="fields">
                                    <div class="input_field">
                                        <label>Bill No.:</label>:</label>
                                        <input type="text" name="bill_no" value="<?php echo $bill_no?>" readonly>
                                    </div>
                                    <div class="input_field">
                                        <label>Loan Type:</label>:</label>
                                        <input type="text" value="<?php echo $result_loan['loan_type'] ?>" readonly>
                                    </div>
                                    <div class="input_field">
                                        <label>Loan Plan:</label>:</label>
                                        <input type="text" name="loan_plan" value="<?php echo $result_loan['loan_plan'] ?>"
                                            readonly>
                                    </div>
                                    <div class="input_field">
                                        <label>Loan Amount:</label>:</label>
                                        <input type="text" name="loan_amount" value="<?php echo $result_loan['loan_amount'] ?>"
                                            readonly>
                                    </div>
                                    <div class="input_field">
                                        <label>Remaining Loan Amount:</label>:</label>
                                        <input type="text" placeholder="<?php
                                        if ($remaining == 0) {
                                            echo $loan_amount;
                                        } else {
                                            if ($loan_plan == '36-Months 8% Interest') {
                                                $monthly_instalment = ($loan_amount) / 36;
                                                echo $loan_amount - round($monthly_instalment, 0);
                                            } elseif ($loan_plan == '24-Months 7% Interest') {
                                                $monthly_instalment = ($loan_amount) / 24;
                                                echo $loan_amount - round($monthly_instalment, 0);
                                            } elseif ($loan_plan = '12-Months 6% Interest') {
                                                $monthly_instalment = ($loan_amount) / 12;
                                                echo $loan_amount - round($monthly_instalment, 0);
                                            } elseif ($loan_plan == '6-Months 5% Interest') {
                                                $monthly_instalment = ($loan_amount) / 6;
                                                echo $loan_amount - round($monthly_instalment, 0);
                                            }
                                        }
                                        ?>" readonly>
                                    </div>
                                    <div class="input_field">
                                        <label>Date:</label>:</label>
                                        <input type="date" id="datepicker" name="payment_date" readonly>
                                    </div>
                                    <div class="input_field">
                                        <label>Payment Amount:<span
                                                style='color:red; font-style: italic; font-size:15px;margin-left:10px;'>Monthly
                                                Installment</span></label>
                                        <input type="text" name="amount" value="<?php

                                        if ($loan_plan == '36-Months 8% Interest') {
                                            $monthly_instalment = ($loan_amount) / 36;
                                            echo round($monthly_instalment, 0);
                                        } elseif ($loan_plan == '24-Months 7% Interest') {
                                            $monthly_instalment = ($loan_amount) / 24;
                                            echo round($monthly_instalment, 0);
                                        } elseif ($loan_plan = '12-Months 6% Interest') {
                                            $monthly_instalment = ($loan_amount) / 12;
                                            echo round($monthly_instalment, 0);
                                        } elseif ($loan_plan == '6-Months 5% Interest') {
                                            $monthly_instalment = ($loan_amount) / 6;
                                            echo round($monthly_instalment, 0);
                                        } ?>" readonly>
                                    </div>
                                    <div class="payment_btn">
                                        <button type="submit" name="payment">Payment</button>
                                    </div>
                                    <input type="hidden" name="loan_id" value="<?php echo $result_loan['loan_id'] ?>">
                                    <input type="hidden" name="user_id" value="<?php echo $user_id ?>">
                                </div>
                                <?php
                            } else {
                                echo "<script>alert('no data found')</script>";
                            }
                        }
                        ?>
                    </form>
                </div>
            </div>

            <!-- payment details -->
            <div class="payment_details hide">
                <div class="payment_container">
                    <div class="user_container ">
                        <h2>Payment Details</h2>
                        <div class="table_wrapper">
                            <?php
                            // Assuming you have already established a database connection
                            
                            // Execute the SQL query
                            $user_query = "SELECT u.user_name, u.profession, u.pan_no, l.loan_id, l.loan_type, p.amount, p.date, p.bill_no
                    FROM user AS u
                    INNER JOIN payment AS p ON u.user_id = p.user_id 
                    INNER JOIN loan AS l ON p.loan_id = l.loan_id";

                            $user_data = mysqli_query($conn, $user_query);

                            // Check if query execution was successful
                            if ($user_data) {
                                ?>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Bill No.</th>
                                            <th>Loan ID</th>
                                            <th>Loan Type</th>
                                            <th>Payed Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (mysqli_num_rows($user_data) > 0) {
                                            while ($result_user = mysqli_fetch_assoc($user_data)) {
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div class="data"><?php echo $result_user['bill_no']; ?></div>
                                                    </td>
                                                    <td>
                                                        <div class="data"><?php echo $result_user['loan_id']; ?></div>
                                                    </td>
                                                    <td>
                                                        <div class="data"><?php echo $result_user['loan_type']; ?></div>
                                                    </td>
                                                    <td>
                                                        <div class="data"><?php echo $result_user['amount']; ?></div>
                                                    </td>
                                                    <td>
                                                        <div class="data"><?php echo $result_user['date']; ?></div>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='7'>No data found</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <?php
                            } else {
                                echo "Error executing the query: " . mysqli_error($conn);
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        // current date
        document.addEventListener('DOMContentLoaded', function () {
            var today = new Date().toISOString().slice(0, 10);

            document.getElementById('datepicker').value = today;
        });

        function loanDetails() {
            document.querySelector('#myloan').classList.add("active");
            document.querySelector('#newloan').classList.remove("active");
            document.querySelector('#payment').classList.remove("active");
            document.querySelector('#dashboard').classList.remove("active");
            document.querySelector('.myloan').classList.remove("hide");
            document.querySelector('.loan_container').classList.add("hide");
            document.querySelector('.payment_container').classList.add("hide");
            document.querySelector('.dashboard').classList.add("hide");
            document.querySelector('#payment_details').classList.remove("active");
            document.querySelector('.payment_details').classList.add("hide");
        }
        function newLoan() {
            document.querySelector('#myloan').classList.remove("active");
            document.querySelector('#dashboard').classList.remove("active");
            document.querySelector('#payment').classList.remove("active");
            document.querySelector('#newloan').classList.add("active");
            document.querySelector('.myloan').classList.add("hide");
            document.querySelector('.loan_container').classList.remove("hide");
            document.querySelector('.payment_container').classList.add("hide");
            document.querySelector('.dashboard').classList.add("hide");
            document.querySelector('#payment_details').classList.remove("active");
            document.querySelector('.payment_details').classList.add("hide");
        }
        function dashboardShow() {
            document.querySelector('#dashboard').classList.add("active");
            document.querySelector('#myloan').classList.remove("active");
            document.querySelector('#payment').classList.remove("active");
            document.querySelector('#newloan').classList.remove("active");
            document.querySelector('.myloan').classList.add("hide");
            document.querySelector('.loan_container').classList.add("hide");
            document.querySelector('.payment_container').classList.add("hide");
            document.querySelector('.dashboard').classList.remove("hide");
            document.querySelector('#payment_details').classList.remove("active");
            document.querySelector('.payment_details').classList.add("hide");

        }
        function paymentShow() {
            document.querySelector('#dashboard').classList.remove("active");
            document.querySelector('#myloan').classList.remove("active");
            document.querySelector('#payment').classList.add("active");
            document.querySelector('#payment_details').classList.remove("active");
            document.querySelector('#newloan').classList.remove("active");
            document.querySelector('.myloan').classList.add("hide");
            document.querySelector('.loan_container').classList.add("hide");
            document.querySelector('.payment_container').classList.remove("hide");
            document.querySelector('.dashboard').classList.add("hide");
            document.querySelector('.payment_details').classList.add("hide");

        }
        function detailsShow() {
            document.querySelector('#dashboard').classList.remove("active");
            document.querySelector('#myloan').classList.remove("active");
            document.querySelector('#payment_details').classList.add("active");
            document.querySelector('#payment').classList.remove("active");
            document.querySelector('#newloan').classList.remove("active");
            document.querySelector('.myloan').classList.add("hide");
            document.querySelector('.loan_container').classList.add("hide");
            document.querySelector('.payment_container').classList.add("hide");
            document.querySelector('.dashboard').classList.add("hide");
            document.querySelector('.payment_details').classList.remove("hide");

        }
    </script>
</body>

</html>