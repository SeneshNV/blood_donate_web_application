<?php


// Include the database connection file
include('components/connection.php');

// Check if the request ID is set in the POST request
if (isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];

    // Prepare the SQL statement to delete the request
    $stmt = $conn->prepare("DELETE FROM blood_request WHERE id = ?");
    $stmt->bind_param("i", $request_id);

    // Execute the statement and check if it was successful
    if ($stmt->execute()) {
        $_SESSION['message'] = "Request deleted successfully.";
    } else {
        $_SESSION['message'] = "Error deleting request.";
    }

    // Close the statement
    $stmt->close();
} else {
    $_SESSION['message'] = "No request ID provided.";
}

// Close the connection
$conn->close();

// Redirect back to the dashboard
header("Location: view_blood_request.php");
exit();
?>
