<?php
// src/SyncBundle/Tests/SyncData/BankingMachine/AccountGroup.php
namespace SyncBundle\Tests\SyncData\BankingMachine;

use SyncBundle\Tests\SyncData\Interfaces\SyncDataTestInterface;

class AccountGroup implements SyncDataTestInterface
{
    const SYNC_METHOD = 'GET';
    const SYNC_ACTION = 'account_groups';

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
        return NULL;
    }
}
