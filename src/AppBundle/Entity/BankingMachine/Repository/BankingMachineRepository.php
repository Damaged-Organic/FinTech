<?php
// src/AppBundle/Entity/BankingMachine/Repository/BankingMachineRepository.php
namespace AppBundle\Entity\BankingMachine\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class BankingMachineRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('vm')
            ->select('bm, bms, bme, o')
            ->leftJoin('bm.bankingMachineSyncs', 'bms')
            ->leftJoin('bm.bankingMachineEvents', 'bme')
            ->leftJoin('bm.organization', 'o')
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
            ->select('bm, bms, bme, o')
            ->leftJoin('bm.bankingMachineSyncs', 'bms')
            ->leftJoin('bm.bankingMachineEvents', 'bme')
            ->leftJoin('bm.organization', 'o')
            ->where('bm.serial = :serial')
            ->setParameter(':serial', $serial)
            ->getQuery()
        ;

        return $query->getSingleResult();
    }
}
