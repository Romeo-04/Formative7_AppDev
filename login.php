<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - User Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-card">
                    <div class="login-header text-center py-4">
                        <h3 class="mb-0">User Login</h3>
                        <p class="mb-0">Enter your credentials</p>
                    </div>
                    <div class="card-body p-4">
                        <?php
                        session_start();
                        
                        // Include database configuration
                        include 'config.php';
                        $conn = getDBConnection();
                        
                        $error_message = "";
                        
                        if ($_POST && isset($_POST['login'])) {
                            $username = mysqli_real_escape_string($conn, $_POST['username']);
                            $password = md5($_POST['password']);
                            
                            if (empty($username) || empty($_POST['password'])) {
                                $error_message = "Please fill in all fields.";
                            } else {
                                // Check user credentials
                                $sql = "SELECT * FROM users WHERE (username = '$username' OR email = '$username') AND password = '$password' AND status = 'active'";
                                $result = $conn->query($sql);
                                
                                if ($result->num_rows == 1) {
                                    $user = $result->fetch_assoc();
                                    
                                    // Set session variables
                                    $_SESSION['user_id'] = $user['id'];
                                    $_SESSION['username'] = $user['username'];
                                    $_SESSION['email'] = $user['email'];
                                    $_SESSION['userlevel'] = $user['userlevel'];
                                    $_SESSION['image'] = $user['image'];
                                    
                                    // Redirect based on user level
                                    if ($user['userlevel'] == 'admin') {
                                        header("Location: admin.php");
                                    } else {
                                        header("Location: user.php");
                                    }
                                    exit();
                                } else {
                                    $error_message = "Invalid credentials or account not active.";
                                }
                            }
                        }
                        
                        $conn->close();
                        ?>
                        
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username or Email</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" name="login" class="btn btn-primary btn-lg">Login</button>
                            </div>
                        </form>
                        
                        <hr>
                        <div class="text-center">
                            <small class="text-muted">
                                <a href="index.php" class="text-decoration-none">← Back to Home</a>
                            </small>
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <strong>Test Accounts:</strong><br>
                                Admin: admin1 / admin123<br>
                                User: johndoe / password123
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
