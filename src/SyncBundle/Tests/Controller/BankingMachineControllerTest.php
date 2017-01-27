<?php
// src/SyncBundle/Tests/Controller/BankingMachineControllerTest.php
namespace SyncBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Goutte\Client;

use SyncBundle\Service\Security\Authorization,
    SyncBundle\Service\BankingMachine\Sync\Utility\ChecksumCalculator;

use SyncBundle\Tests\SyncData\Authentication\CheckinBankingMachine,
    SyncBundle\Tests\SyncData\BankingMachine\BankingMachine,
    SyncBundle\Tests\SyncData\BankingMachine\Operator,
    SyncBundle\Tests\SyncData\BankingMachine\AccountGroup,
    SyncBundle\Tests\SyncData\BankingMachine\Replenishment;

class BankingMachineControllerTest extends WebTestCase
{
    const CUSTOM_BROWSER_KIT_HEADER_PREFIX = 'HTTP';

    const URL_CHECKIN_BANKING_MACHINES = (
        'http://api-v_1.cheers-development.in.ua/authentication'
    );

    const URL_SYNC_BANKING_MACHINES = (
        'http://api-v_1.cheers-development.in.ua/banking_machines'
    );

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
        $connectionPath = implode("/", array_filter([
            CheckinBankingMachine::getSyncAction(),
            $this->getBankingMachineSerial()
        ]));

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

    public function getSyncClientResponse($token, $action, $method, $data = NULL)
    {
        $connectionPath = implode("/", array_filter([
            $this->getBankingMachineSerial(),
            $action
        ]));

        $connectionURL = $this->getSyncBankingMachinesConnectionUrl(
            $connectionPath
        );

        $authorizationHeaderName = (
            self::CUSTOM_BROWSER_KIT_HEADER_PREFIX . "-" . Authorization::TOKEN_HEADER
        );

        $client = new Client();
        $client->request(
            $method,
            $connectionURL,
            [],
            [],
            [
                'Content-Type'           => 'application/json',
                $authorizationHeaderName => $token,
            ],
            $data
        );

        return $client->getResponse();
    }

    public function checkinBankingMachines()
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

        return $responseContent['data']['token'];
    }

    public function testGetBankingMachinesAction()
    {
        $token = $this->checkinBankingMachines();

        // Next sync request has valid token and thus returned OK

        $response = $this->getSyncClientResponse(
            $token, BankingMachine::getSyncAction(), BankingMachine::getSyncMethod()
        );

        $this->assertEquals(200, $response->getStatus());

        // Response array has required fields

        $responseContent = json_decode($response->getContent(), TRUE);

        $this->assertArrayHasKey('checksum', $responseContent);
        $this->assertArrayHasKey('data', $responseContent);

        // Response checksum valid

        $this->assertEquals(TRUE, $this->getChecksumCalculator()->verifyDataChecksum(
            $responseContent['checksum'], $responseContent['data']
        ));

        // Response data contains operators

        $this->assertArrayHasKey('banking-machine', $responseContent['data']);
    }

    public function testGetBankingMachinesOperatorsAction()
    {
        $token = $this->checkinBankingMachines();

        // Next sync request has valid token and thus returned OK

        $response = $this->getSyncClientResponse(
            $token, Operator::getSyncAction(), Operator::getSyncMethod()
        );

        $this->assertEquals(200, $response->getStatus());

        // Response array has required fields

        $responseContent = json_decode($response->getContent(), TRUE);

        $this->assertArrayHasKey('checksum', $responseContent);
        $this->assertArrayHasKey('data', $responseContent);

        // Response checksum valid

        $this->assertEquals(TRUE, $this->getChecksumCalculator()->verifyDataChecksum(
            $responseContent['checksum'], $responseContent['data']
        ));

        // Response data contains operators

        $this->assertArrayHasKey('operators', $responseContent['data']);
    }

    public function testGetBankingMachinesAccountGroupsAction()
    {
        $token = $this->checkinBankingMachines();

        // Next sync request has valid token and thus returned OK

        $response = $this->getSyncClientResponse(
            $token, AccountGroup::getSyncAction(), AccountGroup::getSyncMethod()
        );

        $this->assertEquals(200, $response->getStatus());

        // Response array has required fields

        $responseContent = json_decode($response->getContent(), TRUE);

        $this->assertArrayHasKey('checksum', $responseContent);
        $this->assertArrayHasKey('data', $responseContent);

        // Response checksum valid

        $this->assertEquals(TRUE, $this->getChecksumCalculator()->verifyDataChecksum(
            $responseContent['checksum'], $responseContent['data']
        ));

        // Response data contains operators

        $this->assertArrayHasKey('account-groups', $responseContent['data']);
    }

    public function testPostBankingMachinesReplenishmentsAction()
    {
        $token = $this->checkinBankingMachines();

        // Next sync request has valid token and thus returned OK

        $response = $this->getSyncClientResponse(
            $token, Replenishment::getSyncAction(), Replenishment::getSyncMethod(), Replenishment::getData()
        );

        $this->assertEquals(200, $response->getStatus());
    }
}
