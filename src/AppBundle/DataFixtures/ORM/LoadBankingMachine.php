<?php
// src/AppBundle/DataFixtures/ORM/LoadBankingMachine.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\BankingMachine\BankingMachine;

class LoadBankingMachine extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $bankingMachine_1 = (new BankingMachine)
            ->setOrganization($this->getReference('organization_1'))
            ->setSerial('SM-0001')
            ->setName('Smart Machine Alpha')
            ->setAddress('1600 Amphitheatre Parkway, Mountain View, CA 94043')
            ->setLocation('Office 1')
        ;
        $manager->persist($bankingMachine_1);

        // ---

        $bankingMachine_2 = (new BankingMachine)
            ->setOrganization($this->getReference('organization_2'))
            ->setSerial('SM-0002')
            ->setName('Smart Machine Beta')
            ->setAddress('6100 Amphitheatre Parkway, Mountain View, CA 94043')
            ->setLocation('Office 2')
        ;
        $manager->persist($bankingMachine_2);

        // ---

        $this->addReference('bankingMachine_1', $bankingMachine_1);
        $this->addReference('bankingMachine_2', $bankingMachine_2);

        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}
