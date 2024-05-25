class BloodRequest {
    private $requestId;
    private $recipientId;
    private $donorId;
    private $date;
    private $reasonForRequest;
    private $recipientName;
    private $recipientAge;
    private $recipientTelNo;
    private $requestStatus;

    public function __construct($recipientId, $donorId, $reasonForRequest, $recipientName, $recipientAge, $recipientTelNo) {
        $this->recipientId = $recipientId;
        $this->donorId = $donorId;
        $this->date = date('Y-m-d H:i:s');
        $this->reasonForRequest = $reasonForRequest;
        $this->recipientName = $recipientName;
        $this->recipientAge = $recipientAge;
        $this->recipientTelNo = $recipientTelNo;
        $this->requestStatus = 'pending';
    }

    public function createRequest() {
        // Code to insert the blood request into the database
        include('components/connection.php');
        $sql = "INSERT INTO blood_request (recipient_id, donor_id, date, reason_for_request, blood_recipient_name, recipient_age, recipient_tel_no, request_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisssiss", $this->recipientId, $this->donorId, $this->date, $this->reasonForRequest, $this->recipientName, $this->recipientAge, $this->recipientTelNo, $this->requestStatus);
        return $stmt->execute();
    }

    public function updateRequestStatus($newStatus) {
        // Code to update the status of the blood request
        include('components/connection.php');
        $sql = "UPDATE blood_request SET request_status = ? WHERE request_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $newStatus, $this->requestId);
        return $stmt->execute();
    }

    public function getRequestDetails($requestId) {
        // Code to retrieve request details
        include('components/connection.php');
        $sql = "SELECT * FROM blood_request WHERE request_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $requestId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
