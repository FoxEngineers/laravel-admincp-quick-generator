<?php

namespace FoxEngineers\AdminCP\Support\QrCode;

use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\DriverInterface;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    private DriverInterface $driver;
    private ImageManager $magager;

    public function construct(?DriverInterface $driver = null): void
    {
        if (!$driver instanceof DriverInterface) {
            $driver = resolve(\Intervention\Image\Drivers\Gd\Driver::class);
        }
        $this->driver = $driver;
        $this->magager = new ImageManager($this->driver);
    }

    public function generateQrCodeAtSpecificPosition(
        $qrCodeData,
        string $backgroundPath,
        string $saveTo,
        int $qrCodeSize = 300,
        string $position = 'top-left',
        int $qrCodeX = 150,
        int $qrCodeY = 150,
    ): string {
        // Generate QR code
        $qrCode = QrCode::size($qrCodeSize)->generate($qrCodeData);

        // Load background image.
        $background = $this->magager->read($backgroundPath);

        // Define the coordinates where you want to insert the QR code
        // Insert QR code at specific coordinates
        $background->place($qrCode, $position, $qrCodeX, $qrCodeY);

        // Save the final image
        $background->save($saveTo);

        return $saveTo;
    }
}
