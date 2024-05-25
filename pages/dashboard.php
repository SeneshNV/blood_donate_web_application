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
                <button class="btn btn-primary d-md-none custom-btn" type="button" data-bs-toggle="collapse" data-bs-target="#leftNav" aria-expanded="false" aria-controls="leftNav">
                    ☰ Profile
                </button>
                <div class="collapse d-md-block" id="leftNav">
                    <?php include('components/left_nav.php'); ?>
                </div>
            </div>

            <div class="col">

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">User</li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </nav>

                <div class="topic_dashboard">
                    <p>Latest <b>News</b></p>
                </div>

                <div id="newsCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img class="d-block w-100" src="../img/banner.jpeg" alt="First slide"><br>
                            <h5>Give blood and keep the world beating | 2024 | Colombo 04</h5>
                            <p>Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type</p>
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100" src="../img/banner.jpeg" alt="Second slide"><br>
                            <h5>Give blood and keep the world beating | 2024 | Colombo 04</h5>
                            <p>Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type</p>
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100" src="../img/banner.jpeg" alt="Third slide"><br>
                            <h5>Give blood and keep the world beating | 2024 | Colombo 04</h5>
                            <p>Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type</p>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#newsCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#newsCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <footer class="footer" style="margin-top: 40px;">
        <?php include('components/footer.php'); ?>
    </footer>

    <!-- Modal Structure -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($message)) : ?>
    <script>
        var myModal = new bootstrap.Modal(document.getElementById('messageModal'), {
            keyboard: false
        });
        myModal.show();
    </script>
<?php endif; ?>

    <script>
        // Set the active button on page load
        window.onload = function() {
            setActiveButton('dashboardBtn');
        };
    </script>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

</body>

</html>