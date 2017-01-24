<?php
// src/SyncBundle/Tests/Controller/AuthenticationControllerTest.php
namespace SyncBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Goutte\Client;

use SyncBundle\Service\Security\Authorization,
    SyncBundle\Service\BankingMachine\Sync\Utility\ChecksumCalculator;

use SyncBundle\Tests\SyncData\Authentication\CheckinBankingMachine,
    SyncBundle\Tests\SyncData\BankingMachine\Operator;

class AuthenticationControllerTest extends WebTestCase
{
    const CUSTOM_BROWSER_KIT_HEADER_PREFIX = 'HTTP';

    const URL_CHECKIN_BANKING_MACHINES = (
        'http://api-v_1.fintech.dev/app_dev.php/authentication'
    );

    const URL_SYNC_BANKING_MACHINES = (
        'http://api-v_1.fintech.dev/app_dev.php/banking_machines'
    );

    protected static $kernel;
    protected static $container;

    public static function getChecksumCalculator()
    {
        return new ChecksumCalculator();
    }

    private $bankingMachineCredentials = [
        'serial'   => 'SM-0001',
        'login'    => 'xxx-login',
        'password' => 'xxx-password'
    ];

    private function getBankingMachineSerial()
    {
        return $this->bankingMachineCredentials['serial'];
    }

    private function getBankingMachineLogin()
    {
        return $this->bankingMachineCredentials['login'];
    }

    private function getBankingMachinePassword()
    {
        return $this->bankingMachineCredentials['password'];
    }

    private function getCheckinBankingMachinesConnectionUrl($connectionPath)
    {
        return self::URL_CHECKIN_BANKING_MACHINES . '/' . $connectionPath;
    }

    private function getSyncBankingMachinesConnectionUrl($connectionPath)
    {
        return self::URL_SYNC_BANKING_MACHINES . '/' . $connectionPath;
    }

    private function getAuthenticationClientResponse()
    {
        $connectionPath = sprintf(
            "%s/%s",
            CheckinBankingMachine::getSyncAction(),
            $this->getBankingMachineSerial()
        );

        $connectionURL = $this->getCheckinBankingMachinesConnectionUrl(
            $connectionPath
        );

        $data = CheckinBankingMachine::getData([
            'login'    => $this->getBankingMachineLogin(),
            'password' => $this->getBankingMachinePassword(),
        ]);

        $client = new Client();
        $client->request(
            CheckinBankingMachine::getSyncMethod(),
            $connectionURL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $data
        );

        return $client->getResponse();
    }

    public function getSyncClientResponse($token)
    {
        $connectionPath = sprintf(
            "%s/%s",
            $this->getBankingMachineSerial(),
            Operator::getSyncAction()
        );

        $connectionURL = $this->getSyncBankingMachinesConnectionUrl(
            $connectionPath
        );

        $authorizationHeaderName = (
            self::CUSTOM_BROWSER_KIT_HEADER_PREFIX . "-" . Authorization::TOKEN_HEADER
        );

        $client = new Client();
        $client->request(
            Operator::getSyncMethod(),
            $connectionURL,
            [],
            [],
            [
                'Content-Type'           => 'application/json',
                $authorizationHeaderName => $token,
            ]
        );

        return $client->getResponse();
    }

    public function testCheckinBankingMachines()
    {
        $response = $this->getAuthenticationClientResponse();

        // Response returned OK

        $this->assertEquals(200, $response->getStatus());

        // Response array has required fields

        $responseContent = json_decode($response->getContent(), TRUE);

        $this->assertArrayHasKey('checksum', $responseContent);
        $this->assertArrayHasKey('data', $responseContent);

        // Response checksum valid

        $this->assertEquals(TRUE, $this->getChecksumCalculator()->verifyDataChecksum(
            $responseContent['checksum'], $responseContent['data']
        ));

        // Response data contains token

        $this->assertArrayHasKey('token', $responseContent['data']);

        // Next sync request has valid token

        $response = $this->getSyncClientResponse($responseContent['data']['token']);

        $this->assertEquals(200, $response->getStatus());
    }
}
