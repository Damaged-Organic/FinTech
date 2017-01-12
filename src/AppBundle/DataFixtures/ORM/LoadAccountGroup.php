<?php
// src/AppBundle/DataFixtures/ORM/LoadAccountGroup.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Account\AccountGroup;

class LoadAccountGroup extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $accountGroup_1 = (new AccountGroup)
            ->setOrganization($this->getReference('organization_1'))
            ->setName('ПАТ "КРИСТАЛБАНК"')
        ;
        $manager->persist($accountGroup_1);

        // ---

        $accountGroup_2 = (new AccountGroup)
            ->setOrganization($this->getReference('organization_2'))
            ->setName('ПАТ "ДРУГОЙБАНК"')
        ;
        $manager->persist($accountGroup_2);

        // ---

        $this->addReference('accountGroup_1', $accountGroup_1);
        $this->addReference('accountGroup_2', $accountGroup_2);

        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }
}
