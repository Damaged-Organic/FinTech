<?php
// src/AppBundle/DataFixtures/ORM/LoadNfcTag.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\NfcTag\NfcTag;

class LoadNfcTag extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $nfcTag_1 = (new NfcTag)
            ->setOperator($this->getReference('operator_1'))
            ->setNumber('AA000001')
            ->setCode(uniqid())
        ;
        $manager->persist($nfcTag_1);

        // ---

        $nfcTag_2 = (new NfcTag)
            ->setOperator($this->getReference('operator_2'))
            ->setNumber('AA000002')
            ->setCode(uniqid())
        ;
        $manager->persist($nfcTag_2);

        // ---

        $manager->flush();
    }

    public function getOrder()
    {
        return 7;
    }
}
