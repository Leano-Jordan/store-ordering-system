<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';
require_once 'includes/helpers.php';
require_once 'includes/logger.php';
require_once 'includes/csrf.php';
require_once 'includes/audit.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: suppliers.php');
    exit();
}

verifyCsrfToken();

$companyNameRaw = $_POST['company_name'] ?? null;
$contactPersonRaw = $_POST['contact_person'] ?? null;
$phoneRaw = $_POST['phone'] ?? null;
$emailRaw = $_POST['email'] ?? null;
$addressRaw = $_POST['address'] ?? null;
$notesRaw = $_POST['notes'] ?? null;
$statusRaw = $_POST['status'] ?? null;

if (
    !is_string($companyNameRaw) ||
    !is_string($contactPersonRaw) ||
    !is_string($phoneRaw) ||
    !is_string($emailRaw) ||
    !is_string($addressRaw) ||
    !is_string($notesRaw) ||
    !is_string($statusRaw)
    ) {
    exit('Invalid supplier data.');
}

$companyName = trim($companyNameRaw);
$contactPerson = trim($contactPersonRaw);
$phone = trim($phoneRaw);
$email = trim($emailRaw);
$address = trim($addressRaw);
$notes = trim($notesRaw);
$status = trim($statusRaw);

$allowedStatuses = ['Active', 'Inactive'];
if (!in_array($status, $allowedStatuses, true)) {
    exit('Invalid supplier status.');
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit('Invalid email address.');
}

if ($companyName === '' || $contactPerson === '' || $phone === '') {
    exit('Please complete all required fields');
}

$sql = 'INSERT INTO suppliers(
company_name, contact_person, phone, email, address, notes, status) 
VALUES(?, ?, ?, ?, ?, ?, ?)';

try {
    $conn->begin_transaction();

    if (!executeStatement(
        $conn,
        $sql,
        'sssssss',
        [
            $companyName,
            $contactPerson,
            $phone,
            $email,
            $address,
            $notes,
            $status,
        ]
    )) {
        throw new RuntimeException('Unable to save supplier.');
    }

    $supplierId = (int) $conn->insert_id;

    if ($supplierId < 1) {
        throw new RuntimeException('Supplier creation returned an invalid ID.');
    }

    recordAudit(
        $conn,
        (int) $_SESSION['user_id'],
        'supplier',
        $supplierId,
        'CREATE',
        [
            'company_name' => [null, $companyName],
            'status' => [null, $status],
        ]
    );

    if (!$conn->commit()) {
        throw new RuntimeException('Supplier creation commit failed: '.$conn->error);
    }

    header('Location: suppliers.php');
    exit();
} catch (Throwable $e) {
    $conn->rollback();

    error_log(
        'save_supplier.php: '.$e->getMessage()
    );

    $_SESSION['error'] =
        'Unable to save supplier. Please try again.';

    header('Location: add_supplier.php');
    exit();
}
