<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['userlevel'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'config.php';
$conn = getDBConnection();

// Handle user deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $delete_sql = "DELETE FROM users WHERE id = $delete_id";
    
    if ($conn->query($delete_sql)) {
        $success_message = "User deleted successfully!";
    } else {
        $error_message = "Error deleting user: " . $conn->error;
    }
}

// Handle status update
if (isset($_POST['update_status']) && isset($_POST['user_id']) && isset($_POST['new_status'])) {
    $user_id = (int)$_POST['user_id'];
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    
    $update_sql = "UPDATE users SET status = '$new_status' WHERE id = $user_id";
    
    if ($conn->query($update_sql)) {
        $success_message = "User status updated successfully!";
    } else {
        $error_message = "Error updating status: " . $conn->error;
    }
}

// Get all users
$users_query = "SELECT * FROM users ORDER BY id ASC";
$users_result = $conn->query($users_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Users - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
        }
        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            object-fit: cover;
        }
        .action-buttons .btn {
            margin: 2px;
            padding: 5px 10px;
            font-size: 12px;
        }
        .status-active { background-color: #28a745; }
        .status-inactive { background-color: #6c757d; }
        .status-pending { background-color: #ffc107; color: #000; }
        .level-admin { background-color: #dc3545; }
        .level-user { background-color: #007bff; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2><i class="fas fa-users"></i> All User Records</h2>
                    <p class="mb-0">Manage and view all registered users</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="admin.php" class="btn btn-light me-2">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <a href="logout.php" class="btn btn-outline-light">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <!-- Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- User Table -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>User Level</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($users_result->num_rows > 0): ?>
                            <?php while($user = $users_result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo $user['id']; ?></strong></td>
                                    <td>
                                        <?php if ($user['image'] && file_exists($user['image'])): ?>
                                            <img src="<?php echo htmlspecialchars($user['image']); ?>" 
                                                 alt="User Image" class="user-avatar rounded-circle">
                                        <?php else: ?>
                                            <div class="user-avatar rounded-circle bg-secondary d-flex align-items-center justify-content-center">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge level-<?php echo $user['userlevel']; ?>">
                                            <?php echo strtoupper($user['userlevel']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <select name="new_status" class="form-select form-select-sm status-<?php echo $user['status']; ?>" 
                                                    onchange="this.form.submit()" style="width: auto; display: inline-block;">
                                                <option value="active" <?php echo ($user['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                                <option value="inactive" <?php echo ($user['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                                <option value="pending" <?php echo ($user['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                    </td>
                                    <td>
                                        <small><?php echo date('M j, Y', strtotime($user['created_at'])); ?></small>
                                    </td>
                                    <td class="action-buttons">
                                        <button class="btn btn-info btn-sm" onclick="viewUser(<?php echo $user['id']; ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-warning btn-sm" onclick="editUser(<?php echo $user['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <button class="btn btn-danger btn-sm" onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No users found in the database.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <h4 class="text-primary"><?php echo $users_result->num_rows; ?></h4>
                                <p class="text-muted">Total Users</p>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-success">
                                    <?php 
                                    $users_result->data_seek(0);
                                    $active_count = 0;
                                    while($row = $users_result->fetch_assoc()) {
                                        if($row['status'] == 'active') $active_count++;
                                    }
                                    echo $active_count;
                                    ?>
                                </h4>
                                <p class="text-muted">Active Users</p>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-danger">
                                    <?php 
                                    $users_result->data_seek(0);
                                    $admin_count = 0;
                                    while($row = $users_result->fetch_assoc()) {
                                        if($row['userlevel'] == 'admin') $admin_count++;
                                    }
                                    echo $admin_count;
                                    ?>
                                </h4>
                                <p class="text-muted">Administrators</p>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-warning">
                                    <?php 
                                    $users_result->data_seek(0);
                                    $pending_count = 0;
                                    while($row = $users_result->fetch_assoc()) {
                                        if($row['status'] == 'pending') $pending_count++;
                                    }
                                    echo $pending_count;
                                    ?>
                                </h4>
                                <p class="text-muted">Pending Approval</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewUser(userId) {
            alert('View user details for ID: ' + userId + '\n\nThis would open a detailed view in a real application.');
        }

        function editUser(userId) {
            alert('Edit user with ID: ' + userId + '\n\nThis would redirect to an edit form in a real application.');
        }

        function deleteUser(userId, username) {
            if (confirm('Are you sure you want to delete user "' + username + '"?\n\nThis action cannot be undone.')) {
                window.location.href = '?delete=' + userId;
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
