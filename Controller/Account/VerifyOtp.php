<?php
namespace ZainTawseel\CustomerOtpVerification\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use ZainTawseel\CustomerOtpVerification\Model\OtpManager; // Assume this is your custom model for managing OTPs

class VerifyOtp extends Action
{
    protected $resultJsonFactory;
    protected $otpManager;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        OtpManager $otpManager,
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->otpManager = $otpManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $otp = $this->getRequest()->getParam('otp');
        $phoneNumber = $this->getRequest()->getParam('phone_number');

        try {
            $isVerified = $this->otpManager->verifyOtp($phoneNumber, $otp);
            if ($isVerified) {
                $result->setData(['success' => true, 'message' => __('Phone number verified successfully.')]);
            } else {
                throw new LocalizedException(__('OTP verification failed.'));
            }
        } catch (LocalizedException $e) {
            $result->setData(['success' => false, 'message' => $e->getMessage()]);
        }

        return $result;
    }
}
