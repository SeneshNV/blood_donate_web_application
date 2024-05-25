<head>
    <link href="../styles/users_styles.css" rel="stylesheet">
</head>

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
        <button class="btn btn-primary" id="searchButton">Search</button>
    </div>

    <div id="searchResults"></div>
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

<script>
    // Function to handle search button click
    document.getElementById('searchButton').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default button click behavior
        var bloodType = document.getElementById('bloodTypeSelect').value; // Get selected blood type

        // Make AJAX request to fetch search results
        fetch('search.php', {
                method: 'POST',
                body: JSON.stringify({ blood_type: bloodType }), // Pass blood type in JSON format
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('searchResults').innerHTML = data; // Display search results

                // Add event listeners to request buttons
                document.querySelectorAll('.request-button').forEach(button => {
                    button.addEventListener('click', function() {
                        var donorId = this.getAttribute('data-donor-id');
                        document.getElementById('donorId').value = donorId;
                        document.getElementById('requestModal').style.display = 'block';
                    });
                });
            })
            .catch(error => console.error('Error:', error));
    });

    // Handle form submission
    document.getElementById('requestForm').addEventListener('submit', function(event) {
        event.preventDefault();

        // Gather form data
        var formData = new FormData(this);

        // Make AJAX request to submit the blood request
        fetch('request.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert('Request submitted successfully');
            document.getElementById('requestModal').style.display = 'none';
        })
        .catch(error => console.error('Error:', error));
    });
</script>



<?php
// Include the database connection file
include('components/connection.php');

// Retrieve current user's ID from session
session_start();
if (isset($_SESSION['user_id'])) {
    $current_user_id = $_SESSION['user_id'];
} else {
    // Handle if user is not logged in
    header("Location: login.php");
    exit();
}

// Initialize SQL query
$sql = "SELECT u.username, ui.blood_type, ui.age, ds.status 
        FROM user u
        JOIN user_info ui ON u.id = ui.user_id
        JOIN donation_status ds ON u.id = ds.donor_id
        WHERE ds.status = 'like_to_donate'
        AND u.id != ?";

// Check if blood_type parameter is set (search query)
if (isset($_POST['blood_type']) && !empty($_POST['blood_type'])) {
    $search_blood_type = $_POST['blood_type'];
    // Add blood type condition to SQL query
    $sql .= " AND ui.blood_type = ?";
}

// Prepare and execute SQL query
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_user_id);
if (isset($search_blood_type)) {
    $stmt->bind_param("s", $search_blood_type);
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donor_id = $_POST['donor_id'];
    $recipient_name = $_POST['recipient_name'];
    $recipient_age = $_POST['recipient_age'];
    $recipient_tel_no = $_POST['recipient_tel_no'];
    $reason_for_request = $_POST['reason_for_request'];
    $request_status = 'pending'; // or any default status
    $date = date('Y-m-d H:i:s');

    // Prepare and execute SQL query to insert the blood request
    $sql = "INSERT INTO blood_request (recipient_id, donor_id, date, reason_for_request, blood_recipient_name, recipient_age, recipient_tel_no, request_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssiss", $current_user_id, $donor_id, $date, $reason_for_request, $recipient_name, $recipient_age, $recipient_tel_no, $request_status);

    if ($stmt->execute()) {
        echo "Request submitted successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

?>