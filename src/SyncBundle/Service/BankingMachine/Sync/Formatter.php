<?php
// src/SyncBundle/Service/BankingMachine/Sync/Formatter.php
namespace SyncBundle\Service\BankingMachine\Sync;

use SyncBundle\Service\BankingMachine\Sync\Interfaces\SyncDataInterface,
    SyncBundle\Service\BankingMachine\Sync\Utility\ChecksumCalculator;

class Formatter implements SyncDataInterface
{
    private $_checksumCalculator;

    public function setChecksumCalculator(ChecksumCalculator $checksumCalculator)
    {
        $this->_checksumCalculator = $checksumCalculator;
    }

    public function formatRawData(array $data)
    {
        $formattedData = [
            self::SYNC_CHECKSUM => $this->_checksumCalculator->getDataChecksum($data),
            self::SYNC_DATA     => $data
        ];

        return $formattedData;
    }
}
