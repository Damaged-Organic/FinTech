<?php
// src/SyncBundle/Tests/SyncData/BankingMachineEvent.php
namespace SyncBundle\Tests\SyncData;

class BankingMachineEvent
{
    static public function getData()
    {
        $data = [
            'authentication' => [
                'login' => 'xxx-login',
                'password' => 'xxx-password'
            ],
            'sync' => [
                'id' => hash('sha256', '1')
            ],
            'data' => [
                'events' => [
                    [
                        'id' => 1,
                        'datetime' => (new DateTime)->format('Y-m-d H:i:s'),
                        'type' => 'some_type',
                        'code' => '010',
                        'message' => 'some_message'
                    ],
                    [
                        'id' => 2,
                        'datetime' => (new DateTime)->format('Y-m-d H:i:s'),
                        'type' => 'some_type',
                        'code' => '101',
                        'message' => 'some_message'
                    ]
                ]
            ]
        ];

        $data['checksum'] = hash('sha256', json_encode($data['data']));

        return json_encode($data);
    }
}
