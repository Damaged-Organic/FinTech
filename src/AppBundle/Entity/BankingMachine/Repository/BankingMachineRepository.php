<?php
// src/AppBundle/Entity/BankingMachine/Repository/BankingMachineRepository.php
namespace AppBundle\Entity\BankingMachine\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class BankingMachineRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('bm')
            ->select('bm, bms, bme, org, acg')
            ->leftJoin('bm.bankingMachineSyncs', 'bms')
            ->leftJoin('bm.bankingMachineEvents', 'bme')
            ->leftJoin('bm.organization', 'org')
            ->leftJoin('bm.accountGroups', 'acg')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'bm');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'bm.serial',
            'bms.syncedAt',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods

    public function findOneBySerialPrefetchRelated($serial)
    {
        $query = $this->createQueryBuilder('bm')
            ->select('bm, bms, bme, org, op, acg')
            ->leftJoin('bm.bankingMachineSyncs', 'bms')
            ->leftJoin('bm.bankingMachineEvents', 'bme')
            ->leftJoin('bm.organization', 'org')
            ->leftJoin('bm.operators', 'op')
            ->leftJoin('bm.accountGroups', 'acg')
            ->where('bm.serial = :serial')
            ->setParameter(':serial', $serial)
            ->getQuery()
        ;

        return $query->getOneOrNullResult();
    }
}
