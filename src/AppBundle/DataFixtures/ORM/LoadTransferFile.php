<?php
// src/AppBundle/DataFixtures/ORM/LoadTransferFile.php
namespace AppBundle\DataFixtures\ORM;

use DateTime;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Transfer\TransferFile;

class LoadTransferFile extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $now = new DateTime;

        $transferFile_1 = (new TransferFile)
            ->setDatetime($now)
            ->setDirname($now)
            ->setFilename('1')
        ;
        $manager->persist($transferFile_1);

        // ---

        $this->getReference('transfer_1')->setTransferFile($transferFile_1);

        // ---

        $manager->flush();
    }

    public function getOrder()
    {
        return 9;
    }
}
