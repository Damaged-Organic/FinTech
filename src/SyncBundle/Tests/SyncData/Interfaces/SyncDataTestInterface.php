<?php
// src/SyncBundle/Tests/SyncData/Interfaces/SyncDataTestInterface.php
namespace SyncBundle\Tests\SyncData\Interfaces;

interface SyncDataTestInterface
{
    const SYNC_ID = 5;

    static public function getSyncMethod();

    static public function getSyncAction();

    static public function getData(array $arguments);
}
