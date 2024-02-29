<?php
namespace ZainTawseel\CustomerOtpVerification\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Otp extends AbstractDb
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('customer_otp_verification', 'phone_number'); // Initialize with your table name and primary key
    }

    public function saveOtp($phoneNumber, $otp, $expiration)
    {
        $connection = $this->getConnection();
        $data = [
            'phone_number' => $phoneNumber,
            'otp' => $otp,
            'expiration' => $expiration,
        ];

        $connection->insertOnDuplicate($this->getMainTable(), $data, ['otp', 'expiration']);
    }

    public function getOtpData($phoneNumber)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable())
            ->where('phone_number = ?', $phoneNumber);

        return $connection->fetchRow($select);
    }

    public function clearOtp($phoneNumber)
    {
        $connection = $this->getConnection();
        $connection->delete($this->getMainTable(), ['phone_number = ?' => $phoneNumber]);
    }
}
