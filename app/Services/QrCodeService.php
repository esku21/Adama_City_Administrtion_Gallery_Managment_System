namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    /**
     * Generate a QR Code for a booking token.
     */
    public function generate($token)
    {
        return QrCode::size(250)
            ->color(30, 58, 138) // Adama Blue
            ->margin(1)
            ->generate($token);
    }
}