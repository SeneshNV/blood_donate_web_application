<!-- profile section -->
<div class="card">
    <div class="top-container">
        <img src="../img/2.jpg" class="img-fluid profile-image" width="70">
        <div class="ml-3">
            <h5 class="name"><?php echo isset($username) ? $username : ''; ?></h5>
            <p class="mail">seneshnv@zmail.com</p>
        </div>
    </div>

    <table>
        <tr>
            <td>Total Donations</td>
            <td>--</td>
        </tr>
        <tr>
            <td>Total Requests</td>
            <td>--</td>
        </tr>
        <tr>
            <td>Feedback Rate</td>
            <td>--/5</td>
        </tr>
    </table>
    <br>
    <div class="button-container login_space">
        <button type="button" class="custom-btn" onclick="loadPage('editprofile')">Update Profile</button>
    </div>
    <br>
    <div class="button-container">
        <button id="dashboardBtn" type="button" class="nav_btn active" onclick="loadPage('dashboard')">Dashboard</button>
        <button id="donatingBloodBtn" type="button" class="nav_btn" onclick="loadPage('donatingblood')">Donating Blood</button>
        <button id="needBloodBtn" type="button" class="nav_btn" onclick="loadPage('needblood')">Need Blood</button>
        <button id="requestBloodBtn" type="button" class="nav_btn" onclick="loadPage('requestblood')">Blood Request</button>
        <button id="viewBloodRequestBtn" type="button" class="nav_btn" onclick="loadPage('viewBloodRequest')">View Blood Requests</button>
        <button id="totalDonationsBtn" type="button" class="nav_btn" onclick="loadPage('totalDonations')">Total Donations</button>
        <button id="feedbackAndReviewsBtn" type="button" class="nav_btn" onclick="loadPage('feedbackAndReviews')">Feedback and Reviews</button>
        <button id="aboutUsBtn" type="button" class="nav_btn" onclick="loadPage('aboutUs')">About Us</button>
        <br><br>
        <form method="post" action="logout.php" style="display:inline;">
            <button type="submit" class="custom-btn">Logout</button>
        </form>
    </div>
</div>
<!-- End profile section -->

<script>
    function loadPage(pageName) {
        var user_id = <?php echo json_encode($user_id); ?>;

        var filePath = '';
        switch (pageName) {
            case 'dashboard':
                filePath = 'dashboard.php';
                setActiveButton('dashboardBtn');
                break;
            case 'donatingblood':
                filePath = 'donating_blood.php';
                setActiveButton('donatingBloodBtn');
                break;
            case 'needblood':
                filePath = 'need_blood.php';
                setActiveButton('needBloodBtn');
                break;
            case 'requestblood':
                filePath = 'request.php';
                setActiveButton('requestBloodBtn');
                break;
            case 'viewBloodRequest':
                filePath = 'view_blood_request.php';
                setActiveButton('viewBloodRequestBtn');
                break;
            case 'totalDonations':
                filePath = 'total_donations.php';
                setActiveButton('totalDonationsBtn');
                break;
            case 'feedbackAndReviews':
                filePath = 'feedback_and_reviews.php';
                setActiveButton('feedbackAndReviewsBtn');
                break;
            case 'aboutUs':
                filePath = 'about_us.php';
                setActiveButton('aboutUsBtn');
                break;
            case 'editprofile':
                filePath = 'edit_profile.php';
                setActiveButton('editProfileBtn');
                break;
            default:
                filePath = 'dashboard.php'; // Default to dashboard
                setActiveButton('dashboardBtn');
                break;
        }
        window.location.href = filePath;
    }

    function setActiveButton(buttonId) {
        // Remove active class from all buttons
        var buttons = document.querySelectorAll('.nav_btn');
        buttons.forEach(function(button) {
            button.classList.remove('active');
        });
        // Add active class to the specified button
        document.getElementById(buttonId).classList.add('active');
    }
</script>
