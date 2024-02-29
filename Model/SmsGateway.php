<?php
namespace ZainTawseel\CustomerOtpVerification\Model;

use Magento\Framework\HTTP\Client\Curl;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class SmsGateway
{
    protected $apiUrl = 'https://www.kwtsms.com/API/send/'; // The URL for the SMS API
    protected $username; // API username
    protected $password; // API password
    protected $senderId; // Your private sender ID
    public function __construct(
        Curl $curl,
             $username, // Injected via di.xml
             $password, // Injected via di.xml
             $senderId  // Injected via di.xml
    ) {
        $this->curl = $curl;
        $this->username = $username;
        $this->password = $password;
        $this->senderId = $senderId;
        $this->logger = new Logger('kwtsms');
        $this->logger->pushHandler(new StreamHandler(BP . '/var/log/kwtsms.log', Logger::DEBUG));
    }

    public function sendSms($phoneNumber, $message)
    {
        $this->curl->setOption(CURLOPT_HEADER, 0);
        $this->curl->setOption(CURLOPT_TIMEOUT, 60);
        $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->curl->addHeader("Content-Type", "application/json");

        $payload = json_encode([
            "username" => $this->username,
            "password" => $this->password,
            "sender"   => $this->senderId,
            "mobile"   => $phoneNumber,
            "lang"     => "1", // Assuming English language for the message
            "message"  => $message,
            "test"     => "1" // Set to "1" for testing (no actual SMS sent)
        ]);

        $this->curl->post($this->apiUrl, $payload);
        $response = $this->curl->getBody();

        // Assuming a successful response contains "result":"OK"
        if (strpos($response, '"result":"OK"') !== false) {
            $this->logger->info('SMS sent successfully.', ['response' => $response]);
            return true;
        } else {
            $this->logger->error('Error sending SMS.', ['response' => $response]);
            return false;
        }
    }
}
