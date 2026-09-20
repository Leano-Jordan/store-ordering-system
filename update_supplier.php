<?php

declare(strict_types=1);

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);

require_once 'includes/db.php';
require_once 'includes/helpers.php';
require_once 'includes/csrf.php';
require_once 'includes/audit.php';
require_once __DIR__.'/includes/licensing/license_gate.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: suppliers.php');
    exit();
}

verifyCsrfToken();
requireActiveLicense($conn);

$idRaw = $_POST['id'] ?? null;

$companyNameRaw = $_POST['company_name'] ?? null;
$contactPersonRaw = $_POST['contact_person'] ?? null;
$phoneRaw = $_POST['phone'] ?? null;
$emailRaw = $_POST['email'] ?? null;
$addressRaw = $_POST['address'] ?? null;
$notesRaw = $_POST['notes'] ?? null;
$statusRaw = $_POST['status'] ?? null;

if (
    !is_string($idRaw) ||
    !is_string($companyNameRaw) ||
    !is_string($contactPersonRaw) ||
    !is_string($phoneRaw) ||
    !is_string($emailRaw) ||
    !is_string($addressRaw) ||
    !is_string($notesRaw) ||
    !is_string($statusRaw)
) {
    $_SESSION['error'] = 'Invalid supplier data.';
    header('Location: suppliers.php');
    exit();
}

$id = filter_var(
    $idRaw,
    FILTER_VALIDATE_INT,
    [
        'options' => [
            'min_range' => 1,
        ],
    ]
);

if ($id === false) {
    $_SESSION['error'] = 'Invalid supplier reference.';
    header('Location: suppliers.php');
    exit();
}

$companyName = trim($companyNameRaw);
$contactPerson = trim($contactPersonRaw);
$phone = trim($phoneRaw);
$email = trim($emailRaw);
$address = trim($addressRaw);
$notes = trim($notesRaw);
$status = trim($statusRaw);

$allowedStatuses = [
    'Active',
    'Inactive',
];

if (!in_array($status, $allowedStatuses, true)) {
    $_SESSION['error'] = 'Invalid supplier status.';
    header('Location: suppliers.php');
    exit();
}

if ($companyName === '' || $contactPerson === '' || $phone === '') {
    $_SESSION['error'] =
        'Please complete all required supplier fields.';

    header('Location: suppliers.php');
    exit();
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Invalid email address.';
    header('Location: suppliers.php');
    exit();
}

$transactionStarted = false;

try {
    if (!$conn->begin_transaction()) {
        throw new RuntimeException('Failed to begin supplier update transaction: '.$conn->error);
    }

    $transactionStarted = true;

    /*
     * Lock the supplier row so authorization and audit
     * decisions are based on the same database state that
     * will be updated.
     */
    $currentStmt = $conn->prepare(
        'SELECT
            company_name,
            contact_person,
            phone,
            email,
            address,
            notes,
            status
        FROM suppliers
        WHERE id = ?
        LIMIT 1
        FOR UPDATE'
    );

    if (!$currentStmt) {
        throw new RuntimeException('Unable to prepare supplier lookup.');
    }

    if (!$currentStmt->bind_param('i', $id)) {
        $currentStmt->close();

        throw new RuntimeException('Unable to bind supplier lookup.');
    }

    if (!$currentStmt->execute()) {
        $error = $currentStmt->error;

        $currentStmt->close();

        throw new RuntimeException('Unable to load supplier: '.$error);
    }

    $currentResult = $currentStmt->get_result();

    $currentSupplier = $currentResult
        ? $currentResult->fetch_assoc()
        : null;

    $currentStmt->close();

    if (!$currentSupplier) {
        throw new RuntimeException('Supplier not found.');
    }

    $currentStatus = (string)
        $currentSupplier['status'];

    /*
     * Only administrators may change supplier status.
     * This check is performed after locking the current row.
     */
    if (
        $status !== $currentStatus &&
        (string) $_SESSION['role'] !== ROLE_ADMIN
    ) {
        throw new InvalidArgumentException('Only an administrator can change supplier status.');
    }

    $sql = 'UPDATE suppliers
        SET company_name = ?,
            contact_person = ?,
            phone = ?,
            email = ?,
            address = ?,
            notes = ?,
            status = ?
        WHERE id = ?';

    if (!executeStatement(
        $conn,
        $sql,
        'sssssssi',
        [
            $companyName,
            $contactPerson,
            $phone,
            $email,
            $address,
            $notes,
            $status,
            $id,
        ]
    )) {
        throw new RuntimeException('Unable to update supplier.');
    }

    /*
     * Do not store phone numbers, email addresses or other
     * potentially identifying supplier contact information
     * in the audit record.
     */
    $changes = [];

    if (
        (string) $currentSupplier['company_name']
        !== $companyName
    ) {
        $changes['company_name'] = [
            '[changed]',
            '[changed]',
        ];
    }

    if (
        (string) $currentSupplier['contact_person']
        !== $contactPerson
    ) {
        $changes['contact_person'] = [
            '[changed]',
            '[changed]',
        ];
    }

    if (
        (string) $currentSupplier['phone']
        !== $phone
    ) {
        $changes['phone'] = [
            '[changed]',
            '[changed]',
        ];
    }

    if (
        (string) ($currentSupplier['email'] ?? '')
        !== $email
    ) {
        $changes['email'] = [
            '[changed]',
            '[changed]',
        ];
    }

    if (
        (string) ($currentSupplier['address'] ?? '')
        !== $address
    ) {
        $changes['address'] = [
            '[changed]',
            '[changed]',
        ];
    }

    if (
        (string) ($currentSupplier['notes'] ?? '')
        !== $notes
    ) {
        $changes['notes'] = [
            '[changed]',
            '[changed]',
        ];
    }

    if ($currentStatus !== $status) {
        $changes['status'] = [
            $currentStatus,
            $status,
        ];
    }

    if ($changes !== []) {
        recordAudit(
            $conn,
            (int) $_SESSION['user_id'],
            'supplier',
            $id,
            'UPDATE',
            $changes
        );
    }

    if (!$conn->commit()) {
        $error = $conn->error;

        $conn->rollback();

        throw new RuntimeException('Supplier update commit failed: '.$error);
    }

    $_SESSION['success'] = 'Supplier updated successfully.';

    header('Location: suppliers.php');
    exit();
} catch (Throwable $exception) {
    if ($transactionStarted) {
        $conn->rollback();
    }

    error_log('update_supplier.php: '.$exception->getMessage());

    $_SESSION['error'] = $exception instanceof InvalidArgumentException ? $exception->getMessage() : 'Unable to update supplier. Please try again.';

    header('Location: edit_supplier.php?id='.$id);
    exit();
}
