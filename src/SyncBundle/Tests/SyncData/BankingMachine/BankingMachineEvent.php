<?php
// src/SyncBundle/Tests/SyncData/BankingMachine/BankingMachineEvent.php
namespace SyncBundle\Tests\SyncData\BankingMachine;

use DateTime;

use SyncBundle\Tests\SyncData\Interfaces\SyncDataTestInterface;

class BankingMachineEvent implements SyncDataTestInterface
{
    const SYNC_METHOD = 'POST';
    const SYNC_ACTION = 'events';

    static public function getSyncMethod()
    {
        return self::SYNC_METHOD;
    }

    static public function getSyncAction()
    {
        return self::SYNC_ACTION;
    }

    static public function getData(array $arguments = NULL)
    {
        $data['data'] = [
            'sync' => [
                'id' => hash('sha256', self::SYNC_ID),
                'at' => (new DateTime)->format('Y-m-d H:i:s')
            ],
            'events' => [
                [
                    'event-at' => (new DateTime)->format('Y-m-d H:i:s'),
                    'type'     => 'Type A',
                    'code'     => '13 (0xD)',
                    'message'  => 'description...'
                ],
                [
                    'event-at' => (new DateTime)->format('Y-m-d H:i:s'),
                    'type'     => 'Type B',
                    'code'     => '14 (0xE)',
                    'message'  => 'description...'
                ]
            ]
        ];

        $data['checksum'] = hash('sha256', json_encode($data['data']));

        return json_encode($data);
    }
}
