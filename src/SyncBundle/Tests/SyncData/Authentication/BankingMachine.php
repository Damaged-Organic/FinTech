<?php
// src/SyncBundle/Tests/SyncData/Authentication/BankingMachine.php
namespace SyncBundle\Tests\SyncData\Authentication;

use SyncBundle\Tests\SyncData\Interfaces\SyncDataTestInterface;

class BankingMachine
{
    const SYNC_METHOD = 'POST';

    static public function getSyncAction()
    {
        return NULL;
    }

    static public function getSyncMethod()
    {
        return self::SYNC_METHOD;
    }

    static public function getData($login, $password)
    {
        $data = [
            'authentication' => [
                'login'    => $login,
                'password' => $password,
            ]
        ];

        return json_encode($data);
    }
}
