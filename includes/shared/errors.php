<?php

function encodeJsonResponse(array $payload): string
{
    return json_encode(
        $payload,
        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR
    );
}

function jsonSuccess(string $message, array $extra = []): void
{
    header('Content-Type: application/json');
    echo encodeJsonResponse(
        array_merge(
            [
                'success' => true,
                'message' => $message,
            ],
            $extra
        )
    );
    exit;
}

function jsonError(string $message, int $status = 400, ?string $code = null): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo encodeJsonResponse([
        'success' => false,
        'message' => $message,
        'code' => $code,
    ]);
    exit;
}
