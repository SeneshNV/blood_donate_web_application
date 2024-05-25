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

// Process form submission to update user data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['full_name'], $_POST['contact_info'], $_POST['blood_type'], $_POST['age'])) {
        $full_name = $_POST['full_name'];
        $contact_info = $_POST['contact_info'];
        $blood_type = $_POST['blood_type'];
        $age = $_POST['age'];

        $sql = "UPDATE user_info SET full_name=?, contact_info=?, blood_type=?, age=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }

        $stmt->bind_param("ssssi", $full_name, $contact_info, $blood_type, $age, $user_id);
        if (!$stmt->execute()) {
            // Log SQL error to a file
            error_log('Execute failed: ' . htmlspecialchars($stmt->error));
            die('Execute failed: ' . htmlspecialchars($stmt->error));
        }

        // Check if any rows were affected by the update
        if ($stmt->affected_rows === 0) {
            $message = "No rows were updated.";
        } else {
            $message = "User data updated successfully.";
        }

        $stmt->close();
    } elseif (isset($_POST['status'])) {
        $new_status = $_POST['status'];
    
        $stmt = $conn->prepare("UPDATE donation_status SET status = ? WHERE donor_id = ?");
        $stmt->bind_param("si", $new_status, $user_id);
        $stmt->execute();
    
        // Check if any rows were affected by the update
        $affected_rows = $stmt->affected_rows;
    
        $stmt->close();
    

        $message = "Status updated successfully.";
        
    
        $_SESSION['message'] = $message;
    
        // Retrieve current status
        $stmt = $conn->prepare("SELECT status FROM donation_status WHERE donor_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($current_status);
        $stmt->fetch();
        $stmt->close();
    }
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

    <!-- dashboard section -->
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <!-- profile section -->
                <div class="card">
                    <div class="top-container">
                        <img src="../img/2.jpg" class="img-fluid profile-image" width="70">
                        <div class="ml-3">
                            <h5 class="name"><?php echo isset($username) ? $username : ''; ?></h5>
                            <p class="mail">seneshnv@zmail.com</p>
                        </div>
                    </div>
                    <table>
                        <tr>
                            <td>Total Donations</td>
                            <td>--</td>
                        </tr>
                        <tr>
                            <td>Total Request</td>
                            <td>--</td>
                        </tr>
                        <tr>
                            <td>Feedback Rate</td>
                            <td>--/5</td>
                        </tr>
                    </table>
                    <br>
                    <div class="button-container login_space">
                        <button type="button" class="custom-btn" onclick="loadPage('editprofile')">Edit Profile</button>
                    </div>
                </div>
                <!-- End profile section -->

                <!-- profile section -->
                <div class="card">
                    <div class="top-container">
                        <div>
                            <h5>Welcome <b><?php echo isset($username) ? $username : ''; ?></b></h5>
                        </div>
                    </div>
                    <div class="button-container">
                        <button type="button" class="nav_btn active" onclick="loadPage('dashboard')">Dashboard</button>
                        <button type="button" class="nav_btn" onclick="loadPage('donatingblood')">Donating Blood</button>
                        <button type="button" class="nav_btn" onclick="loadPage('needblood')">Need a Blood</button>
                        <button type="button" class="nav_btn" onclick="loadPage('rerquestblood')">Blood Request</button>
                        <button type="button" class="nav_btn" onclick="loadPage('viewBloodRequest')">View Blood Request</button>
                        <button type="button" class="nav_btn" onclick="loadPage('totalDonations')">Total Donations</button>
                        <button type="button" class="nav_btn" onclick="loadPage('feedbackAndReviews')">Feedback and Reviews</button>
                        <button type="button" class="nav_btn" onclick="loadPage('aboutUs')">About Us</button>
                        <form method="post" action="logout.php" style="display:inline;">
                            <button type="submit" class="btn logout-btn">Logout</button>
                        </form>
                    </div>
                </div>
                <!-- End profile section -->
            </div>

            <div class="col donor_pages">
                <?php include('dashboard.php'); ?>
            </div>
        </div>
    </div>
    <!-- End dashboard section -->

    <!-- start footer -->
    <footer class="footer" style="margin-top: 40px;">
        <?php include('components/footer.php'); ?>
    </footer>
    <!-- End footer -->

    <script>
        function loadPage(pageName) {
            var user_id = <?php echo json_encode($user_id); ?>;

            var filePath = '';
            switch (pageName) {
                case 'dashboard':
                    filePath = 'dashboard.php';
                    break;
                case 'donatingblood':
                    filePath = 'donating_blood.php';
                    break;
                case 'needblood':
                    filePath = 'need_blood.php';
                    break;
                case 'rerquestblood':
                    filePath = 'request.php';
                    break;
                case 'viewBloodRequest':
                    filePath = 'view_blood_request.php';
                    break;
                case 'totalDonations':
                    filePath = 'total_donations.php';
                    break;
                case 'feedbackAndReviews':
                    filePath = 'feedback_and_reviews.php';
                    break;
                case 'aboutUs':
                    filePath = 'about_us.php';
                    break;
                case 'editprofile':
                    filePath = 'edit_profile.php';
                    break;
                default:
                    filePath = 'dashboard.php'; // Default to dashboard
                    break;
            }

            filePath += '?user_id=' + user_id;

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.querySelector('.donor_pages').innerHTML = this.responseText;
                }
            };

            xhttp.open("GET", filePath, true);
            xhttp.send();
        }

        // Add event listener to blood type dropdown
        document.querySelector('[name="blood_type"]').addEventListener('change', function() {
            this.closest('form').submit(); // Submit the form when blood type is selected
        });
    </script>

    <!-- Modal Structure -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php
                    if (!empty($message)) {
                        echo "<div class='message'>$message</div>";
                    }
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

    <?php if (!empty($message)) : ?>
        <script>
            var myModal = new bootstrap.Modal(document.getElementById('messageModal'), {
                keyboard: false
            });
            myModal.show();
        </script>
    <?php endif; ?>
</body>

</html>