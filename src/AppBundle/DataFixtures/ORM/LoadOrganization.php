<?php
// src/AppBundle/DataFixtures/ORM/LoadOrganization.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Organization\Organization;

class LoadOrganization extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $organization_1 = (new Organization)
            ->setName('Organization 1')
        ;
        $manager->persist($organization_1);

        // ---

        $organization_2 = (new Organization)
            ->setName('Organization 2')
        ;
        $manager->persist($organization_2);

        // ---

        $this->addReference('organization_1', $organization_1);
        $this->addReference('organization_2', $organization_2);

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}
