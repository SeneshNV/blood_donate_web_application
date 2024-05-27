<?php
// Start the session
session_start();

// Include the database connection file
include('components/connection.php');
include('need_blood_request.php'); // Include the validation and form submission logic

// Fetch the username based on the user_id stored in the session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT username FROM user WHERE id = ?");
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

// Retrieve and clear the message from the session
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
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
    <link href="../styles/users_styles.css" rel="stylesheet">
    <style>
        .col-md-3 {
            min-width: 280px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <?php include('components/donor_header.php'); ?>
    </div>

    <div class="login_space"></div>

    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <button class="d-md-none custom-btn mx-auto btn-100" type="button" data-bs-toggle="collapse" data-bs-target="#leftNav" aria-expanded="false" aria-controls="leftNav">
                    ☰ Menu
                </button>

                <div class="collapse d-md-block" id="leftNav">
                    <?php include('components/left_nav.php'); ?>
                </div>
            </div>
            <div class="col">

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Need Blood</li>
                    </ol>
                </nav>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <p class="topic_dashboard">Request <b>Blood</b></p>
                    <button type="button" class="custom-btn" onclick="openPopupForm('Any')">Quick Request Blood</button>
                </div>

                <div class="container">
                    <form method="post" action="">
                        <div class="input-group mb-3">
                            <select class="form-select" id="bloodTypeSelect" name="blood_type">
                                <option value="">Select Blood Type</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                            <button class="custom-btn" type="submit" id="searchButton">Search</button>
                        </div>
                    </form>

                    <div id="searchResults" class="table-responsive">
                        <?php
                        // Initialize SQL query
                        $sql = "SELECT u.id as row_id, u.username, ui.blood_type, ui.age, ds.status 
                            FROM user u
                            JOIN user_info ui ON u.id = ui.user_id
                            JOIN donation_status ds ON u.id = ds.donor_id
                            WHERE ds.status = 'like_to_donate'
                            AND u.id != ?";

                        // Check if blood_type parameter is set (search query)
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['blood_type'])) {
                            $search_blood_type = $_POST['blood_type'];
                            if (!empty($search_blood_type)) {
                                // Add blood type condition to SQL query
                                $sql .= " AND ui.blood_type = ?";
                            }
                        }

                        // Prepare and execute SQL query
                        $stmt = $conn->prepare($sql);
                        if (!empty($search_blood_type)) {
                            $stmt->bind_param("is", $user_id, $search_blood_type);
                        } else {
                            $stmt->bind_param("i", $user_id);
                        }
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Prepare HTML for search results
                        $search_results_html = '';
                        if ($result->num_rows > 0) {
                            $search_results_html .= "<table class='table' style='text-align: center;'>";
                            $search_results_html .= "<thead><tr><th>ID</th><th>Username</th><th>Blood Type</th><th>Age</th><th>Request</th></thead>";
                            $search_results_html .= "<tbody>";

                            while ($row = $result->fetch_assoc()) {
                                $search_results_html .= "<tr>";
                                $search_results_html .= "<td>" . $row['row_id'] . "</td>";
                                $search_results_html .= "<td>" . $row['username'] . "</td>";
                                $search_results_html .= "<td>" . $row['blood_type'] . "</td>";
                                $search_results_html .= "<td>" . $row['age'] . "</td>";
                                $search_results_html .= "<td><button class='nav_btn' onclick='openPopupForm(" . $row['row_id'] . ")'>Request</button></td>";
                                $search_results_html .= "</tr>";
                            }

                            $search_results_html .= "</tbody>";
                            $search_results_html .= "</table>";
                        } else {
                            $search_results_html .= "No records found";
                        }

                        $stmt->close();

                        // Return search results HTML
                        echo $search_results_html;
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <footer class="footer" style="margin-top: 40px;">
        <?php include('components/footer.php'); ?>
    </footer>

    <div>
        <?php include('components/message_box.php'); ?>
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
                        <div class="input-group mb-3">
                            <label for="donor_id" class="form-label">Donor ID: </label>
                            <input type="text" id="donor_id" name="donor_id" required class="form-input" readonly>
                        </div>
                        <div class="input-group mb-3">
                            <label for="recipient_name" class="form-label">Recipient Name:</label>
                            <input type="text" id="recipient_name" name="recipient_name" required class="form-input">
                        </div>
                        <div class="input-group mb-3">
                            <label for="recipient_age" class="form-label">Recipient Age:</label>
                            <input type="number" id="recipient_age" name="recipient_age" required class="form-input">
                        </div>
                        <div class="input-group mb-3">
                            <label for="recipient_tel_no" class="form-label">Recipient Telephone Number:</label>
                            <input type="text" id="recipient_tel_no" name="recipient_tel_no" required class="form-input">
                        </div>
                        <div class="input-group mb-3">
                            <label for="reason_for_request" class="form-label">Reason for Request:</label>
                            <textarea id="reason_for_request" name="reason_for_request" required class="form-input"></textarea>
                        </div>
                        <button type="submit" class="custom-btn">Submit Request</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openPopupForm(rowId) {
            document.getElementById('donor_id').value = rowId;
            var requestModal = new bootstrap.Modal(document.getElementById('requestModal'));
            requestModal.show();
        }
    </script>


    <script>
        // Set the active button on page load
        window.onload = function() {
            setActiveButton('needBloodBtn');
        };
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

</body>

</html>