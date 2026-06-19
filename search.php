<?php
require_once 'config/database.php';
require_once 'classes/Database.php';
require_once 'classes/Student.php';
require_once 'includes/header.php';

$database = new Database();
$pdo = $database->getConnection();
$student = new Student($pdo);

$page_title = 'Search Student';
$searchResult = null;
$searchPerformed = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $regNumber = trim($_POST['registration_number'] ?? '');
    if (!empty($regNumber)) {
        $searchResult = $student->searchByRegNumber($regNumber);
        $searchPerformed = true;
        if (!$searchResult) {
            $_SESSION['error'] = 'No student found with registration number: ' . htmlspecialchars($regNumber);
            header('Location: search.php');
            exit();
        }
    }
}

if (isset($_GET['reg']) && empty($searchResult)) {
    $regNumber = trim($_GET['reg']);
    $searchResult = $student->searchByRegNumber($regNumber);
    $searchPerformed = true;
}

if ($searchResult) {
    $fullName = $searchResult['first_name'];
    if (!empty($searchResult['middle_name'])) $fullName .= ' ' . $searchResult['middle_name'];
    $fullName .= ' ' . $searchResult['last_name'];
    $age = date_diff(date_create($searchResult['date_of_birth']), date_create('today'))->y;
}
?>

<div class="page-header">
    <h1><i class="bi bi-search"></i> Search Student</h1>
    <p class="text-muted">Search for a student by registration number</p>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="bi bi-search"></i> Search</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="input-group">
                        <input type="text" class="form-control" id="registration_number" name="registration_number"
                               placeholder="Enter registration number..." required
                               value="<?php echo htmlspecialchars($_POST['registration_number'] ?? ($_GET['reg'] ?? '')); ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                    <small class="text-muted">Example: P.2024/001 or S.2024/001</small>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if ($searchPerformed && $searchResult): ?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card student-detail-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-badge-fill"></i> Student Found
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="student-photo">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($fullName); ?></h3>
                    <span class="badge bg-primary fs-6"><?php echo htmlspecialchars($searchResult['registration_number']); ?></span>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Registration Number:</label>
                        <p><?php echo htmlspecialchars($searchResult['registration_number']); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Full Name:</label>
                        <p><?php echo htmlspecialchars($fullName); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Date of Birth:</label>
                        <p><?php echo date('d/m/Y', strtotime($searchResult['date_of_birth'])); ?> (<?php echo $age; ?> years)</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Gender:</label>
                        <p>
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($searchResult['gender']); ?></span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Level:</label>
                        <p>
                            <span class="badge bg-<?php echo $searchResult['level'] === 'Primary' ? 'success' : 'info'; ?>">
                                <?php echo htmlspecialchars($searchResult['level']); ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Academic Year:</label>
                        <p><?php echo htmlspecialchars($searchResult['academic_year']); ?></p>
                    </div>
                    <?php if (!empty($searchResult['address'])): ?>
                    <div class="col-12 mb-3">
                        <label class="fw-bold">Address:</label>
                        <p><?php echo nl2br(htmlspecialchars($searchResult['address'])); ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($searchResult['phone'])): ?>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Phone:</label>
                        <p><?php echo htmlspecialchars($searchResult['phone']); ?></p>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Guardian Name:</label>
                        <p><?php echo htmlspecialchars($searchResult['guardian_name']); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Guardian Phone:</label>
                        <p><?php echo htmlspecialchars($searchResult['guardian_phone']); ?></p>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="search.php" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> New Search
                    </a>
                    <a href="students.php" class="btn btn-outline-secondary">
                        <i class="bi bi-list-ul"></i> View All Students
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
