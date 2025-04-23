<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';        // Database connection
include 'encryption.php';    // Encrypt/decrypt functions

// Handle search and pagination input
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 5; // Number of products per page
$offset = ($page - 1) * $limit;

// Build SQL query for searching
$where = $search ? "WHERE productname LIKE '%$search%' OR product_category LIKE '%$search%'" : "";

// Get total count for pagination
$total = $conn->query("SELECT COUNT(*) as count FROM products $where")->fetch_assoc()['count'];
$pages = ceil($total / $limit);

// Fetch products for current page
$result = $conn->query("SELECT * FROM products $where LIMIT $limit OFFSET $offset");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard</title>
  <!-- Bootstrap CSS & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">

<!-- Navbar with links -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="#">Product Admin</a>
    <div class="collapse navbar-collapse justify-content-end">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="add_edit.php"><i class="bi bi-plus-circle"></i> Add Product</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">

  <!-- Display flash messages -->
  <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
  <?php endif; ?>

  <!-- Search bar -->
  <div class="mb-3 d-flex justify-content-between align-items-center">
    <form method="GET" class="d-flex w-50">
      <input name="search" class="form-control me-2" value="<?= htmlspecialchars($search) ?>" placeholder="Search by name/category" />
      <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
    </form>
  </div>

  <!-- Product list table -->
  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">Product List</h5>
    </div>
    <div class="card-body p-0">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th>Name</th>
            <th>Category</th>
            <th>Description</th>
            <th>Serial</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['productname'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['product_category'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['product_desc'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['serialno'] ?? '') ?></td>
            <td>
              <a href="add_edit.php?id=<?= encrypt($row['id']) ?>" class="btn btn-sm btn-primary"><i class="bi bi-pencil-square"></i></a>
              <a href="delete.php?id=<?= encrypt($row['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
        <?php endwhile ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pagination links -->
  <nav class="mt-3">
    <ul class="pagination">
      <?php for ($i = 1; $i <= $pages; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>

  <!-- Excel import form -->
  <div class="mt-4">
    <h5>Import Excel</h5>
    <form method="POST" enctype="multipart/form-data" action="import.php">
      <input type="file" name="excel" class="form-control mb-2" required />
      <button class="btn btn-warning"><i class="bi bi-upload"></i> Upload</button>
    </form>
  </div>
</div>

</body>
</html>
 