<?php
// src/SyncBundle/Tests/SyncData/BankingMachine/BankingMachine.php
namespace SyncBundle\Tests\SyncData\BankingMachine;

use SyncBundle\Tests\SyncData\Interfaces\SyncDataTestInterface;

class BankingMachine implements SyncDataTestInterface
{
    const SYNC_METHOD = 'GET';

    static public function getSyncMethod()
    {
        return self::SYNC_METHOD;
    }

    static public function getSyncAction()
    {
        return NULL;
    }

    static public function getData(array $arguments)
    {
        return NULL;
    }
}
