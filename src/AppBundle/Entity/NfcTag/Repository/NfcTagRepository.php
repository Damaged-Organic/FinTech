<?php
// src/AppBundle/Entity/NfcTag/Repository/NfcTagRepository.php
namespace AppBundle\Entity\NfcTag\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class NfcTagRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('nt')
            ->select('nt')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'nt');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'nt.number',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods
}
