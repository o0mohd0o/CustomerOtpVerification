<?php
namespace ZainTawseel\CustomerOtpVerification\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;

class AccountCreatePostPlugin
{
    protected $customerSession;
    protected $customerRepository;
    protected $messageManager;

    public function __construct(
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        ManagerInterface $messageManager
    ) {
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->messageManager = $messageManager;
    }

    public function beforeExecute(\Magento\Customer\Controller\Account\CreatePost $subject)
    {
        // Use getData to retrieve your custom session value
        $customerId = $this->customerSession->getData('customer_id_awaiting_verification');
        if ($customerId) {
            try {
                $customer = $this->customerRepository->getById($customerId);
                $isVerified = $customer->getCustomAttribute('phone_verified')->getValue();
                if (!$isVerified) {
                    throw new LocalizedException(__('Your phone number is not verified.'));
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('There was an issue with your phone verification. Please try again.'));
                throw new LocalizedException(__('Your phone number is not verified.'));
            }
        } else {
            throw new LocalizedException(__('Your phone number is not verified.'));
        }
    }
}
