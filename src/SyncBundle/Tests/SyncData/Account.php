<?php
// src/SyncBundle/Tests/SyncData/Account.php
namespace SyncBundle\Tests\SyncData;

class Account
{
    static public function getData()
    {
        $data = [
            'data' => [
                'accounts' => [
                    [
                        'id' => 1,
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
                                'type' => 'cashier',
                                'full_name' => 'Some Cashier',
                                'nfc-tag' => [
                                    'code' => "8526e4b885df0"
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 2,
                        'operators' => [
                            [
                                'id' => 1,
                                'type' => 'cashier',
                                'full_name' => 'Some Cashier',
                                'nfc-tag' => [
                                    'code' => "5826e4b885d0f"
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $data['checksum'] = hash('sha256', json_encode($data['data']));

        return json_encode($data);
    }
}
