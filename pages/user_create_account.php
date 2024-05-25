class User {
    private $userId;
    private $username;
    private $password;
    private $email;

    public function __construct($userId, $username, $password, $email) {
        $this->userId = $userId;
        $this->username = $username;
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        $this->email = $email;
    }

    public function login($username, $password) {
        // Code to authenticate user
        if ($this->username === $username && password_verify($password, $this->password)) {
            // Start session, set session variables
            session_start();
            $_SESSION['user_id'] = $this->userId;
            $_SESSION['username'] = $this->username;
            return true;
        }
        return false;
    }

    public function register() {
        // Code to register a new user
        include('components/connection.php');
        $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $this->username, $this->password, $this->email);
        return $stmt->execute();
    }

    public function updateProfile($newUsername, $newEmail) {
        // Code to update user profile
        include('components/connection.php');
        $sql = "UPDATE users SET username = ?, email = ? WHERE userId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $newUsername, $newEmail, $this->userId);
        return $stmt->execute();
    }

    public function logout() {
        // Code to log out user
        session_start();
        session_unset();
        session_destroy();
    }
}
