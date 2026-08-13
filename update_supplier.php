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

$companyName = trim($_POST['company_name']);
$contactPerson = trim($_POST['contact_person']);
$phone = trim($_POST['phone']);
$email = trim($_POST['email']);
$address = trim($_POST['address']);
$notes = trim($_POST['notes']);
$status = trim($_POST['status']);

$allowedStatuses = ['Active', 'Inactive'];
if (!in_array($status, $allowedStatuses, true)) {
    exit('Invalid supplier status.');
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit('Invalid email address.');
}

if ($companyName === '' || $contactPerson === '' || $phone === '') {
    header('Location: suppliers.php');
    exit();
}

$sql = 'UPDATE suppliers
SET company_name = ?,
contact_person = ?,
phone = ?,
email = ?,
address = ?,
notes = ?,
status = ? WHERE id = ?';

if (!executeStatement(
    $conn,
    $sql,
    'sssssssi',
    [$companyName, $contactPerson,
        $phone, $email, $address, $notes, $status, $id, ]
)) {
    exit('Unable to Update Supplier.');
}

logActivity($conn, $_SESSION['user_id'], 'Updated supplier: '.$companyName);
header('Location: suppliers.php');
exit();
