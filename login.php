<?php
// Start the session to track user login
session_start();

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Hardcoded credentials for demo/admin access
    if ($_POST['username'] === 'admin' && $_POST['password'] === 'admin123') {
        // Store login session
        $_SESSION['admin'] = true;
        $_SESSION['username'] = 'admin';
        $_SESSION['message'] = "Login successful.";
        // Redirect to dashboard after login
        header("Location: dashboard.php");
        exit;
    } else {
        // If login fails, set error message and redirect
        $_SESSION['error'] = "Invalid username or password.";
        header("Location: login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login</title>
  <!-- Bootstrap CSS and Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    /* Styling for the centered login card */
    body {
      background-color: #f8f9fa;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-card {
      width: 100%;
      max-width: 400px;
      border: 1px solid #dee2e6;
    }
  </style>
</head>
<body>
  <!-- Login card structure -->
  <div class="card login-card shadow-sm">
    <div class="card-header bg-white text-center">
      <h5 class="mb-0">Admin Login</h5>
    </div>
    <div class="card-body">
      <!-- Show error message if login fails -->
      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
          <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>

      <!-- Login Form -->
      <form method="POST" action="">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" placeholder="Enter username" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Enter password" required />
        </div>
        <button type="submit" class="btn btn-dark w-100">
          <i class="bi bi-box-arrow-in-right"></i> Login
        </button>
      </form>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
