<?php
// Database connection configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "user_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form submission
$message = "";
if ($_POST && isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']); // Hash password with MD5
    $userlevel = mysqli_real_escape_string($conn, $_POST['userlevel']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $image = mysqli_real_escape_string($conn, $_POST['image']);
    
    // Validate required fields
    if (empty($email) || empty($username) || empty($_POST['password'])) {
        $message = "<div class='alert alert-danger'>Please fill in all required fields.</div>";
    } else {
        // Check if email or username already exists
        $check_query = "SELECT id FROM users WHERE email = '$email' OR username = '$username'";
        $check_result = $conn->query($check_query);
        
        if ($check_result->num_rows > 0) {
            $message = "<div class='alert alert-danger'>Email or username already exists.</div>";
        } else {
            // Insert new user
            $sql = "INSERT INTO users (email, username, password, userlevel, status, image) 
                    VALUES ('$email', '$username', '$password', '$userlevel', '$status', '$image')";
            
            if ($conn->query($sql) === TRUE) {
                $message = "<div class='alert alert-success'>User added successfully!</div>";
                // Clear form fields after successful submission
                $_POST = array();
            } else {
                $message = "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
            }
        }
    }
}

// Fetch all users for display
$users_query = "SELECT * FROM users ORDER BY id DESC";
$users_result = $conn->query($users_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
        }
        .card {
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
        .badge {
            font-size: 0.75em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Add New User</h4>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">Username *</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="userlevel" class="form-label">User Level</label>
                                <select class="form-select" id="userlevel" name="userlevel">
                                    <option value="user" <?php echo (isset($_POST['userlevel']) && $_POST['userlevel'] == 'user') ? 'selected' : ''; ?>>User</option>
                                    <option value="admin" <?php echo (isset($_POST['userlevel']) && $_POST['userlevel'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" <?php echo (isset($_POST['status']) && $_POST['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo (isset($_POST['status']) && $_POST['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                    <option value="pending" <?php echo (isset($_POST['status']) && $_POST['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="image" class="form-label">Image Path</label>
                                <input type="text" class="form-control" id="image" name="image" 
                                       placeholder="e.g., images/user.jpg"
                                       value="<?php echo isset($_POST['image']) ? htmlspecialchars($_POST['image']) : ''; ?>">
                            </div>
                            
                            <button type="submit" name="submit" class="btn btn-primary">Add User</button>
                            <button type="reset" class="btn btn-secondary">Reset</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Current Users (<?php echo $users_result->num_rows; ?>)</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Level</th>
                                        <th>Status</th>
                                        <th>Image</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($users_result->num_rows > 0): ?>
                                        <?php while($row = $users_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $row['id']; ?></td>
                                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                                <td>
                                                    <span class="badge <?php echo ($row['userlevel'] == 'admin') ? 'bg-danger' : 'bg-info'; ?>">
                                                        <?php echo strtoupper($row['userlevel']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge <?php 
                                                        echo ($row['status'] == 'active') ? 'bg-success' : 
                                                             (($row['status'] == 'inactive') ? 'bg-secondary' : 'bg-warning'); 
                                                    ?>">
                                                        <?php echo strtoupper($row['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($row['image']): ?>
                                                        <small><?php echo htmlspecialchars($row['image']); ?></small>
                                                    <?php else: ?>
                                                        <em class="text-muted">No image</em>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No users found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Database Setup Instructions</h5>
                    </div>
                    <div class="card-body">
                        <p>To set up the database with sample data:</p>
                        <ol>
                            <li>Make sure XAMPP is running (Apache and MySQL)</li>
                            <li>Open phpMyAdmin (http://localhost/phpmyadmin)</li>
                            <li>Import or run the SQL script: <code>database_setup.sql</code></li>
                            <li>The database will be created with 10 sample user records</li>
                        </ol>
                        <div class="alert alert-warning">
                            <strong>Note:</strong> This example uses MD5 for password hashing for simplicity. 
                            In production, use more secure methods like password_hash() and password_verify().
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
