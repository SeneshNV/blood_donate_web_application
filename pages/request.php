<?php
// Include the database connection file
include('components/connection.php');

// Start the session
session_start();
if (isset($_SESSION['user_id'])) {
    $current_user_id = $_SESSION['user_id'];
} else {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $donor_id = $_POST['donor_id'];
    $recipient_name = $_POST['recipient_name'];
    $recipient_age = $_POST['recipient_age'];
    $recipient_tel_no = $_POST['recipient_tel_no'];
    $reason_for_request = $_POST['reason_for_request'];
    $request_status = 'pending'; // Default status
    $date = date('Y-m-d H:i:s');

    // Prepare SQL query to insert the blood request
    $sql = "INSERT INTO blood_request (recipient_id, donor_id, date, reason_for_request, blood_recipient_name, recipient_age, recipient_tel_no, request_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Check if the statement was prepared correctly
    if ($stmt === false) {
        die("Error preparing SQL statement: " . $conn->error);
    }

    // Bind parameters to the SQL query
    $stmt->bind_param("iisssiss", $current_user_id, $donor_id, $date, $reason_for_request, $recipient_name, $recipient_age, $recipient_tel_no, $request_status);

    // Execute the SQL query
    if ($stmt->execute()) {
        echo "Request submitted successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Request Form</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0;">

    <nav aria-label="breadcrumb" style="background-color: #e9ecef; padding: 10px;">
        <ol class="breadcrumb" style="margin: 0; padding: 0; list-style: none; display: flex;">
            <li class="breadcrumb-item" style="margin-right: 5px;"><a href="#" style="text-decoration: none; color: #007bff;">Blood Donor</a></li>
            <li class="breadcrumb-item active" aria-current="page" style="margin-right: 5px;">Need Blood</li>
        </ol>
    </nav>

    <div class="topic_dashboard" style="padding: 20px; text-align: center;">
        <p style="font-size: 24px; margin: 0;">Request <b>Blood</b></p>
    </div>

    <form method="POST" action="need_blood.php" style="max-width: 600px; margin: 20px auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
        <div class="input-group mb-3" style="margin-bottom: 15px;">
            <label for="donor_id" style="display: block; margin-bottom: 5px;">Donor ID:</label>
            <input type="text" id="donor_id" name="donor_id" required style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px;">
        </div>
        <div class="input-group mb-3" style="margin-bottom: 15px;">
            <label for="recipient_name" style="display: block; margin-bottom: 5px;">Recipient Name:</label>
            <input type="text" id="recipient_name" name="recipient_name" required style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px;">
        </div>
        <div class="input-group mb-3" style="margin-bottom: 15px;">
            <label for="recipient_age" style="display: block; margin-bottom: 5px;">Recipient Age:</label>
            <input type="number" id="recipient_age" name="recipient_age" required style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px;">
        </div>
        <div class="input-group mb-3" style="margin-bottom: 15px;">
            <label for="recipient_tel_no" style="display: block; margin-bottom: 5px;">Recipient Telephone Number:</label>
            <input type="text" id="recipient_tel_no" name="recipient_tel_no" required style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px;">
        </div>
        <div class="input-group mb-3" style="margin-bottom: 15px;">
            <label for="reason_for_request" style="display: block; margin-bottom: 5px;">Reason for Request:</label>
            <textarea id="reason_for_request" name="reason_for_request" required style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px;"></textarea>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Submit Request</button>
    </form>
</body>
</html>
