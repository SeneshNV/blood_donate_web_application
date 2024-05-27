<!-- profile section -->
<div class="card">
    <div class="top-container">        
        <div class="ml-3">
            <p style="margin-bottom: -5px;">Welcome,</p>
            <h5><b><?php echo isset($username) ? $username : ''; ?></b></h5>
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
    <div class="login_space">
        <button id="editProfileBtn" type="button" class="custom-btn btn-100" onclick="loadPage('editprofile')">Update Profile</button>
    </div>
    <div>
        <button id="dashboardBtn" type="button" class="nav_btn btn-100 active" onclick="loadPage('dashboard')">Dashboard</button>
        
        <div class="nav_title">
            <h6>Blood Donate</h6>
        </div>
        <button id="donatingBloodBtn" type="button" class="nav_btn btn-100" onclick="loadPage('donatingblood')">Donating Blood</button>
        <button id="requestBloodBtn" type="button" class="nav_btn btn-100" onclick="loadPage('requestblood')">Blood Needed from Me</button>
        
        <div class="nav_title">
            <h6>Blood Receive</h6>
        </div>
        <button id="needBloodBtn" type="button" class="nav_btn btn-100" onclick="loadPage('needblood')">Need Blood</button>
        <button id="viewBloodRequestBtn" type="button" class="nav_btn btn-100" onclick="loadPage('viewBloodRequest')">Blood Requests to Me</button>
        
        <div class="nav_title">
            <h6>Other</h6>
        </div>
        <button id="feedbackAndReviewsBtn" type="button" class="nav_btn btn-100" onclick="loadPage('feedbackAndReviews')">Feedback and Reviews</button>
        <button id="aboutUsBtn" type="button" class="nav_btn btn-100" onclick="loadPage('aboutUs')">About Us</button>
        <br><br>
        <form method="post" action="logout.php" style="display:inline;">
            <button type="submit" class="custom-btn btn-100">Logout</button>
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
                filePath = 'blood_needed_from_me.php';
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