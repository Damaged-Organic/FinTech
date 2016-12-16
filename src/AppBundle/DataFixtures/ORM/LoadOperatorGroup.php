<?php
// src/AppBundle/DataFixtures/ORM/LoadOperatorGroup.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Operator\OperatorGroup;

class LoadOperatorGroup extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $operator_group_1 = (new OperatorGroup)
            ->setName('Кассир')
            ->setRole(OperatorGroup::ROLE_CASHIER)
        ;
        $manager->persist($operator_group_1);

        // ---

        $operator_group_2 = (new OperatorGroup)
            ->setName('Инкассатор')
            ->setRole(OperatorGroup::ROLE_COLLECTOR)
        ;
        $manager->persist($operator_group_2);

        // ---

        $this->addReference('cashier', $operator_group_1);
        $this->addReference('collector', $operator_group_2);

        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}
