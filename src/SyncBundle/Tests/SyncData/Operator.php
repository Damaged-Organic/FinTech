<?php
// src/SyncBundle/Tests/SyncData/Operator.php
namespace SyncBundle\Tests\SyncData;

class Operator
{
    static public function getData()
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
