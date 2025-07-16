<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';
$conn = getDBConnection();

$message = "";
$message_type = "";

// Handle image upload
if ($_POST && isset($_POST['upload_image'])) {
    $target_dir = "uploads/";
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $message = "File is not an image.";
            $message_type = "danger";
        } else {
            // Check file size (limit to 5MB)
            if ($_FILES["image"]["size"] > 5000000) {
                $message = "Sorry, your file is too large. Maximum size is 5MB.";
                $message_type = "danger";
            } else {
                // Allow certain file formats
                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    $message_type = "danger";
                } else {
                    // Generate unique filename
                    $new_filename = "user_" . $_SESSION['user_id'] . "_" . time() . "." . $imageFileType;
                    $target_file = $target_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        // Update database with new image path
                        $user_id = $_SESSION['user_id'];
                        $update_sql = "UPDATE users SET image = '$target_file' WHERE id = $user_id";
                        
                        if ($conn->query($update_sql)) {
                            $_SESSION['image'] = $target_file;
                            $message = "Image uploaded successfully!";
                            $message_type = "success";
                        } else {
                            $message = "Error updating database: " . $conn->error;
                            $message_type = "danger";
                        }
                    } else {
                        $message = "Sorry, there was an error uploading your file.";
                        $message_type = "danger";
                    }
                }
            }
        }
    } else {
        $message = "Please select an image file to upload.";
        $message_type = "warning";
    }
}

// Handle profile update
if ($_POST && isset($_POST['update_profile'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $user_id = $_SESSION['user_id'];
    
    // Verify current password if changing password
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $message = "Please enter your current password to change it.";
            $message_type = "danger";
        } else {
            // Check current password
            $check_pass = "SELECT password FROM users WHERE id = $user_id";
            $result = $conn->query($check_pass);
            $user_data = $result->fetch_assoc();
            
            if (md5($current_password) != $user_data['password']) {
                $message = "Current password is incorrect.";
                $message_type = "danger";
            } else if ($new_password != $confirm_password) {
                $message = "New passwords do not match.";
                $message_type = "danger";
            } else {
                // Update email and password
                $new_password_hash = md5($new_password);
                $update_sql = "UPDATE users SET email = '$email', password = '$new_password_hash' WHERE id = $user_id";
                
                if ($conn->query($update_sql)) {
                    $_SESSION['email'] = $email;
                    $message = "Profile updated successfully!";
                    $message_type = "success";
                } else {
                    $message = "Error updating profile: " . $conn->error;
                    $message_type = "danger";
                }
            }
        }
    } else {
        // Update only email
        $update_sql = "UPDATE users SET email = '$email' WHERE id = $user_id";
        
        if ($conn->query($update_sql)) {
            $_SESSION['email'] = $email;
            $message = "Email updated successfully!";
            $message_type = "success";
        } else {
            $message = "Error updating email: " . $conn->error;
            $message_type = "danger";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .dashboard-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px 0;
        }
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .profile-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
        }
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            border-color: #28a745;
            background-color: #e8f5e8;
        }
        .upload-area.dragover {
            border-color: #28a745;
            background-color: #e8f5e8;
        }
        .file-input {
            display: none;
        }
        .user-card {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2><i class="fas fa-user"></i> User Dashboard</h2>
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
        <!-- Messages -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
                <i class="fas fa-<?php echo ($message_type == 'success') ? 'check-circle' : (($message_type == 'danger') ? 'exclamation-circle' : 'exclamation-triangle'); ?>"></i>
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Profile Card -->
            <div class="col-md-4">
                <div class="card user-card">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <?php if (!empty($_SESSION['image']) && file_exists($_SESSION['image'])): ?>
                                <img src="<?php echo htmlspecialchars($_SESSION['image']); ?>" 
                                     alt="Profile Picture" class="profile-image rounded-circle">
                            <?php else: ?>
                                <div class="profile-image rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto">
                                    <i class="fas fa-user fa-4x text-secondary"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h4><?php echo htmlspecialchars($_SESSION['username']); ?></h4>
                        <p class="mb-1"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
                        <span class="badge bg-primary">USER</span>
                    </div>
                </div>
            </div>

            <!-- Upload Image Card -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-upload"></i> Upload Profile Image</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="upload-area" onclick="document.getElementById('imageInput').click()">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h5>Click to Upload Image</h5>
                                <p class="text-muted">Or drag and drop your image here</p>
                                <small class="text-muted">Supported formats: JPG, JPEG, PNG, GIF (Max: 5MB)</small>
                            </div>
                            <input type="file" id="imageInput" name="image" accept="image/*" class="file-input" onchange="showFileName()">
                            <div id="fileName" class="mt-2"></div>
                            <div class="text-center mt-3">
                                <button type="submit" name="upload_image" class="btn btn-primary">
                                    <i class="fas fa-upload"></i> Upload Image
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Update Form -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-user-edit"></i> Update Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" 
                                               value="<?php echo htmlspecialchars($_SESSION['username']); ?>" disabled>
                                        <small class="text-muted">Username cannot be changed</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($_SESSION['email']); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            <h6 class="text-muted">Change Password (Optional)</h6>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" name="update_profile" class="btn btn-success">
                                    <i class="fas fa-save"></i> Update Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showFileName() {
            const input = document.getElementById('imageInput');
            const fileName = document.getElementById('fileName');
            
            if (input.files.length > 0) {
                const file = input.files[0];
                fileName.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-file-image"></i> Selected: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)
                    </div>
                `;
            }
        }

        // Drag and drop functionality
        const uploadArea = document.querySelector('.upload-area');
        const fileInput = document.getElementById('imageInput');

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                showFileName();
            }
        });
    </script>
</body>
</html>
