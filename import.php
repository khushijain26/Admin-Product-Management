<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

session_start();
include 'config.php';
include 'encryption.php';

if (!isset($_SESSION['admin'])) die("Unauthorized");

if ($_FILES['excel']['tmp_name']) {
    $spreadsheet = IOFactory::load($_FILES['excel']['tmp_name']);
    $rows = $spreadsheet->getActiveSheet()->toArray();

    $added = $updated = $errors = 0;
    $duplicates = [];
    $_SESSION['updated_ids'] = [];

    foreach ($rows as $i => $r) {
        if ($i === 0) continue; // Skip header row

        // Safely extract and trim each field
        $name = trim($r[0] ?? '');
        $cat = trim($r[1] ?? '');
        $desc = trim($r[2] ?? '');
        $serial = trim($r[3] ?? '');

        // If all fields are blank, count as error and skip
        if (!$name && !$cat && !$desc && !$serial) {
            $errors++;
            continue;
        }

        // If serial is blank, also count as error
        if (!$serial) {
            $errors++;
            continue;
        }

        // Check if serialno already exists
        $stmt = $conn->prepare("SELECT id FROM products WHERE serialno = ?");
        $stmt->bind_param("s", $serial);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id); $stmt->fetch();
            $_SESSION['updated_ids'][] = $id;

            $stmt = $conn->prepare("UPDATE products SET productname = ?, product_category = ?, product_desc = ? WHERE serialno = ?");
            $stmt->bind_param("ssss", $name, $cat, $desc, $serial);
            $updated++;
            $duplicates[] = $serial;
        } else {
            $stmt = $conn->prepare("INSERT INTO products (productname, product_category, product_desc, serialno) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $cat, $desc, $serial);
            $added++;
        }

        $stmt->execute();
    }

    // Log duplicate serial numbers
    if (!empty($duplicates)) {
        $log = fopen("import_duplicates_log.txt", "a");
        foreach ($duplicates as $dup) {
            fwrite($log, "Duplicate serialno updated: $dup\n");
        }
        fclose($log);
    }

    $_SESSION['message'] = "$added added, $updated updated, $errors errors.";
    header("Location: dashboard.php");
    exit;
}
?>
