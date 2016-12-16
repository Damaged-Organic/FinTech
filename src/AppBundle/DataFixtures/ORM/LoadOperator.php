<?php
// src/AppBundle/DataFixtures/ORM/LoadOperator.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Operator\Operator,
    AppBundle\Entity\Operator\OperatorGroup;

class LoadOperator extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $operator_1 = (new Operator)
            ->setOperatorGroup($this->getReference('cashier'))
            ->setOrganization($this->getReference('organization_1'))
            ->setBankingMachine($this->getReference('bankingMachine_1'))
            ->setName('Julius')
            ->setSurname('Gaius')
            ->setPatronymic('Caesar')
        ;
        $manager->persist($operator_1);

        // ---

        $operator_2 = (new Operator)
            ->setOperatorGroup($this->getReference('collector'))
            ->setOrganization($this->getReference('organization_2'))
            ->setBankingMachine($this->getReference('bankingMachine_1'))
            ->setName('Octavius')
            ->setSurname('Gaius')
            ->setPatronymic('Caesar')
        ;
        $manager->persist($operator_2);

        // ---

        $this->addReference('operator_1', $operator_1);
        $this->addReference('operator_2', $operator_2);

        $manager->flush();
    }

    public function getOrder()
    {
        return 6;
    }
}
