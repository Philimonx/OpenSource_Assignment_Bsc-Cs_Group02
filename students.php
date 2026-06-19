<?php
require_once 'config/database.php';
require_once 'classes/Database.php';
require_once 'classes/Student.php';
require_once 'includes/header.php';

$database = new Database();
$pdo = $database->getConnection();
$student = new Student($pdo);

$page_title = 'Student Records';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['selected_level'] = $_POST['level'] ?? '';
    $_SESSION['selected_year'] = $_POST['academic_year'] ?? '';
    header('Location: students.php');
    exit();
}

$level = $_SESSION['selected_level'] ?? '';
$academicYear = $_SESSION['selected_year'] ?? '';
unset($_SESSION['selected_level'], $_SESSION['selected_year']);

$students = $student->getAll(
    !empty($level) ? $level : null,
    !empty($academicYear) ? $academicYear : null
);

$academicYears = $student->getAcademicYears();
arsort($academicYears);
$currentYear = date('Y');
$nextYear = $currentYear + 1;
$defaultAcademicYear = "{$currentYear}/{$nextYear}";
?>

<div class="page-header">
    <h1><i class="bi bi-people-fill"></i> Student Records</h1>
    <p class="text-muted">View and filter all registered students</p>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-funnel-fill"></i> Filter Students</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" class="row g-3">
                    <div class="col-md-4">
                        <label for="level" class="form-label">Level</label>
                        <select class="form-select" id="level" name="level">
                            <option value="">All Levels</option>
                            <option value="Primary" <?php echo $level === 'Primary' ? 'selected' : ''; ?>>Primary</option>
                            <option value="Secondary" <?php echo $level === 'Secondary' ? 'selected' : ''; ?>>Secondary</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="academic_year" class="form-label">Academic Year</label>
                        <select class="form-select" id="academic_year" name="academic_year">
                            <option value="">All Academic Years</option>
                            <?php foreach ($academicYears as $yr): ?>
                                <option value="<?php echo htmlspecialchars($yr); ?>" <?php echo $academicYear === $yr ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($yr); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel-fill"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <a href="students.php" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <p class="text-muted">
            Showing <strong><?php echo number_format(count($students)); ?></strong> student(s)
            <?php if ($level || $academicYear): ?>
                (filtered)
            <?php endif; ?>
        </p>
    </div>
</div>

<?php if (empty($students)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <h4 class="text-muted mt-3">No students found</h4>
            <p class="text-muted">Try adjusting your filters or register a new student.</p>
            <a href="register.php" class="btn btn-primary">Register Student</a>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Reg. Number</th>
                            <th>Full Name</th>
                            <th>Date of Birth</th>
                            <th>Gender</th>
                            <th>Level</th>
                            <th>Academic Year</th>
                            <th>Guardian</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $s): ?>
                            <?php
                                $fullName = $s['first_name'];
                                if (!empty($s['middle_name'])) $fullName .= ' ' . $s['middle_name'];
                                $fullName .= ' ' . $s['last_name'];
                            ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($s['registration_number']); ?></strong></td>
                                <td><?php echo htmlspecialchars($fullName); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($s['date_of_birth'])); ?></td>
                                <td><?php echo htmlspecialchars($s['gender']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $s['level'] === 'Primary' ? 'success' : 'info'; ?>">
                                        <?php echo htmlspecialchars($s['level']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($s['academic_year']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($s['guardian_name']); ?><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($s['guardian_phone']); ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="search.php?reg=<?php echo urlencode($s['registration_number']); ?>" class="btn btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
