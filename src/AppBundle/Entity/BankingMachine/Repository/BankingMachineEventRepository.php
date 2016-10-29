<?php
// src/AppBundle/Entity/BankingMachine/Repository/BankingMachineEventRepository.php
namespace AppBundle\Entity\BankingMachine\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class BankingMachineEventRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('bme')
            ->select('bme, bm')
            ->leftJoin('bme.bankingMachine', 'bm')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'bme');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'bm.serial',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods
}
