<?php
// src/SyncBundle/Service/BankingMachine/Sync/Utility/ChecksumCalculator.php
namespace SyncBundle\Service\BankingMachine\Sync\Utility;

class ChecksumCalculator
{
    public function getDataChecksum($data)
    {
        return hash('sha256', json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function verifyDataChecksum($checksum, $data)
    {
        return $checksum === $this->getDataChecksum($data);
    }
}
