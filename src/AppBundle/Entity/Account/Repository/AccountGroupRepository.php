<?php
// src/AppBundle/Entity/Account/Repository/AccountGroupRepository.php
namespace AppBundle\Entity\Account\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class AccountGroupRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('acg')
            ->select('acg, ac, org, bm')
            ->leftJoin('acg.accounts', 'ac')
            ->leftJoin('acg.organization', 'org')
            ->leftJoin('acg.bankingMachines', 'bm')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'acg');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'org.name',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods
}
