<?php
// src/SyncBundle/Tests/SyncData/BankingMachine/Replenishment.php
namespace SyncBundle\Tests\SyncData\BankingMachine;

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
            'banking_machine_id' => "SM-0001",
            'nfc_tag_id'         => '043fa3426f3f81',
            'notes'              => [
                [
                    'iso'     => "UAH",
                    'value'   => "100",
                    'ammount' => "2",
                ],
                [
                    'iso'     => "UAH",
                    'value'   => "20",
                    'ammount' => "3",
                ],
            ],
        ];

        return json_encode($data);
    }

    // static public function getData()
    // {
    //     $data = [
    //         'sync' => [
    //             'id' => hash('sha256', '1')
    //         ],
    //     	'data' => [
    //     		'replenishments' => [
    //     			[
    //     				'id' => 1,
    //     				'datetime' => (new DateTime)->format('Y-m-d H:i:s'),
    //                     'operator' => [
    //                         'id'      => 1,
    //                         'nfc-tag' => [
    //                             'code' => "5826e4b885d0f"
    //                         ]
    //                     ],
    //                     'account' => [
    //                         'id' => '123456789'
    //                     ],
    //     				'banknotes' => [
    //     					[
    //     						'currency' => 'UAH',
    //     						'nominal' => 1,
    //     						'quantity' => 1
    //     					]
    //     				],
    //                     'state' => 'message describing current BM state'
    //     			],
    //                 [
    //     				'id' => 2,
    //     				'datetime' => (new DateTime)->format('Y-m-d H:i:s'),
    //                     'operator' => [
    //                         'id' => 2,
    //                         'nfc-tag' => [
    //                             'code' => "4676e4b885d1g"
    //                         ]
    //                     ],
    //                     'account' => [
    //                         'id' => '987654321'
    //                     ],
    //     				'banknotes' => [
    //     					[
    //     						'currency' => 'UAH',
    //     						'nominal' => 10,
    //     						'quantity' => 5
    //     					]
    //     				],
    //                     'state' => NULL
    //     			],
    //                 // ...
    //     		]
    //     	]
    //     ];
    //
    //     $data['checksum'] = hash('sha256', json_encode($data['data']));
    //
    //     return json_encode($data);
    // }
}
