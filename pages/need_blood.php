<?php
// Start the session
session_start();

// Include the database connection file
include('components/connection.php');

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
                <button class="btn btn-primary d-md-none custom-btn" type="button" data-bs-toggle="collapse" data-bs-target="#leftNav" aria-expanded="false" aria-controls="leftNav">
                    ☰ Profile
                </button>
                <div class="collapse d-md-block" id="leftNav">
                    <?php include('components/left_nav.php'); ?>
                </div>
            </div>
            <div class="col">

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Blood Donor</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Need Blood</li>
                    </ol>
                </nav>

                <div class="topic_dashboard">
                    <p>Request <b>Blood</b></p>
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
                            <button class="btn btn-primary" type="submit" id="searchButton">Search</button>
                        </div>
                    </form>

                    <div id="searchResults">
                        <?php
                        // Initialize SQL query
                        $sql = "SELECT u.username, ui.blood_type, ui.age, ds.status 
                        FROM user u
                        JOIN user_info ui ON u.id = ui.user_id
                        JOIN donation_status ds ON u.id = ds.donor_id
                        WHERE ds.status = 'like_to_donate'
                        AND u.id != ?";

                        // Check if blood_type parameter is set (search query)
                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                            $search_results_html .= "<table class='table'>";
                            $search_results_html .= "<thead><tr><th>Username</th><th>Blood Type</th><th>Age</th><th>Request</th></tr></thead>";
                            $search_results_html .= "<tbody>";
                            while ($row = $result->fetch_assoc()) {
                                $search_results_html .= "<tr>";
                                $search_results_html .= "<td>" . $row['username'] . "</td>";
                                $search_results_html .= "<td>" . $row['blood_type'] . "</td>";
                                $search_results_html .= "<td>" . $row['age'] . "</td>";
                                // Add request button
                                $search_results_html .= "<td><button class='btn btn-primary'>Request</button></td>";
                                $search_results_html .= "</tr>";
                            }
                            $search_results_html .= "</tbody>";
                            $search_results_html .= "</table>";
                        } else {
                            $search_results_html .= "No records found";
                        }

                        $stmt->close();
                        $conn->close();

                        // Return search results HTML
                        echo $search_results_html;
                        ?>
                    </div>
                </div>

                <!-- Modal for blood request form -->
                <div id="requestModal" style="display:none;">
                    <form id="requestForm">
                        <label for="recipientName">Recipient Name:</label>
                        <input type="text" id="recipientName" name="recipient_name" required>
                        <label for="recipientAge">Recipient Age:</label>
                        <input type="number" id="recipientAge" name="recipient_age" required>
                        <label for="recipientTelNo">Recipient Tel No:</label>
                        <input type="tel" id="recipientTelNo" name="recipient_tel_no" required>
                        <label for="reason">Reason for Request:</label>
                        <textarea id="reason" name="reason_for_request" required></textarea>
                        <input type="hidden" id="donorId" name="donor_id">
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                    </form>
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
