<?php

declare(strict_types=1);

/**
 * Records an audit event and its changed fields.
 *
 * This function must be called inside the caller's existing transaction.
 */
function recordAudit(
    mysqli $conn,
    int $userId,
    string $entity,
    int $entityId,
    string $action,
    array $changes
): void {
    if ($userId < 1 || $entityId < 1) {
        throw new InvalidArgumentException('Invalid audit actor or entity ID.');
    }

    if ($entity === '' || $action === '') {
        throw new InvalidArgumentException('Audit entity and action are required.');
    }

    $auditStmt = $conn->prepare(
        'INSERT INTO audit_log (
            user_id,
            entity,
            entity_id,
            action
        ) VALUES (?, ?, ?, ?)'
    );

    if (!$auditStmt) {
        error_log(
            'SwiftOrder audit prepare failed: '.$conn->error
        );

        throw new RuntimeException('Unable to create audit record.');
    }

    if (!$auditStmt->bind_param(
        'isis',
        $userId,
        $entity,
        $entityId,
        $action
    )) {
        error_log(
            'SwiftOrder audit bind failed: '.$auditStmt->error
        );

        $auditStmt->close();

        throw new RuntimeException('Unable to create audit record.');
    }

    if (!$auditStmt->execute()) {
        error_log(
            'SwiftOrder audit execute failed: '.$auditStmt->error
        );

        $auditStmt->close();

        throw new RuntimeException('Unable to create audit record.');
    }

    $auditId = $auditStmt->insert_id;
    $auditStmt->close();

    if ($auditId < 1) {
        error_log(
            'SwiftOrder audit returned invalid audit ID.'
        );

        throw new RuntimeException('Unable to create audit record.');
    }

    if ($changes === []) {
        return;
    }

    $changeStmt = $conn->prepare(
        'INSERT INTO audit_changes (
            audit_id,
            field_name,
            old_value,
            new_value
        ) VALUES (?, ?, ?, ?)'
    );

    if (!$changeStmt) {
        error_log(
            'SwiftOrder audit change prepare failed: '.$conn->error
        );

        throw new RuntimeException('Unable to create audit changes.');
    }

    foreach ($changes as $fieldName => $change) {
        if (
            !is_string($fieldName)
            || !is_array($change)
            || count($change) !== 2
        ) {
            $changeStmt->close();

            throw new InvalidArgumentException('Invalid audit change structure.');
        }

        $oldValue = $change[0];
        $newValue = $change[1];

        $oldText = $oldValue === null
            ? null
            : (string) $oldValue;

        $newText = $newValue === null
            ? null
            : (string) $newValue;

        if (!$changeStmt->bind_param(
            'isss',
            $auditId,
            $fieldName,
            $oldText,
            $newText
        )) {
            error_log(
                'SwiftOrder audit change bind failed: '
                .$changeStmt->error
            );

            $changeStmt->close();

            throw new RuntimeException('Unable to create audit changes.');
        }

        if (!$changeStmt->execute()) {
            error_log(
                'SwiftOrder audit change execute failed: '
                .$changeStmt->error
            );

            $changeStmt->close();

            throw new RuntimeException('Unable to create audit changes.');
        }
    }

    $changeStmt->close();
}
