<?php

declare(strict_types=1);

require_once __DIR__.'/../../includes/user_update_helpers.php';

use PHPUnitFrameworkTestCase;

final class UserUpdateHelpersTest extends TestCase
{
    public function testNoProfileImageReturnsUnchangedState(): void
    {
        $result = prepareProfileImageUpload(null);

        $this->assertSame(
            [
                'uploaded' => false,
                'filename' => '',
                'path' => null,
            ],
            $result
        );
    }

    public function testNoFileUploadReturnsUnchangedState(): void
    {
        $result = prepareProfileImageUpload([
            'error' => UPLOAD_ERR_NO_FILE,
        ]);

        $this->assertFalse($result['uploaded']);
        $this->assertSame('', $result['filename']);
        $this->assertNull($result['path']);
    }

    public function testOversizedProfileImageIsRejected(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Profile image must not exceed 2MB.'
        );

        prepareProfileImageUpload([
            'error' => UPLOAD_ERR_OK,
            'size' => 2 * 1024 * 1024 + 1,
            'tmp_name' => '/tmp/not-used',
        ]);
    }

    public function testIncompleteUploadIsRejected(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Invalid profile image upload.'
        );

        prepareProfileImageUpload([
            'error' => UPLOAD_ERR_OK,
            'size' => 100,
        ]);
    }

    public function testNonImageUploadIsRejected(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'swiftorder-test-');

        if ($path === false) {
            $this->markTestSkipped('Unable to create temporary test file.');
        }

        file_put_contents($path, 'not an image');

        try {
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage(
                'Invalid profile image format.'
            );

            prepareProfileImageUpload([
                'error' => UPLOAD_ERR_OK,
                'size' => filesize($path),
                'tmp_name' => $path,
            ]);
        } finally {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }
}
