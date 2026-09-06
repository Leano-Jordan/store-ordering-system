<?php

function formatCurrency($amount)
{
    return 'R'.number_format((float) $amount, 2);
}

function formatDate($date): string
{
    return date('d M Y H:i', strtotime($date));
}

function executeQuery(
    mysqli $conn,
    string $sql,
    string $types = '',
    array $params = []
) {
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log('SwiftOrder executeQuery prepare failed: '.$conn->error);

        return false;
    }

    try {
        if (($types === '') !== empty($params)) {
            error_log('SwiftOrder executeQuery parameter contract mismatch.');

            return false;
        }

        if ($types !== '' && !$stmt->bind_param($types, ...$params)) {
            error_log('SwiftOrder executeQuery bind failed: '.$stmt->error);

            return false;
        }

        if (!$stmt->execute()) {
            error_log('SwiftOrder executeQuery execute failed: '.$stmt->error);

            return false;
        }

        $result = $stmt->get_result();

        if (!$result) {
            error_log('SwiftOrder executeQuery result retrieval failed: '.$stmt->error);

            return false;
        }

        return $result;
    } finally {
        $stmt->close();
    }
}

function executeStatement(
    mysqli $conn,
    string $sql,
    string $types = '',
    array $params = []
): bool {
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log('SwiftOrder executeStatement prepare failed: '.$conn->error);

        return false;
    }

    try {
        if ($types !== '' && !empty($params) && !$stmt->bind_param($types, ...$params)) {
            error_log('SwiftOrder executeStatement bind failed: '.$stmt->error);

            return false;
        }

        if (!$stmt->execute()) {
            error_log('SwiftOrder executeStatement execute failed: '.$stmt->error);

            return false;
        }

        return true;
    } finally {
        $stmt->close();
    }
}

function executeStatementAffectedRows(
    mysqli $conn,
    string $sql,
    string $types = '',
    array $params = []
): int {
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log('SwiftOrder executeStatementAffectedRows prepare failed: '.$conn->error);

        return -1;
    }

    try {
        if ($types !== '' && !empty($params) && !$stmt->bind_param($types, ...$params)) {
            error_log('SwiftOrder executeStatementAffectedRows bind failed: '.$stmt->error);

            return -1;
        }

        if (!$stmt->execute()) {
            error_log('SwiftOrder executeStatementAffectedRows execute failed: '.$stmt->error);

            return -1;
        }

        return $stmt->affected_rows;
    } finally {
        $stmt->close();
    }
}
