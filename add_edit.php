<?php
session_start();
include 'config.php';
include 'encryption.php';

if (!isset($_SESSION['admin'])) die("Unauthorized");

// Decrypt product ID if provided (Edit mode)
$id = isset($_GET['id']) ? decrypt($_GET['id']) : null;

// Set default values
$product = ['productname' => '', 'product_category' => '', 'product_desc' => '', 'serialno' => ''];

// Load product data if editing
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = $_POST['productname'];
    $cat    = $_POST['product_category'];
    $desc   = $_POST['product_desc'];
    $serial = $_POST['serialno'];

    // Validate required fields
    if (!$name || !$cat || !$desc || !$serial) {
        $_SESSION['message'] = "All fields are required.";
        header("Location: add_edit.php" . ($id ? "?id=" . encrypt($id) : ""));
        exit;
    }

    // Check for duplicate serialno
    $stmt = $conn->prepare("SELECT id FROM products WHERE serialno = ?");
    $stmt->bind_param("s", $serial);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($existingId);
        $stmt->fetch();

        // Block if adding OR editing and it's a different record
        if (!$id || $existingId != $id) {
            $_SESSION['message'] = "Duplicate serial number.";
            header("Location: add_edit.php" . ($id ? "?id=" . encrypt($id) : ""));
            exit;
        }
    }

    // Update or Insert
    if ($id) {
        $stmt = $conn->prepare("UPDATE products SET productname = ?, product_category = ?, product_desc = ?, serialno = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $cat, $desc, $serial, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO products (productname, product_category, product_desc, serialno) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $cat, $desc, $serial);
    }

    $stmt->execute();
    $_SESSION['message'] = $id ? "Product updated successfully." : "Product added successfully.";
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title><?= $id ? 'Edit' : 'Add' ?> Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card shadow border-0">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><?= $id ? 'Edit' : 'Add New' ?> Product</h5>
          </div>
          <div class="card-body">
            <?php if (isset($_SESSION['message'])): ?>
              <div class="alert alert-info"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
            <?php endif; ?>
            <form method="POST">
              <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" name="productname" class="form-control" value="<?= htmlspecialchars($product['productname'] ?? '') ?>" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Category</label>
                <input type="text" name="product_category" class="form-control" value="<?= htmlspecialchars($product['product_category'] ?? '') ?>" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="product_desc" class="form-control" rows="3" required><?= htmlspecialchars($product['product_desc'] ?? '') ?></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Serial Number</label>
                <input type="text" name="serialno" class="form-control" value="<?= htmlspecialchars($product['serialno'] ?? '') ?>" required>
              </div>
              <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-success">
                  <i class="bi bi-check-circle"></i> Submit
                </button>
                <a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
