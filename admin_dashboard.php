<?php
include ("connection.php");

session_start();
if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
    $query = "SELECT * FROM admin WHERE admin_id='$admin_id'";
    $data = mysqli_query($conn, $query);
    if (mysqli_num_rows($data) == 1) {
        $admin_data = mysqli_fetch_assoc($data);
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
    <title>Admin dashboard</title>
    <link rel="stylesheet" href="css/admin2.css" />
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
                    <i class="fa-solid fa-gauge"></i>
                    <p>Dashboard</p>
                </div>
                <div class="menu" id="myloan" onclick="loanDetails()">
                    <i class="fa-solid fa-bell"></i>
                    <p>Loan Requests</p>
                </div>
                <a href="#">
                    <div class="menu" id="newloan" onclick="newLoan()">
                        <i class="fa-solid fa-user"></i>
                        <p>Users</p>
                    </div>
                </a>

                <a href="#">
                    <div class="menu" id="loan" onclick="loanShow()">
                        <i class="fa-solid fa-landmark"></i>
                        <p>Loans</p>
                    </div>
                </a>
                <a href="#">
                    <div class="menu" id="payment" onclick="paymentShow()">
                        <i class="fa-solid fa-money-bills"></i>
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
                        <?php echo substr($admin_data['admin_name'], 0, 1); ?>
                    </div>
                    <p><?php echo $admin_data['admin_name'] ?></p>
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
                $select_loan_sum = "SELECT SUM(l.loan_amount) AS total_loan_amount, SUM(p.amount) AS total_loan_payed
                 FROM loan as l
                 LEFT JOIN payment as p ON l.loan_id=p.loan_id WHERE l.status='approved'";
                $loan_sum_result = mysqli_query($conn, $select_loan_sum);

                $loan_sum_row = mysqli_fetch_assoc($loan_sum_result);
                $total_loan_amount = $loan_sum_row['total_loan_amount'];
                $total_loan_payed = $loan_sum_row['total_loan_payed'];
                ?>
                <div class="card_container">
                    <div class="card" style="background-color:rgb(253, 213, 119)">
                        <div>
                            <p>TOTAL USERS</p>
                            <span><?php echo $total_user ?></span>
                        </div>
                    </div>
                    <div class="card" style="background-color:rgb(152, 255, 134)">
                        <div>
                            <p>TOTAL LOANS</p>
                            <span><?php echo $total_loan ?></span>
                            <div class="pending">
                                <p>Pending</p>
                                <span><?php echo $total_pending?></span>
                            </div>

                        </div>
                    </div>
                    <div class="card" style="background-color:rgb(134, 219, 255)">
                        <div>
                            <p>AMOUNT DISBURSED</p>
                            <span>Rs.<?php echo $total_loan_amount ?></span>

                            <div class="recived_pending">
                                <p>Recieved
                                    <span>Rs.<?php echo $total_loan_payed ?></span>
                                </p>
                                <p>Pending
                                    <span>Rs.<?php echo $total_loan_amount - $total_loan_payed ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--LOan request section-->
            <div class="loan_request ">
                <h2>Loan Requests</h2>
                <div class="table_wrapper">
                    <table>
                        <thead>
                            <th>User Id</th>
                            <th>Loan Id</th>
                            <th>Loan Amount</th>
                            <th>Loan Plan</th>
                            <th>Loan Type</th>
                            <th>Status</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            <?php
                            include ('connection.php');
                            $request_query = "SELECT `user_id`,`loan_id`, `user_id`, `loan_amount`, `loan_plan`, `loan_type`, `status` FROM `loan` WHERE status='pending';";
                            $request_data = mysqli_query($conn, $request_query);

                            if (mysqli_num_rows($request_data) > 0) {
                                while ($result = mysqli_fetch_assoc($request_data)) {
                                    // echo $result['loan_id'];
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="data">
                                                <?php echo $result['user_id'] ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="data">
                                                <?php echo $result['loan_id'] ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="data">
                                                <?php echo $result['loan_amount'] ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="data">
                                                <?php echo $result['loan_plan'] ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="data">
                                                <?php echo $result['loan_type'] ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="data">
                                                <?php echo $result['status'] ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="action_data">

                                                <a href="approve.php?id=<?php echo $result['loan_id'] ?>"><button
                                                        style="background-color:rgb(89, 183, 110);" onclick="return confirmApprove()">Approve</button></a>

                                                <a href="delete_request.php?id=<?php echo $result['loan_id'] ?>"><button
                                                        onclick="return confirmDelete()">Reject</button></a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php

                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- user section -->
            <div class="user_container hide">
                <h2>User Details</h2>
                <div class="table_wrapper">
                    <table>
                        <thead>
                            <th>user Id</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Pan No.</th>
                            <th>Profession</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            <?php
                            $user_query = "SELECT * FROM user";
                            $user_data = mysqli_query($conn, $user_query);

                            if ($user_data) { // Check if query execution was successful
                                if (mysqli_num_rows($user_data) > 0) {
                                    while ($result_user = mysqli_fetch_assoc($user_data)) {
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="data">
                                                    <?php echo $result_user['user_id'] ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="data">
                                                    <?php echo $result_user['user_name'] ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="data">
                                                    <?php echo $result_user['address'] ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="data">
                                                    <?php echo $result_user['gender'] ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="data">
                                                    <?php echo $result_user['email'] ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="data">
                                                    <?php echo $result_user['pan_no'] ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="data">
                                                    <?php echo $result_user['profession'] ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="action_data">
                                                    <a href="user_delete.php?id=<?php echo $result_user['user_id'] ?>"><button
                                                            onclick="return confirmDeleteUser()">Delete</button></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                            } else {
                                echo "Error executing the query: " . mysqli_error($conn); // Output error message
                            }
                            ?>

                        </tbody>
                    </table>
                </div>
            </div>

            <!-- loan section -->
            <div class="loan_container hide">
                <div class="user_container">
                    <h2>Loan Details</h2>
                    <div class="table_wrapper">
                        <table>
                            <thead>
                                <th>User Name</th>
                                <th>User Pan No.</th>
                                <th>User Id</th>
                                <th>Loan Id</th>
                                <th>Loan Plan</th>
                                <th>Loan Type</th>
                                <th>Loan Amount</th>
                                <th>Remaining Due</th>
                            </thead>
                            <tbody>
                                <?php
                                $user_query = "SELECT u.user_name,u.user_id, u.pan_no, l.loan_id, l.loan_type, l.loan_plan, l.loan_amount, p.amount
                            FROM loan AS l
                            LEFT JOIN user AS u ON l.user_id = u.user_id 
                            LEFT JOIN payment AS p ON l.loan_id = p.loan_id
                            WHERE l.status='approved'";
                                $user_data = mysqli_query($conn, $user_query);

                                if ($user_data) { // Check if query execution was successful
                                    if (mysqli_num_rows($user_data) > 0) {
                                        while ($result_user = mysqli_fetch_assoc($user_data)) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="data">
                                                        <?php echo $result_user['user_name'] ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="data">
                                                        <?php echo $result_user['pan_no'] ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="data">
                                                        <?php echo $result_user['user_id'] ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="data">
                                                        <?php echo $result_user['loan_id'] ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="data">
                                                        <?php echo $result_user['loan_plan'] ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="data">
                                                        <?php echo $result_user['loan_type'] ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="data">
                                                        <?php echo $result_user['loan_amount'] ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="data">
                                                        <?php echo $result_user['loan_amount'] - $result_user['amount'] ?>
                                                    </div>
                                                </td>

                                            </tr>
                                            <?php
                                        }
                                    }
                                } else {
                                    echo "Error executing the query: " . mysqli_error($conn); // Output error message
                                }
                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <!-- payment details -->
            <div class="payment_container hide">
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
                                        <th>User Name</th>
                                        <th>Profession</th>
                                        <th>Pan No.</th>
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
                                                    <div class="data"><?php echo $result_user['user_name']; ?></div>
                                                </td>
                                                <td>
                                                    <div class="data"><?php echo $result_user['profession']; ?></div>
                                                </td>
                                                <td>
                                                    <div class="data"><?php echo $result_user['pan_no']; ?></div>
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
    <script>
        function confirmDelete() {
            return confirm('Are you sure. you want to reject the request.');
        }
        function confirmDeleteUser() {
            return confirm('Are you sure. you want to delete the user.');
        }
        function confirmApprove(){
            return confirm('Are you sure. you want to approve the request.');
        }

        //side bar 
        function loanDetails() {
            document.querySelector('#myloan').classList.add("active");
            document.querySelector('#newloan').classList.remove("active");
            document.querySelector('#payment').classList.remove("active");
            document.querySelector('#loan').classList.remove("active");
            document.querySelector('#dashboard').classList.remove("active");
            document.querySelector('.loan_request').classList.remove("hide");
            document.querySelector('.user_container').classList.add("hide");
            document.querySelector('.payment_container').classList.add("hide");
            // document.querySelector('.dashboard').classList.add("hide");
            document.querySelector('.loan_container').classList.add("hide");


        }
        function newLoan() {
            document.querySelector('#myloan').classList.remove("active");
            document.querySelector('#dashboard').classList.remove("active");
            document.querySelector('#payment').classList.remove("active");
            document.querySelector('#loan').classList.remove("active");
            document.querySelector('#newloan').classList.add("active");
            document.querySelector('.loan_request').classList.add("hide");
            document.querySelector('.user_container').classList.remove("hide");
            document.querySelector('.payment_container').classList.add("hide");
          //  document.querySelector('.dashboard').classList.add("hide");
            document.querySelector('.loan_container').classList.add("hide");



        }
        function dashboardShow() {
            document.querySelector('#dashboard').classList.add("active");
            document.querySelector('#myloan').classList.remove("active");
            document.querySelector('#loan').classList.remove("active");
            document.querySelector('#payment').classList.remove("active");
            document.querySelector('#newloan').classList.remove("active");
            //document.querySelector('.loan_request').classList.add("hide");
            document.querySelector('.user_container').classList.add("hide");
            document.querySelector('.payment_container').classList.add("hide");
            document.querySelector('.loan_container').classList.add("hide");
           // document.querySelector('.dashboard').classList.remove("hide");


        }

        function loanShow() {
            document.querySelector('#loan').classList.add("active");
            document.querySelector('#dashboard').classList.remove("active");
            document.querySelector('#myloan').classList.remove("active");
            document.querySelector('#payment').classList.remove("active");
            document.querySelector('#newloan').classList.remove("active");
            document.querySelector('.loan_container').classList.remove("hide");
            document.querySelector('.user_container').classList.add("hide");
            document.querySelector('.loan_request').classList.add("hide");
            document.querySelector('.payment_container').classList.add("hide");
           // document.querySelector('.dashboard').classList.add("hide");

        }
        function paymentShow() {
            document.querySelector('#dashboard').classList.remove("active");
            document.querySelector('#loan').classList.remove("active");
            document.querySelector('#myloan').classList.remove("active");
            document.querySelector('#payment').classList.add("active");
            document.querySelector('#newloan').classList.remove("active");
            document.querySelector('.loan_container').classList.add("hide");
            document.querySelector('.loan_request').classList.add("hide");
            document.querySelector('.user_container').classList.add("hide");
            document.querySelector('.payment_container').classList.remove("hide");
           // document.querySelector('.dashboard').classList.add("hide");
        }
    </script>
</body>

</html>