<?php
// src/SyncBundle/Tests/SyncData/BankingMachine/Replenishment.php
namespace SyncBundle\Tests\SyncData\BankingMachine;

use DateTime;

use SyncBundle\Tests\SyncData\Interfaces\SyncDataTestInterface;

class Replenishment implements SyncDataTestInterface
{
    const SYNC_METHOD = 'POST';
    const SYNC_ACTION = 'replenishments';

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
            'replenishments' => [
                [
                    'transaction-at' => (new DateTime)->format('Y-m-d H:i:s'),
                    'operator' => [
                        'id'        => 1,
                        'full-name' => "First Test Test",
                    ],
                    'account-group' => [
                        'id'   => 1,
                        'name' => "First Test"
                    ],
                    'banknotes' => [
                        [
                            'currency' => 'UAH',
                            'nominal'  => 5,
                            'quantity' => 10
                        ],
                        [
                            'currency' => 'UAH',
                            'nominal'  => 2,
                            'quantity' => 20
                        ],
                        [
                            'currency' => 'UAH',
                            'nominal'  => 1,
                            'quantity' => 50
                        ]
                    ]
                ],
                [
                    'transaction-at' => (new DateTime)->format('Y-m-d H:i:s'),
                    'operator' => [
                        'id'        => 2,
                        'full-name' => "Second Test Test",
                    ],
                    'account-group' => [
                        'id'   => 1,
                        'name' => "Second Test"
                    ],
                    'banknotes' => [
                        [
                            'currency' => 'UAH',
                            'nominal'  => 100,
                            'quantity' => 3
                        ],
                        [
                            'currency' => 'UAH',
                            'nominal'  => 200,
                            'quantity' => 2
                        ],
                        [
                            'currency' => 'UAH',
                            'nominal'  => 500,
                            'quantity' => 1
                        ]
                    ]
                ]
            ]
        ];

        $data['checksum'] = hash('sha256', json_encode($data['data']));

        return json_encode($data);
    }
}
