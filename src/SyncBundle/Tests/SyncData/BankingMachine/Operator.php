<?php
// src/SyncBundle/Tests/SyncData/BankingMachine/Operator.php
namespace SyncBundle\Tests\SyncData\BankingMachine;

use SyncBundle\Tests\SyncData\Interfaces\SyncDataTestInterface;

class Operator implements SyncDataTestInterface
{
    const SYNC_METHOD = 'GET';
    const SYNC_ACTION = 'operators';

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

    static public function sampleOutgoingData()
    {
        $data = [
            'data' => [
                'operators' => [
                    [
                        'id' => 1,
                        'type' => 'cashier',
                        'full_name' => 'Some Cashier',
                        'nfc-tag' => [
                            'code' => "5826e4b885d0f"
                        ]
                    ],
                    [
                        'id' => 2,
                        'type' => 'collector',
                        'full_name' => 'Some Collector',
                        'nfc-tag' => [
                            'code' => "4676e4b885d1g"
                        ]
                    ]
                ]
            ]
        ];

        $data['checksum'] = hash('sha256', json_encode($data['data']));

        return json_encode($data);
    }
}
