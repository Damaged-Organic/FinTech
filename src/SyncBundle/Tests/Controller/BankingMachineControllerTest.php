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
    SyncBundle\Tests\SyncData\BankingMachine\Replenishment,
    SyncBundle\Tests\SyncData\BankingMachine\Collection,
    SyncBundle\Tests\SyncData\BankingMachine\BankingMachineSync,
    SyncBundle\Tests\SyncData\BankingMachine\BankingMachineEvent;

class BankingMachineControllerTest extends WebTestCase
{
    const CUSTOM_BROWSER_KIT_HEADER_PREFIX = 'HTTP';

    const URL_CHECKIN_BANKING_MACHINES = (
        'http://api-v_1.cheers-development.in.ua/authentication'
        // 'http://api-v_1.fintech.dev/app_dev.php/authentication'
    );

    const URL_SYNC_BANKING_MACHINES = (
        'http://api-v_1.cheers-development.in.ua/banking_machines'
        // 'http://api-v_1.fintech.dev/app_dev.php/banking_machines'
    );

    public static function getChecksumCalculator()
    {
        return new ChecksumCalculator();
    }

    private $bankingMachineCredentials = [
        'serial'   => 'tstboard-0002',
        'login'    => 'xxx-login2',
        'password' => 'xxx-password2'
        // 'serial'   => 'SM-0001',
        // 'login'    => 'xxx-login',
        // 'password' => 'xxx-password'
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

    /**
     * @group data
     */
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

    /**
     * @group data
     */
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

    /**
     * @group data
     */
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

    public function getBankingMachinesSyncsAction($type)
    {
        $token = $this->checkinBankingMachines();

        // Next sync request has valid token and thus returned OK

        $response = $this->getSyncClientResponse(
            $token, BankingMachineSync::getSyncAction() . $type, BankingMachineSync::getSyncMethod()
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

        // Response data contains sync

        $this->assertArrayHasKey('sync', $responseContent['data']);

        // Sync contains id

        $this->assertArrayHasKey('id', $responseContent['data']['sync']);

        return $responseContent['data']['sync']['id'];
    }

    /**
     * @group transactions
     */
    public function testPostBankingMachinesReplenishmentsAction()
    {
        // Obtaining last sync id of a given type

        $lastSyncId = $this->getBankingMachinesSyncsAction(
            '?type=sync_post_banking_machines_replenishments'
        );

        $token = $this->checkinBankingMachines();

        // Next sync request has valid token and thus returned OK

        $response = $this->getSyncClientResponse(
            $token, Replenishment::getSyncAction(), Replenishment::getSyncMethod(), Replenishment::getData()
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

        $this->assertArrayHasKey('transaction-id', $responseContent['data']);

        // Second request with same syncId should return 200 OK

        $token = $this->checkinBankingMachines();

        $response = $this->getSyncClientResponse(
            $token, Replenishment::getSyncAction(), Replenishment::getSyncMethod(), Replenishment::getData()
        );

        $this->assertEquals(200, $response->getStatus());
        $this->assertEquals('Already in sync', $response->getContent());
    }

    /**
     * @group transactions
     */
    public function testPostBankingMachinesCollectionsAction()
    {
        // Obtaining last sync id of a given type

        $lastSyncId = $this->getBankingMachinesSyncsAction(
            '?type=sync_post_banking_machines_collections'
        );

        $token = $this->checkinBankingMachines();

        // Next sync request has valid token and thus returned OK

        $response = $this->getSyncClientResponse(
            $token, Collection::getSyncAction(), Collection::getSyncMethod(), Collection::getData()
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

        $this->assertArrayHasKey('transaction-id', $responseContent['data']);

        // Second request with same syncId should return 200 OK

        $token = $this->checkinBankingMachines();

        $response = $this->getSyncClientResponse(
            $token, Replenishment::getSyncAction(), Replenishment::getSyncMethod(), Replenishment::getData()
        );

        $this->assertEquals(200, $response->getStatus());
        $this->assertEquals('Already in sync', $response->getContent());
    }

    /**
     * @group events
     */
    public function testPostBankingMachinesEventsAction()
    {
        $token = $this->checkinBankingMachines();

        // Next sync request has valid token and thus returned OK

        $response = $this->getSyncClientResponse(
            $token, BankingMachineEvent::getSyncAction(), BankingMachineEvent::getSyncMethod(), BankingMachineEvent::getData()
        );

        $this->assertEquals(200, $response->getStatus());
        $this->assertEquals(NULL, $response->getContent());

        // Second request with same syncId should return 200 OK

        $token = $this->checkinBankingMachines();

        $response = $this->getSyncClientResponse(
            $token, BankingMachineEvent::getSyncAction(), BankingMachineEvent::getSyncMethod(), BankingMachineEvent::getData()
        );

        $this->assertEquals(200, $response->getStatus());
        $this->assertEquals('Already in sync', $response->getContent());
    }
}
