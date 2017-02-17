<?php
// src/SyncBundle/Service/BankingMachine/Sync/Formatter.php
namespace SyncBundle\Service\BankingMachine\Sync;

use DateTime;

use AppBundle\Entity\BankingMachine\BankingMachineSync,
    AppBundle\Serializer\BankingMachineSyncSerializer;

use SyncBundle\Service\BankingMachine\Sync\Utility\ChecksumCalculator;

class Formatter
{
    private $_checksumCalculator;

    public function setChecksumCalculator(ChecksumCalculator $checksumCalculator)
    {
        $this->_checksumCalculator = $checksumCalculator;
    }

    public function getExportBankingMachineSync($syncType = NULL, array $serialized)
    {
        $bankingMachineSync = (new BankingMachineSync)
            ->setSyncType($syncType)
            ->setSyncAt(new DateTime)
        ;

        $bankingMachineSync
            ->setRawData($serialized)
            ->setChecksum(
                $this->_checksumCalculator->getDataChecksum($serialized)
            )
        ;

        return $bankingMachineSync;
    }

    public function getImportBankingMachineSync($syncType, BankingMachineSync $bankingMachineSync)
    {
        $bankingMachineSync
            ->setSyncType($syncType)
        ;

        return $bankingMachineSync;
    }

    public function formatSyncData(BankingMachineSync $bankingMachineSync)
    {
        $dataPropertyName     = BankingMachineSyncSerializer::getDataPropertyName();
        $checksumPropertyName = BankingMachineSyncSerializer::getChecksumPropertyName();

        return json_encode([
            $dataPropertyName     => $bankingMachineSync->getRawData(),
            $checksumPropertyName => $bankingMachineSync->getChecksum()
        ], JSON_UNESCAPED_UNICODE);
    }
}
