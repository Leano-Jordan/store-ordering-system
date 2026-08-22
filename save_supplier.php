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

if (executeStatement(
    $conn,
    $sql,
    'sssssss',
    [$companyName, $contactPerson, $phone, $email, $address, $notes, $status]
)) {
    logActivity($conn, $_SESSION['user_id'], 'Added supplier: '.$companyName);
    header('Location: suppliers.php');
    exit();
}

exit('Unable to Save Supplier.');
