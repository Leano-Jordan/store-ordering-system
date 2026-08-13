<?php

function validateImageDimensions(
    string $tmpFile,
    int $maxWidth = 2000,
    int $maxHeight = 2000
): bool
{
    $imageInfo = getimagesize($tmpFile);

    if ($imageInfo === false) {
        return false;
    }

    return $imageInfo[0] <= $maxWidth && $imageInfo[1] <= $maxHeight;
}
