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

// Fetch user data
$sql = "SELECT full_name, contact_info, blood_type, age FROM user_info WHERE user_id=?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();


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
            $_SESSION['message'] = "No rows were updated.";
        } else {
            $_SESSION['message'] = "User data updated successfully.";
        }

        $stmt->close();
    }
}

// Retrieve and clear the message from the session
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
$conn->close();
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
                        <li class="breadcrumb-item"><a href="#">Bllod Donor</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
                    </ol>
                </nav>

                <div class="topic_dashboard">
                    <p>Edit Your <b>Data</b></p>
                </div>

                <div>
                    <div class="card">
                        <h6 class="mb-3">Donation Status</h6>
                        <div class="card-body">
                            <form method="post">
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="contact_info" class="form-label">Contact Info</label>
                                    <input type="text" class="form-control" id="contact_info" name="contact_info" value="<?php echo htmlspecialchars($user['contact_info']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="blood_type" class="form-label">Blood Type</label>
                                    <select class="form-select" id="blood_type" name="blood_type" required>
                                        <option value="">Select Blood Type</option>
                                        <option value="A+" <?php if ($user['blood_type'] === 'A+') echo 'selected'; ?>>A+</option>
                                        <option value="A-" <?php if ($user['blood_type'] === 'A-') echo 'selected'; ?>>A-</option>
                                        <option value="B+" <?php if ($user['blood_type'] === 'B+') echo 'selected'; ?>>B+</option>
                                        <option value="B-" <?php if ($user['blood_type'] === 'B-') echo 'selected'; ?>>B-</option>
                                        <option value="AB+" <?php if ($user['blood_type'] === 'AB+') echo 'selected'; ?>>AB+</option>
                                        <option value="AB-" <?php if ($user['blood_type'] === 'AB-') echo 'selected'; ?>>AB-</option>
                                        <option value="O+" <?php if ($user['blood_type'] === 'O+') echo 'selected'; ?>>O+</option>
                                        <option value="O-" <?php if ($user['blood_type'] === 'O-') echo 'selected'; ?>>O-</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="age" class="form-label">Age</label>
                                    <input type="number" class="form-control" id="age" name="age" value="<?php echo htmlspecialchars($user['age']); ?>" required>
                                </div>
                                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                                <button type="submit" class="custom-btn">Update Profile</button>
                            </form>
                        </div>
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

    <script>
        // Set the active button on page load
        window.onload = function() {
            setActiveButton('editProfileBtn');
        };
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

</body>

</html>