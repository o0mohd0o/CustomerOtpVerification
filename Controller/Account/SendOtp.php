<?php
namespace ZainTawseel\CustomerOtpVerification\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use ZainTawseel\CustomerOtpVerification\Model\OtpManager; // Assume this is your custom model for managing OTPs

class SendOtp extends Action
{
    protected $resultJsonFactory;
    protected $otpManager;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        OtpManager $otpManager
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->otpManager = $otpManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $phoneNumber = $this->getRequest()->getParam('phone_number');

        try {
            $otp = $this->otpManager->generateAndSendOtp($phoneNumber);
            $result->setData(['success' => true, 'message' => __('OTP sent successfully.')]);
        } catch (LocalizedException $e) {
            $result->setData(['success' => false, 'message' => $e->getMessage()]);
        }

        return $result;
    }
}
