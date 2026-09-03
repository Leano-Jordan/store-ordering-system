<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);
require_once 'includes/db.php';
require_once 'includes/helpers.php';
require_once 'includes/logger.php';
require_once 'includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: suppliers.php');
    exit();
}

verifyCsrfToken();

$id = (int) $_POST['id'];

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

$currentStatusStmt = $conn->prepare('SELECT status FROM suppliers WHERE id = ? LIMIT 1');

if (!$currentStatusStmt) {
    error_log(
        'update_supplier.php: Failed to prepare current supplier status lookup: '.$conn->error
    );

    exit('Unable to validate supplier.');
}

if (!$currentStatusStmt->bind_param('i', $id)) {
    $currentStatusStmt->close();

    exit('Unable to validate supplier.');
}

if (!$currentStatusStmt->execute()) {
    error_log(
        'update_supplier.php: Failed to execute current supplier status lookup: '.$currentStatusStmt->error
    );

    $currentStatusStmt->close();

    exit('Unable to validate supplier.');
}

$currentStatusResult = $currentStatusStmt->get_result();

$currentSupplier = $currentStatusResult
    ? $currentStatusResult->fetch_assoc() : null;

$currentStatusStmt->close();

if (!$currentSupplier) {
    exit('Supplier not found.');
}

$currentStatus = (string) $currentSupplier['status'];

if ($status !== $currentStatus && (string) $_SESSION['role'] !== ROLE_ADMIN) {
    exit('Only an administrator can change supplier status.');
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit('Invalid email address.');
}

if ($companyName === '' || $contactPerson === '' || $phone === '') {
    header('Location: suppliers.php');
    exit();
}

$sql = 'UPDATE suppliers 
SET company_name = ?, contact_person = ?,
phone = ?, email = ?,
address = ?, notes = ?,
status = ? WHERE id = ?';

if (!executeStatement(
    $conn,
    $sql,
    'sssssssi',
    [
        $companyName, $contactPerson,
        $phone, $email, $address, $notes, $status, $id,
        ]
)
        ) {
    exit('Unable to Update Supplier.');
}

logActivity($conn, $_SESSION['user_id'], 'Updated supplier: '.$companyName);
header('Location: suppliers.php');
exit();
