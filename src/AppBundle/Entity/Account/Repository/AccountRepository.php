<?php
// src/AppBundle/Entity/Account/Repository/AccountRepository.php
namespace AppBundle\Entity\Account\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class AccountRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('ac')
            ->select('ac, acg, org, bm')
            ->leftJoin('ac.accountGroup', 'acg')
            ->leftJoin('acg.organization', 'org')
            ->leftJoin('acg.bankingMachines', 'bm')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'ac');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'org.name',
            'bm.serial',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods
}
