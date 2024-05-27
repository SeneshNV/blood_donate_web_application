<?php
// Function to validate input data
function validate_input($data, $max_length = null) {
    $validated_data = htmlspecialchars(stripslashes(trim($data)));
    // Check if max_length is provided and trim the string accordingly
    if ($max_length !== null) {
        $validated_data = substr($validated_data, 0, $max_length);
    }
    return $validated_data;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donor_id'])) {
    $donor_id = validate_input($_POST['donor_id']);
    $recipient_name = validate_input($_POST['recipient_name'], 100); // Limit to 100 characters
    $recipient_age = validate_input($_POST['recipient_age']);
    $recipient_tel_no = validate_input($_POST['recipient_tel_no']);
    $reason_for_request = validate_input($_POST['reason_for_request'], 250); // Limit to 250 characters
    $request_status = 'pending';

    // Validate recipient age (between 1 and 120)
    if (!filter_var($recipient_age, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 120)))) {
        $_SESSION['message'] = "Recipient age must be between 1 and 120.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Validate recipient telephone number (only numbers and + allowed)
    if (!preg_match("/^[0-9+]+$/", $recipient_tel_no)) {
        $_SESSION['message'] = "Recipient telephone number can only contain numbers and +.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Check if the session has user_id
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['message'] = "User not logged in!";
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Prepare SQL query to insert data
    $insert_stmt = $conn->prepare("INSERT INTO blood_request (recipient_id, donor_id, blood_recipient_name, recipient_age, recipient_tel_no, reason_for_request, request_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insert_stmt->bind_param("iisiiss", $user_id, $donor_id, $recipient_name, $recipient_age, $recipient_tel_no, $reason_for_request, $request_status);

    if ($insert_stmt->execute()) {
        $_SESSION['message'] = "Blood Request submitted successfully!";
    } else {
        $_SESSION['message'] = "Error: " . $insert_stmt->error;
    }

    $insert_stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

?>
