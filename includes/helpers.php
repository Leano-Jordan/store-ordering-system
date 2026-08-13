<?php

function formatCurrency($amount)
{
    return 'R'.number_format((float) $amount, 2);
}

function formatDate($date): string
{
    return date('d M Y H:i', strtotime($date));
}

function executeQuery(mysqli $conn, string $sql, string $types = '', array $params = [])
{
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log('SwiftOrder executeQuery prepare failed: '.$conn->error);

        return false;
    }

    if ($types !== '' && !empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        $stmt->close();

        return false;
    }

    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

function executeStatement(mysqli $conn, string $sql, string $types = '', array $params = [])
{
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log('SwiftOrder executeStatement prepare failed: '.$conn->error);

        return false;
    }

    try {
        if ($types !== '' && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            return false;
        }
    } finally {
        $stmt->close();
    }

    return true;
}

function executeStatementAffectedRows(mysqli $conn, string $sql, string $types = '', array $params = []): int
{
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log('SwiftOrder executeStatementAffectedRows prepare failed: '.$conn->error);

        return -1;
    }

    try {
        if ($types !== '' && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            return -1;
        }

        return $stmt->affected_rows;
    } finally {
        $stmt->close();
    }
}
