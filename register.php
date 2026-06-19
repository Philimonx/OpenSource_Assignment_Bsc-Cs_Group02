<?php
require_once 'config/database.php';
require_once 'classes/Database.php';
require_once 'classes/Student.php';
require_once 'includes/header.php';

$database = new Database();
$pdo = $database->getConnection();
$student = new Student($pdo);

$page_title = 'Register Student';

$currentYear = date('Y');
$nextYear = $currentYear + 1;
$defaultAcademicYear = "{$currentYear}/{$nextYear}";
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registration_number = trim($_POST['registration_number'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $date_of_birth = trim($_POST['date_of_birth'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $level = trim($_POST['level'] ?? '');
    $academic_year = trim($_POST['academic_year'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $guardian_name = trim($_POST['guardian_name'] ?? '');
    $guardian_phone = trim($_POST['guardian_phone'] ?? '');

    if (empty($registration_number)) {
        $errors[] = 'Registration number is required';
    } elseif ($student->exists($registration_number)) {
        $errors[] = 'A student with this registration number already exists';
    }

    if (empty($first_name)) $errors[] = 'First name is required';
    if (empty($last_name)) $errors[] = 'Last name is required';
    if (empty($date_of_birth)) $errors[] = 'Date of birth is required';
    if (empty($gender)) $errors[] = 'Gender is required';
    if (empty($level)) $errors[] = 'Level is required';
    if (empty($academic_year)) $errors[] = 'Academic year is required';
    if (empty($guardian_name)) $errors[] = 'Guardian name is required';
    if (empty($guardian_phone)) $errors[] = 'Guardian phone is required';

    if (empty($errors)) {
        $data = [
            'registration_number' => $registration_number,
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'last_name' => $last_name,
            'date_of_birth' => $date_of_birth,
            'gender' => $gender,
            'level' => $level,
            'academic_year' => $academic_year,
            'address' => $address,
            'phone' => $phone,
            'guardian_name' => $guardian_name,
            'guardian_phone' => $guardian_phone
        ];

        if ($student->register($data)) {
            $_SESSION['success'] = 'Student registered successfully!';
            $success = true;
            header('Location: register.php');
            exit();
        } else {
            $errors[] = 'Failed to register student. Please try again.';
        }
    }
}

$currentYear = date('Y');
$nextYear = $currentYear + 1;
$defaultAcademicYear = "{$currentYear}/{$nextYear}";
?>

<div class="page-header">
    <h1><i class="bi bi-person-plus-fill"></i> Register New Student</h1>
    <p class="text-muted">Enter student information below</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-clipboard-data"></i> Student Information Form</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="registration_number" class="form-label">Registration Number *</label>
                            <input type="text" class="form-control" id="registration_number" name="registration_number"
                                   value="<?php echo htmlspecialchars($_POST['registration_number'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="level" class="form-label">Level *</label>
                            <select class="form-select" id="level" name="level" required>
                                <option value="">-- Select Level --</option>
                                <option value="Primary" <?php echo (($_POST['level'] ?? '') === 'Primary') ? 'selected' : ''; ?>>Primary</option>
                                <option value="Secondary" <?php echo (($_POST['level'] ?? '') === 'Secondary') ? 'selected' : ''; ?>>Secondary</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="academic_year" class="form-label">Academic Year *</label>
                            <input type="text" class="form-control" id="academic_year" name="academic_year"
                                   value="<?php echo htmlspecialchars($_POST['academic_year'] ?? $defaultAcademicYear); ?>" required>
                            <small class="text-muted">Example: 2024/2025</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                   value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="middle_name" name="middle_name"
                                   value="<?php echo htmlspecialchars($_POST['middle_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                   value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth *</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                   value="<?php echo htmlspecialchars($_POST['date_of_birth'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="gender" class="form-label">Gender *</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="">-- Select Gender --</option>
                                <option value="Male" <?php echo (($_POST['gender'] ?? '') === 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo (($_POST['gender'] ?? '') === 'Female') ? 'selected' : ''; ?>>Female</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                   value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                   placeholder="+255 XXX XXX XXX">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="guardian_name" class="form-label">Guardian Name *</label>
                            <input type="text" class="form-control" id="guardian_name" name="guardian_name"
                                   value="<?php echo htmlspecialchars($_POST['guardian_name'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="guardian_phone" class="form-label">Guardian Phone *</label>
                            <input type="tel" class="form-control" id="guardian_phone" name="guardian_phone"
                                   value="<?php echo htmlspecialchars($_POST['guardian_phone'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Dashboard
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> Register Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
