<?php
// src/AppBundle/Entity/BankingMachine/Repository/BankingMachineSyncRepository.php
namespace AppBundle\Entity\BankingMachine\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class BankingMachineSyncRepository extends ExtendedEntityRepository
{
    public function findLatestByBankingMachineSyncType($bankingMachine, $syncType)
    {
        $query = $this->createQueryBuilder('bms')
            ->select('bms')
            ->where('bms.bankingMachine = :bankingMachine')
            ->andWhere('bms.syncType = :syncType')
            ->setParameters([
                'bankingMachine' => $bankingMachine,
                'syncType'     => $syncType
            ])
            ->orderBy('bms.syncAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
        ;

        return $query->getOneOrNullResult();
    }
}
