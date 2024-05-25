<?php
session_start();
include('components/connection.php');

// Retrieve user_id from URL parameter or session
if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    $_SESSION['user_id'] = $user_id; // Store user_id in session for further requests
} elseif (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    // Handle if user_id is not present
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
$conn->close();
?>

<head>

    <link href="../styles/users_styles.css" rel="stylesheet">

</head>

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