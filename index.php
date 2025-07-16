<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h3>User Management System</h3>
                    </div>
                    <div class="card-body text-center">
                        <p class="card-text">Welcome to the User Management System. Use the links below to navigate:</p>
                        <a href="login.php" class="btn btn-success btn-lg mb-2">
                            <i class="fas fa-sign-in-alt"></i> Login to System
                        </a>
                        <br>
                        <a href="admin_add.php" class="btn btn-primary">Admin - Add User</a>
                        <br><br>
                        <small class="text-muted">Make sure to run the database_setup.sql file first to create the database and sample data.</small>
                        
                        <hr>
                        <div class="mt-3">
                            <h6>Test Accounts:</h6>
                            <small class="text-muted">
                                <strong>Admin:</strong> admin1 / admin123<br>
                                <strong>User:</strong> johndoe / password123
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
