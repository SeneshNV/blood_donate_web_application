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
$sql = "SELECT u.id as user_id, u.username, ui.blood_type, ui.age, ds.status 
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
    $stmt->bind_param("is", $current_user_id, $search_blood_type);
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
        // Add request button with donor ID as a data attribute
        $search_results_html .= "<td><button class='btn btn-primary request-button' data-donor-id='" . $row['user_id'] . "'>Request</button></td>";
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
