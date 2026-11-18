<?php

namespace App\Services;

use App\Models\Certificate;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode as EndroidQrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\SvgWriter;

class CertificateQrCode
{
    public function dataUri(Certificate $certificate): string
    {
        $code = new EndroidQrCode(
            data: route('certificates.verify', $certificate->verification_code),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 240,
            margin: 8,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        );

        return (new SvgWriter)->write($code)->getDataUri();
    }
}
