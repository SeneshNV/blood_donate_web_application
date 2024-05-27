<?php
// Start the session
session_start();

// Include the database connection file
include('admin_components/connection.php');

// Fetch the username based on the user_id stored in the session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT username FROM admin WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username);
    $stmt->fetch();
    $stmt->close();
} else {
    // Handle if user is not logged in
    header("Location: login.php");
    exit();
}

// Handle form submission to update request details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_request'])) {
    $request_id = $_POST['request_id'];
    $donate_date = $_POST['donate_date'];
    $donate_time = $_POST['donate_time'];
    $donate_location = $_POST['donate_location'];
    
    $update_query = "UPDATE blood_request SET donate_date = ?, donate_time = ?, donate_location = ?, request_status = 'Confirm' WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sssi", $donate_date, $donate_time, $donate_location, $request_id);
    if ($stmt->execute()) {
        // Update successful
    } else {
        // Handle error
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donor's Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Fjalla+One&display=swap" rel="stylesheet">
    <link href="styles/users_styles.css" rel="stylesheet">
    <style>
        .col-md-3 {
            min-width: 280px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <?php include('admin_components/donor_header.php'); ?>
    </div>

    <div class="login_space"></div>

    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <button class="d-md-none custom-btn mx-auto btn-100" type="button" data-bs-toggle="collapse" data-bs-target="#leftNav" aria-expanded="false" aria-controls="leftNav">
                    ☰ Menu
                </button>
                <div class="collapse d-md-block" id="leftNav">
                    <?php include('admin_components/left_nav.php'); ?>
                </div>
            </div>

            <div class="col">
            <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item active" aria-current="page">Matched Blood Transactions</li>
                    </ol>
                </nav>

                <div class="topic_dashboard">
                    <p>Matched <b>Blood Transactions</b></p>
                </div>

                <div class = "table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Recipient ID</th>
                            <th>Donor ID</th>
                            <th>Recipient Name</th>
                            <th>Recipient Age</th>
                            <th>Recipient Tel. No</th>
                            <th>Reason for Request</th>
                            <th>Request Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch blood donation requests from the database
                        $query = "SELECT * FROM blood_request where request_status = 'pending'";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['recipient_id'] . "</td>";
                            echo "<td>" . $row['donor_id'] . "</td>";
                            echo "<td>" . $row['blood_recipient_name'] . "</td>";
                            echo "<td>" . $row['recipient_age'] . "</td>";
                            echo "<td>" . $row['recipient_tel_no'] . "</td>";
                            echo "<td>" . $row['reason_for_request'] . "</td>";
                            echo "<td>" . $row['request_status'] . "</td>";
                            echo "<td><button class='nav_btn' onclick='openPopupForm(" . $row['id'] . ")'>Set Date Time</button></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Popup Form (Hidden Initially) -->
    <div class="modal fade" id="requestModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="requestModalLabel">Blood Request Form<span id="modalRowId"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="requestForm" method="post" action="">
                        <input type="hidden" id="request_id" name="request_id">
                        <div class="input-group mb-3">
                            <label for="donate_date" class="form-label">Donation Date:</label>
                            <input type="date" id="donate_date" name="donate_date" required class="form-input">
                        </div>
                        <div class="input-group mb-3">
                            <label for="donate_time" class="form-label">Donation Time:</label>
                            <input type="time" id="donate_time" name="donate_time" required class="form-input">
                        </div>
                        <div class="input-group mb-3">
                            <label for="donate_location" class="form-label">Donation Location:</label>
                            <input type="text" id="donate_location" name="donate_location" required class="form-input">
                        </div>
                        <button type="submit" name="update_request" class="custom-btn">Submit Request</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer" style="margin-top: 40px;">
        <?php include('admin_components/footer.php'); ?>
    </footer>

    <div>
        <?php include('admin_components/message_box.php'); ?>
    </div>

    <script>
        // Set the active button on page load
        window.onload = function() {
            setActiveButton('matchingblooddBtn');
        };

        function openPopupForm(rowId) {
            // Populate form fields with the selected row data
            document.getElementById('request_id').value = rowId;
            var requestModal = new bootstrap.Modal(document.getElementById('requestModal'));
            requestModal.show();
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

</body>

</html>
