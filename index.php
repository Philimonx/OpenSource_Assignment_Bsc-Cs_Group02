<?php
session_start();

require_once 'config/database.php';
require_once 'classes/Database.php';
require_once 'classes/Student.php';
require_once 'includes/header.php';

$database = new Database();
$pdo = $database->getConnection();
$student = new Student($pdo);

$page_title = 'Home';

$currentYear = date('Y');
$nextYear = $currentYear + 1;
$currentAcademicYear = "{$currentYear}/{$nextYear}";
$totalStudents = $student->getCount();
$primaryStudents = $student->getCount('Primary');
$secondaryStudents = $student->getCount('Secondary');
$studentsCurrentYear = $student->getCount(null, $currentAcademicYear);
$academicYears = $student->getAcademicYears();
arsort($academicYears);
$recentStudents = $student->getAll();
$recentStudents = array_slice($recentStudents, 0, 5);
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="jumbotron p-4 mb-3 bg-white border-0 shadow-sm">
            <h1 class="display-5 fw-bold text-success">
                <i class="bi bi-mortarboard-fill me-2"></i>The Green Gate Academy
            </h1>
            <p class="lead">Student Management System (SMS)</p>
            <hr class="my-4">
            <p>Welcome to The Green Gate Academy SMS. Efficiently manage student records for our educational community.</p>
        </div>
    </div>
</div>

<div class="dashboard">
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($totalStudents); ?></h3>
                    <p>Total Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon primary-level">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($primaryStudents); ?></h3>
                    <p>Primary Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon secondary-level">
                    <i class="bi bi-book-fill"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($secondaryStudents); ?></h3>
                    <p>Secondary Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon academic">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($studentsCurrentYear); ?></h3>
                    <p>Current Year (<?php echo $currentAcademicYear; ?>)</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-info-circle"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="register.php" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-person-plus-fill me-2"></i> Register New Student
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="students.php" class="btn btn-outline-primary btn-lg w-100">
                                <i class="bi bi-list-ul me-2"></i> View All Students
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="search.php" class="btn btn-outline-info btn-lg w-100">
                                <i class="bi bi-search me-2"></i> Search Student
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($academicYears): ?>
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-calendar3"></i> Academic Year Statistics</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Academic Year</th>
                                <th>Total Students</th>
                                <th>Primary</th>
                                <th>Secondary</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($academicYears as $year): ?>
                            <?php
                                $yearTotal = $student->getCount(null, $year);
                                $yearPrimary = $student->getCount('Primary', $year);
                                $yearSecondary = $student->getCount('Secondary', $year);
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($year); ?></td>
                                <td><?php echo number_format($yearTotal); ?></td>
                                <td><?php echo number_format($yearPrimary); ?></td>
                                <td><?php echo number_format($yearSecondary); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($recentStudents): ?>
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-clock-history"></i> Recently Registered Students</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Reg. Number</th>
                                <th>Full Name</th>
                                <th>Level</th>
                                <th>Academic Year</th>
                                <th>Gender</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentStudents as $s): ?>
                            <?php
                                $fullName = $s['first_name'];
                                if (!empty($s['middle_name'])) $fullName .= ' ' . $s['middle_name'];
                                $fullName .= ' ' . $s['last_name'];
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($s['registration_number']); ?></td>
                                <td><?php echo htmlspecialchars($fullName); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $s['level'] === 'Primary' ? 'success' : 'info'; ?>">
                                        <?php echo htmlspecialchars($s['level']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($s['academic_year']); ?></td>
                                <td><?php echo htmlspecialchars($s['gender']); ?></td>
                                <td>
                                    <a href="search.php?reg=<?php echo urlencode($s['registration_number']); ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card mt-4">
    <div class="card-body text-center py-5">
        <i class="bi bi-mortarboard fs-1 text-muted"></i>
        <h4>Welcome to The Green Gate Academy SMS</h4>
        <p class="text-muted">Manage student records efficiently.</p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
