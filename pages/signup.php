<?php
include('components/connection.php');

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $repassword = trim($_POST['repassword']);

    // Validate form data
    if (empty($username) || empty($password) || empty($repassword)) {
        $message = "All fields are required.";
    } elseif ($password !== $repassword) {
        $message = "Passwords do not match.";
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT username FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Username already exists.";
        } else {
            // Hash the password using SHA-256
            $hashed_password = hash('sha256', $password);

            // Insert new user
            $stmt = $conn->prepare("INSERT INTO user (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);

            if ($stmt->execute()) {
                $user_id = $stmt->insert_id; // Get the newly inserted user ID

                // Insert into donation_status table
                $stmt = $conn->prepare("INSERT INTO donation_status (donor_id, status) VALUES (?, 'none')");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();

                $stmt = $conn->prepare("INSERT INTO user_info (user_id) VALUES (?)");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();

                $message = "Registration successful.";
            } else {
                $message = "Error: " . $stmt->error;
            }
        }

        // Close the statement
        $stmt->close();
    }

    // Close the connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Fjalla+One&display=swap" rel="stylesheet">
    <link href="../styles/styles.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container">
                <!-- Left-aligned logo and brand name -->
                <a class="navbar-brand d-flex align-items-center" href="../index.html">
                    <img src="../img/nawaloka_logo.png" alt="Nawaloka Logo" style="height: 40px; margin-right: 10px;"> <!-- Add your logo path here -->
                    Nawaloka Hospitals PLC
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
                    <!-- Right-aligned navigation links -->
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="../index.html">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="signup.php">Create an Account</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <!-- End Right-aligned navigation links -->

    <div class="col-lg-7">
        <div class="login-img-wrap">
            <img src="../img/hand1.png" class="img-fluid" alt="Cover Image">
        </div>
    </div>

    <div class="login-container">
        <div class="login-card">
            <h1>Sign Up to</h1>
            <p>Nawaloka Blood Connect</p><br>
            <form action="signup.php" method="POST">
                <div class="input-group">
                    <input type="text" id="username" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="input-group">
                    <input type="password" id="repassword" name="repassword" placeholder="Re-enter password" required>
                </div>
                <div class="button-container login_space">
                    <button type="submit" class="custom-btn">Join with Nawaloka</button>
                </div>
            </form>
        </div>
    </div>

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