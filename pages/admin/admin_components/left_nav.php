<!-- profile section -->
<div class="card">
    <div class="top-container">
        <div class="ml-3">
            <p style="margin-bottom: -5px;">Welcome,</p>
            <h5><b><?php echo isset($username) ? $username : ''; ?></b></h5>
        </div>
    </div>

    <div>
        
        <div class="nav_title">
            <h6>Blood Donate</h6>
        </div>
        <button id="matchingblooddBtn" type="button" class="nav_btn btn-100" onclick="loadPage('matchingblood')">Matched Blood Transactions</button>
        <button id="confirmbloodBtn" type="button" class="nav_btn btn-100" onclick="loadPage('confirmblood')">Confirm Blood Transactions</button>
        
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
            case 'matchingblood':
                filePath = 'matching_blood.php';
                setActiveButton('matchingblooddBtn');
                break;
            case 'confirmblood':
                filePath = 'confirm_donation.php';
                setActiveButton('confirmbloodBtn');
                break;
            default:
                filePath = 'matching_blood.php'; // Default to dashboard
                setActiveButton('matchingblooddBtn');
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