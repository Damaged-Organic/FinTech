<?php
// AppBundle/DataFixtures/ORM/LoadEmployeeGroup.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Employee\EmployeeGroup;

class LoadEmployeeGroup extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $employeeGroup_1 = (new EmployeeGroup)
            ->setName("Суперадминистратор")
            ->setRole(EmployeeGroup::ROLE_SUPERADMIN)
        ;
        $manager->persist($employeeGroup_1);

        // ---

        $employeeGroup_2 = (new EmployeeGroup)
            ->setName("Администратор")
            ->setRole(EmployeeGroup::ROLE_ADMIN)
        ;
        $manager->persist($employeeGroup_2);

        // ---

        $employeeGroup_3 = (new EmployeeGroup)
            ->setName("Менеджер")
            ->setRole(EmployeeGroup::ROLE_MANAGER)
        ;
        $manager->persist($employeeGroup_3);

        // ---

        $manager->flush();

        $this->addReference('superadministrator', $employeeGroup_1);
        $this->addReference('administrator', $employeeGroup_2);
        $this->addReference('manager', $employeeGroup_3);
    }

    public function getOrder()
    {
        return 2;
    }
}
