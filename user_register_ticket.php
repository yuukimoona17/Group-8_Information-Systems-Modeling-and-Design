<?php
// user_register_ticket.php - Premium UI + Date of Birth
include 'user_header.php';
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Lấy danh sách tuyến
$routes = $conn->query("SELECT route_id, route_name FROM routes ORDER BY route_id");
?>

<style>
    /* CSS Riêng cho hiệu ứng Glassmorphism */
    .register-container {
        background: url('img/bg-main.jpg') no-repeat center center fixed;
        background-size: cover;
        padding: 50px 0;
        min-height: 100vh;
    }
    
    .glass-card {
        background: rgba(16, 20, 24, 0.85);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5);
        overflow: hidden;
    }

    .glass-header {
        background: linear-gradient(90deg, rgba(13, 110, 253, 0.2) 0%, rgba(13, 202, 240, 0.2) 100%);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 30px;
        text-align: center;
    }

    .form-label-custom {
        color: #a0aec0;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    /* Input Style */
    .form-control-glass, .form-select-glass {
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #fff !important;
        border-radius: 12px;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }

    .form-control-glass:focus, .form-select-glass:focus {
        background: rgba(255, 255, 255, 0.1) !important;
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.25);
    }
    
    /* Fix màu option dropdown */
    .form-select-glass option {
        background-color: #1e293b;
        color: white;
    }

    /* Upload Box */
    .upload-box {
        border: 2px dashed rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        transition: 0.3s;
        cursor: pointer;
        background: rgba(0,0,0,0.2);
    }
    
    .upload-box:hover {
        border-color: #0dcaf0;
        background: rgba(13, 202, 240, 0.05);
    }

    .price-tag {
        background: linear-gradient(135deg, #00b09b, #96c93d);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-size: 2.5rem;
        font-weight: 800;
    }
    
    /* Loader cho thanh toán */
    .loader {
        width: 48px; height: 48px;
        border: 5px solid #FFF;
        border-bottom-color: #0d6efd;
        border-radius: 50%;
        display: inline-block;
        box-sizing: border-box;
        animation: rotation 1s linear infinite;
    }
    @keyframes rotation {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="glass-card animate-fade-up">
                
                <div class="glass-header">
                    <h2 class="text-white fw-bold mb-1">Monthly Pass Registration</h2>
                    <p class="text-white-50 mb-0">Get unlimited access to Hanoi Bus network</p>
                </div>

                <div class="p-4 p-md-5">
                    <form action="user_register_ticket_action.php" method="POST" enctype="multipart/form-data" id="regForm">
                        
                        <h5 class="text-info mb-4"><i class="bi bi-person-vcard me-2"></i>Pass Details</h5>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label-custom">Priority Category</label>
                                <select name="priority_type" id="priority_type" class="form-select-glass" onchange="calcPrice()">
                                    <option value="normal">Normal Passenger (Standard)</option>
                                    <option value="student">Student (High School/University)</option>
                                    <option value="elderly">Elderly (Over 60 years)</option>
                                    <option value="disabled">Person with Disabilities</option>
                                    <option value="contributor">Meritorious Person</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label-custom">Date of Birth</label>
                                <input type="date" name="dob" class="form-control-glass" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label-custom">Scope</label>
                                <select name="ticket_scope" id="ticket_scope" class="form-select-glass" onchange="calcPrice()">
                                    <option value="single_route">Single Route</option>
                                    <option value="inter_route">Inter-bus (All Routes)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-5" id="route_group">
                            <label class="form-label-custom">Select Route</label>
                            <select name="route_id" id="route_id" class="form-select-glass">
                                <option value="">-- Select a Route --</option>
                                <?php while($r = $routes->fetch_assoc()): ?>
                                    <option value="<?php echo $r['route_id']; ?>">
                                        [<?php echo $r['route_id']; ?>] <?php echo $r['route_name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <h5 class="text-info mb-4"><i class="bi bi-camera me-2"></i>Required Photos</h5>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="upload-box" onclick="document.getElementById('face_input').click()">
                                    <div class="mb-2" id="face_placeholder">
                                        <i class="bi bi-person-bounding-box display-4 text-white-50"></i>
                                        <h6 class="text-white mb-1 mt-2">Portrait Photo (3x4)</h6>
                                        <p class="text-white-50 small mb-0">Click to upload</p>
                                    </div>
                                    <img id="preview_face" src="" class="img-fluid rounded d-none" style="max-height: 150px;">
                                    <input type="file" name="face_image" id="face_input" class="d-none" accept="image/*" onchange="preview(this, 'preview_face', 'face_placeholder')" required>
                                </div>
                            </div>

                            <div class="col-md-6" id="evidence_group" style="display:none;">
                                <div class="upload-box" style="border-color: #ffc107;" onclick="document.getElementById('evidence_input').click()">
                                    <div class="mb-2" id="evidence_placeholder">
                                        <i class="bi bi-card-heading display-4 text-warning"></i>
                                        <h6 class="text-warning mb-1 mt-2" id="evidence_label">Student ID Card</h6>
                                        <p class="text-white-50 small mb-0">Required for priority pass</p>
                                    </div>
                                    <img id="preview_evidence" src="" class="img-fluid rounded d-none" style="max-height: 150px;">
                                    <input type="file" name="evidence_image" id="evidence_input" class="d-none" accept="image/*" onchange="preview(this, 'preview_evidence', 'evidence_placeholder')">
                                </div>
                            </div>
                        </div>

                        <div class="bg-black bg-opacity-25 p-4 rounded-4 mb-4 border border-secondary border-opacity-25">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-white-50 text-uppercase small fw-bold">Total Payment</div>
                                    <div class="text-white small">Valid for 1 month</div>
                                </div>
                                <div class="text-end">
                                    <div class="price-tag" id="price_display">0 VND</div>
                                    <input type="hidden" name="final_price" id="final_price" value="0">
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="button" onclick="processPayment()" class="btn btn-primary btn-lg rounded-pill fw-bold py-3 shadow-lg">
                                Proceed to Payment <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold"><i class="bi bi-qr-code-scan me-2"></i>Payment Gateway</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body text-center p-4" id="paymentStep1">
                <p class="text-white-50 mb-3">Scan to pay via Banking App</p>
                <div class="bg-white p-3 rounded d-inline-block mb-3">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=PAY-TO-HANOI-BUS" alt="QR">
                </div>
                <h3 class="text-success fw-bold mb-1" id="modal_price">0 VND</h3>
                <hr class="border-secondary">
                <button onclick="confirmPayment()" class="btn btn-success w-100 py-2 fw-bold">
                    <i class="bi bi-check-circle-fill me-2"></i>I Have Paid
                </button>
            </div>

            <div class="modal-body text-center p-5" id="paymentStep2" style="display: none;">
                <span class="loader mb-4"></span>
                <h5 class="fw-bold">Verifying Transaction...</h5>
                <p class="text-white-50 small">Please do not close this window.</p>
            </div>

            <div class="modal-body text-center p-5" id="paymentStep3" style="display: none;">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                <h4 class="fw-bold mt-3">Success!</h4>
                <p class="text-white-50 small">Redirecting to receipt...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Logic tính tiền
function calcPrice() {
    const type = document.getElementById('priority_type').value;
    const scope = document.getElementById('ticket_scope').value;
    const routeGroup = document.getElementById('route_group');
    const evidenceGroup = document.getElementById('evidence_group');
    const evidenceInput = document.getElementById('evidence_input');
    const evidenceLabel = document.getElementById('evidence_label');
    
    // Show/Hide Route
    if (scope === 'inter_route') {
        routeGroup.style.display = 'none';
        document.getElementById('route_id').required = false;
    } else {
        routeGroup.style.display = 'block';
        document.getElementById('route_id').required = true;
    }

    // Show/Hide Evidence
    if (type === 'normal') {
        evidenceGroup.style.display = 'none';
        evidenceInput.required = false;
    } else {
        evidenceGroup.style.display = 'block';
        evidenceInput.required = true;
        if(type === 'student') evidenceLabel.innerText = "Student ID Card";
        else if(type === 'elderly') evidenceLabel.innerText = "ID / CCCD";
        else evidenceLabel.innerText = "Certificate";
    }

    // Calculate
    let price = 0;
    if (type === 'normal') {
        price = (scope === 'single_route') ? 140000 : 200000;
    } else if (type === 'student') {
        price = (scope === 'single_route') ? 70000 : 140000;
    } else {
        price = 0; // Free
    }

    const priceStr = new Intl.NumberFormat('vi-VN').format(price) + " VND";
    document.getElementById('price_display').innerText = priceStr;
    document.getElementById('modal_price').innerText = priceStr;
    document.getElementById('final_price').value = price;
}

// Preview Ảnh
function preview(input, imgId, placeholderId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(imgId).src = e.target.result;
            document.getElementById(imgId).classList.remove('d-none');
            document.getElementById(placeholderId).classList.add('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Giả lập thanh toán
function processPayment() {
    const form = document.getElementById('regForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const price = parseInt(document.getElementById('final_price').value);
    if (price === 0) {
        form.submit(); // Miễn phí thì submit luôn
    } else {
        // Có phí thì hiện QR
        const myModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        myModal.show();
    }
}

function confirmPayment() {
    document.getElementById('paymentStep1').style.display = 'none';
    document.getElementById('paymentStep2').style.display = 'block';
    
    setTimeout(() => {
        document.getElementById('paymentStep2').style.display = 'none';
        document.getElementById('paymentStep3').style.display = 'block';
        setTimeout(() => {
            document.getElementById('regForm').submit();
        }, 1000);
    }, 2000);
}

// Run on load
document.addEventListener('DOMContentLoaded', calcPrice);
</script>

<?php include 'user_footer.php'; ?>