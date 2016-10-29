<?php
// src/AppBundle/Entity/Operator/Repository/OperatorRepository.php
namespace AppBundle\Entity\Operator\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class OperatorRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('op')
            ->select('op')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'op');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'op.name', 'op.surname', 'op.patronymic',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods
}
