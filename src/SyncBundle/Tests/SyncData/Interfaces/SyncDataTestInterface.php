<?php
// src/SyncBundle/Tests/SyncData/Interfaces/SyncDataTestInterface.php
namespace SyncBundle\Tests\SyncData\Interfaces;

interface SyncDataTestInterface
{
    static public function getData();

    static public function getSyncAction();

    static public function getSyncMethod();
}
