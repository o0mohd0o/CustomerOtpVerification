<?php
namespace ZainTawseel\CustomerOtpVerification\Model;

use Magento\Framework\Exception\LocalizedException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class OtpManager
{
    protected $smsGateway;
    protected $otpResource;

    public function __construct(
        // Inject dependencies for SMS sending and data storage
        \ZainTawseel\CustomerOtpVerification\Model\SmsGateway $smsGateway,
        \ZainTawseel\CustomerOtpVerification\Model\ResourceModel\Otp $otpResource
    ) {
        $this->smsGateway = $smsGateway;
        $this->otpResource = $otpResource;

        $this->logger = new Logger('kwtsms');
        $this->logger->pushHandler(new StreamHandler(BP . '/var/log/kwtsms.log', Logger::DEBUG));
    }

    public function generateOtp()
    {
        // Generate a 6-digit OTP
        return rand(100000, 999999);
    }

    public function generateAndSendOtp($phoneNumber)
    {
        $otp = $this->generateOtp();

        // Save OTP with expiration time (e.g., 5 minutes from now)
        $this->otpResource->saveOtp($phoneNumber, $otp, time() + 300);

        // Send OTP via SMS
//        $this->smsGateway->sendSms($phoneNumber, "Your OTP is: $otp");

        $this->logger->info('OTP: ' . $otp);

        return $otp; // Return OTP for testing, in production you might not want to return this
    }

    public function verifyOtp($phoneNumber, $inputOtp)
    {
        // Retrieve OTP data for the phone number
        $otpData = $this->otpResource->getOtpData($phoneNumber);

        if (!$otpData || $otpData['otp'] != $inputOtp) {
            throw new LocalizedException(__('The OTP is incorrect.'));
        }

        // Check OTP expiration
        if (time() > $otpData['expiration']) {
            throw new LocalizedException(__('The OTP has expired.'));
        }

        // Optionally, clear the OTP after successful verification
        $this->otpResource->clearOtp($phoneNumber);

        return true;
    }
}
