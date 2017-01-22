<?php
// src/SyncBundle/Tests/Controller/AuthenticationControllerTest.php
namespace SyncBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Goutte\Client;

use SyncBundle\Tests\SyncData\Authentication\BankingMachine;

class AuthenticationControllerTest extends WebTestCase
{
    const URL_AUTHENTICATION_CHECKIN_BANKING_MACHINES
        = 'http://api-v_1.fintech.dev/app_dev.php/authentication/checkin/banking_machines';

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

    private function getAuthenticationCheckinBankingMachinesConnectionUrl($bankingMachineSerial)
    {
        return self::URL_AUTHENTICATION_CHECKIN_BANKING_MACHINES . '/' . $bankingMachineSerial;
    }

    public function testAuthenticationCheckinBankingMachines()
    {
        $connectionURL = $this->getAuthenticationCheckinBankingMachinesConnectionUrl(
            $this->getBankingMachineSerial()
        );

        $data = BankingMachine::getData(
            $this->getBankingMachineLogin(), $this->getBankingMachinePassword()
        );

        $client = new Client();
        $client->request(
            BankingMachine::getSyncMethod(), $connectionURL, [], [], [
                'CONTENT_TYPE' => 'application/json'], $data
        );

        $this->assertEquals(200, $client->getResponse()->getStatus());
        var_dump($client->getResponse()->getContent());
        $this->assertEquals('null', $client->getResponse()->getContent());
    }
}
