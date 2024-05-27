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

// Retrieve and clear the message from the session
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Fetch blood request data where recipient_id matches the user_id
$sql = "SELECT br.*, u.username AS donor_name
        FROM blood_request br
        LEFT JOIN user u ON br.donor_id = u.id
        WHERE br.recipient_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donor's Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <li class="breadcrumb-item">User</li>
                        <li class="breadcrumb-item active" aria-current="page">View Blood Request</li>
                    </ol>
                </nav>

                <div class="topic_dashboard">
                    <p>View <b>Blood Request</b></p>
                </div>


                <?php if ($message) : ?>
                    <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Donor Name</th>
                                <th>Blood Recipient Name</th>
                                <th>Request Status</th>
                                <th>Donate Date</th>
                                <th>Donate Time</th>
                                <th>Donate Location</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) : ?>
                                <tr>
                                    <td><?php echo $row['donor_id'] == 0 ? 'Any' : htmlspecialchars($row['donor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['blood_recipient_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['request_status']); ?></td>
                                    <td><?php echo htmlspecialchars($row['donate_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['donate_time']); ?></td>
                                    <td><?php echo htmlspecialchars($row['donate_location']); ?></td>
                                    <td>
                                        <form action="delete_request.php" method="post" onsubmit="return confirm('Are you sure you want to delete this request?');">
                                            <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="custom-btn">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
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
            setActiveButton('viewBloodRequestBtn');
        };
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>