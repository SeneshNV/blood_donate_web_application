<?php
session_start();
include('components/connection.php');

if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
} else {
    header("Location: login.php");
    exit();
}

$stmt = $conn->prepare("SELECT status FROM donation_status WHERE donor_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($current_status);
$stmt->fetch();
$stmt->close();

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
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Bllod Donor</a></li>
        <li class="breadcrumb-item active" aria-current="page">Donating Blood</li>
    </ol>
</nav>

<div class="topic_dashboard">
    <p>Update Donation <b>Status</b></p>
</div>

<div class="col donor_pages">
    <div class="card">
        <h6 class="mb-3">Donation Status</h6>
        
        <form method="post" id="statusForm">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="status" id="like_to_donate" value="like_to_donate" <?php if ($current_status === "like_to_donate") echo "checked"; ?>>
                <label class="form-check-label" for="like_to_donate">
                    Like to donate
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="status" id="cannot_donate_now" value="cannot_donate_now" <?php if ($current_status === "cannot_donate_now") echo "checked"; ?>>
                <label class="form-check-label" for="cannot_donate_now">
                    Cannot donate now
                </label>
            </div>
            <button type="submit" class="custom-btn">Update Status</button>
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        </form>
        <div id="messageContainer"></div>
    </div>
</div>

<div>
<div class="card">
    <h6 class="mb-3">Donation Status</h6>

    <div class="card-body">
        <form method="post">
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required readonly>
            </div>
            <div class="mb-3">
                <label for="contact_info" class="form-label">Contact Info</label>
                <input type="text" class="form-control" id="contact_info" name="contact_info" value="<?php echo htmlspecialchars($user['contact_info']); ?>" required readonly>
            </div>
            <div class="mb-3">
                <label for="blood_type" class="form-label">Blood Type</label>
                <select class="form-select" id="blood_type" name="blood_type" required disabled>
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
                <input type="number" class="form-control" id="age" name="age" value="<?php echo htmlspecialchars($user['age']); ?>" required readonly>
            </div>
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <!-- Remove the submit button -->
        </form>
    </div>
</div>

</div>

