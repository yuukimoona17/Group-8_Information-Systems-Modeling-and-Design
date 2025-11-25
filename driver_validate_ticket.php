<?php
// driver_validate_ticket.php - Ticket Validator (Shows Age)
include 'driver_header.php';
include 'db.php';

$validation_result = null;
$scan_code = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scan_code = trim($_POST['ticket_code']);
    // Lọc lấy số ID
    $ticket_id = filter_var($scan_code, FILTER_SANITIZE_NUMBER_INT);

    if ($ticket_id) {
        // Query lấy thêm cột dob
        $sql = "SELECT t.*, u.full_name, u.username, r.route_name 
                FROM monthly_tickets t 
                JOIN users u ON t.user_id = u.user_id 
                LEFT JOIN routes r ON t.route_id = r.route_id 
                WHERE t.ticket_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ticket_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $ticket = $result->fetch_assoc();
            $today = date('Y-m-d');
            
            // Tính tuổi
            $age = 'N/A';
            if (!empty($ticket['dob'])) {
                $dob = new DateTime($ticket['dob']);
                $now = new DateTime();
                $interval = $now->diff($dob);
                $age = $interval->y;
            }

            // Logic kiểm tra
            if ($ticket['status'] !== 'active') {
                $validation_result = [
                    'status' => 'invalid',
                    'title' => 'INVALID TICKET',
                    'msg' => 'Status: ' . strtoupper($ticket['status']),
                    'icon' => 'bi-x-circle-fill'
                ];
            } elseif ($ticket['end_date'] < $today) {
                $validation_result = [
                    'status' => 'invalid',
                    'title' => 'EXPIRED TICKET',
                    'msg' => 'Expired on ' . date('d/m/Y', strtotime($ticket['end_date'])),
                    'icon' => 'bi-calendar-x-fill'
                ];
            } else {
                // HỢP LỆ
                $scope = ($ticket['ticket_scope'] == 'inter_route') ? 'ALL ROUTES' : 'ROUTE ' . $ticket['route_id'];
                $validation_result = [
                    'status' => 'valid',
                    'title' => 'VALID PASS',
                    'msg' => 'Access Granted',
                    'icon' => 'bi-check-circle-fill',
                    'data' => $ticket,
                    'age' => $age,
                    'scope_text' => $scope
                ];
            }
        } else {
            $validation_result = [
                'status' => 'invalid',
                'title' => 'NOT FOUND',
                'msg' => 'ID #' . $ticket_id . ' does not exist.',
                'icon' => 'bi-search'
            ];
        }
    }
}
?>

<div class="container mt-5 mb-5">
    <a href="driver_dashboard.php" class="text-white-50 text-decoration-none mb-4 d-inline-block">
        <i class="bi bi-arrow-left me-2"></i>Back
    </a>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            
            <div class="validator-box animate-fade-up">
                <div class="text-center mb-4">
                    <h3 class="text-white fw-bold">Ticket Scanner</h3>
                    <p class="text-white-50 small">Enter Ticket ID to verify</p>
                </div>

                <form method="POST" action="">
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-123"></i></span>
                        <input type="tel" name="ticket_code" class="form-control bg-dark border-secondary text-white text-center fs-4 fw-bold" placeholder="ID..." value="<?php echo htmlspecialchars($scan_code); ?>" required autofocus autocomplete="off">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold shadow">CHECK NOW</button>
                    </div>
                </form>
            </div>

            <?php if ($validation_result): ?>
                <?php $res_class = ($validation_result['status'] == 'valid') ? 'result-valid' : 'result-invalid'; ?>
                
                <div class="validation-result <?php echo $res_class; ?> text-center text-white shadow-lg mt-4 position-relative overflow-hidden">
                    
                    <div class="mb-2">
                        <i class="bi <?php echo $validation_result['icon']; ?>" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="fw-bold text-uppercase mb-1"><?php echo $validation_result['title']; ?></h2>
                    <p class="opacity-75 mb-3"><?php echo $validation_result['msg']; ?></p>

                    <?php if ($validation_result['status'] == 'valid'): 
                        $t = $validation_result['data'];
                    ?>
                        <div class="bg-black bg-opacity-25 p-3 rounded-4 mt-3 text-start border border-white border-opacity-10">
                            <div class="d-flex">
                                <img src="<?php echo htmlspecialchars($t['face_image_path']); ?>" class="rounded-3 border border-2 border-white" style="width: 80px; height: 100px; object-fit: cover;">
                                
                                <div class="ms-3 flex-grow-1">
                                    <h5 class="fw-bold mb-1 text-uppercase text-warning"><?php echo htmlspecialchars($t['full_name']); ?></h5>
                                    
                                    <div class="d-flex justify-content-between align-items-center border-bottom border-white border-opacity-25 pb-1 mb-1">
                                        <small class="opacity-75">Age</small>
                                        <span class="fw-bold"><?php echo $validation_result['age']; ?></span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center border-bottom border-white border-opacity-25 pb-1 mb-1">
                                        <small class="opacity-75">Type</small>
                                        <span class="fw-bold text-uppercase"><?php echo $t['priority_type']; ?></span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="opacity-75">Scope</small>
                                        <span class="fw-bold text-info"><?php echo $validation_result['scope_text']; ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3 text-center">
                                <small class="opacity-50 text-uppercase" style="font-size: 0.65rem;">Valid Until</small>
                                <div class="fs-5 fw-bold"><?php echo date('d/m/Y', strtotime($t['end_date'])); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php include 'driver_footer.php'; ?>