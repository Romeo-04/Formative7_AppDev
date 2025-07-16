<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['userlevel'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
        }
        .card {
            transition: transform 0.2s;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .admin-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .user-info {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2><i class="fas fa-user-shield"></i> Admin Dashboard</h2>
                    <p class="mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="logout.php" class="btn btn-light">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <!-- User Info Card -->
        <div class="user-info">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <?php if (!empty($_SESSION['image'])): ?>
                        <img src="<?php echo htmlspecialchars($_SESSION['image']); ?>" 
                             alt="Profile" class="img-fluid rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-2x text-white"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-10">
                    <h4><?php echo htmlspecialchars($_SESSION['username']); ?></h4>
                    <p class="text-muted mb-1"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
                    <span class="badge bg-danger">ADMIN</span>
                </div>
            </div>
        </div>

        <!-- Admin Functions -->
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card admin-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <h5 class="card-title">View All Users</h5>
                        <p class="card-text">View and manage all registered users in the system</p>
                        <a href="viewusers.php" class="btn btn-light">
                            <i class="fas fa-eye"></i> View Records
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-user-plus fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Add New User</h5>
                        <p class="card-text">Add new users to the system with specified roles</p>
                        <a href="admin_add.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add User
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-bar fa-3x mb-3 text-success"></i>
                        <h5 class="card-title">System Statistics</h5>
                        <p class="card-text">View system statistics and user analytics</p>
                        <a href="#" class="btn btn-success" onclick="showStats()">
                            <i class="fas fa-chart-line"></i> View Stats
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-tachometer-alt"></i> Quick Statistics</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        include 'config.php';
                        $conn = getDBConnection();
                        
                        // Get user statistics
                        $total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
                        $active_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE status = 'active'")->fetch_assoc()['count'];
                        $admin_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE userlevel = 'admin'")->fetch_assoc()['count'];
                        $pending_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE status = 'pending'")->fetch_assoc()['count'];
                        
                        $conn->close();
                        ?>
                        
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="bg-primary text-white p-3 rounded">
                                    <h3><?php echo $total_users; ?></h3>
                                    <p class="mb-0">Total Users</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-success text-white p-3 rounded">
                                    <h3><?php echo $active_users; ?></h3>
                                    <p class="mb-0">Active Users</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-danger text-white p-3 rounded">
                                    <h3><?php echo $admin_users; ?></h3>
                                    <p class="mb-0">Admin Users</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-warning text-white p-3 rounded">
                                    <h3><?php echo $pending_users; ?></h3>
                                    <p class="mb-0">Pending Users</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showStats() {
            alert('System Statistics:\n\nTotal Users: <?php echo $total_users; ?>\nActive Users: <?php echo $active_users; ?>\nAdmin Users: <?php echo $admin_users; ?>\nPending Users: <?php echo $pending_users; ?>');
        }
    </script>
</body>
</html>
