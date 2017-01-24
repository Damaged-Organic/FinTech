<?php
// src/SyncBundle/Tests/SyncData/Authentication/CheckinBankingMachine.php
namespace SyncBundle\Tests\SyncData\Authentication;

use SyncBundle\Tests\SyncData\Interfaces\SyncDataTestInterface;

class CheckinBankingMachine implements SyncDataTestInterface
{
    const SYNC_METHOD = 'POST';
    const SYNC_ACTION = 'checkin/banking_machines';

    static public function getSyncMethod()
    {
        return self::SYNC_METHOD;
    }

    static public function getSyncAction()
    {
        return self::SYNC_ACTION;
    }

    static public function getData(array $arguments)
    {
        $data = [
            'authentication' => [
                'login'    => $arguments['login'],
                'password' => $arguments['password'],
            ]
        ];

        return json_encode($data);
    }
}
