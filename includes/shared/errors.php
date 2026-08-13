<?php

function jsonSuccess(string $message, array $extra = []): void
{
    header('Content-Type: application/json');
    echo json_encode(
        array_merge(
            [
        'success' => true,
        'message' => $message, ],
            $extra
        )
    );
    exit;
}

function jsonError(string $message, int $status = 400, ?string $code = null): void
{
    http_response_code($status);

    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $message,
        'code' => $code,
    ]);
    exit;
}
