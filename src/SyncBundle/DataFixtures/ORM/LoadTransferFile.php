<?php
// src/SyncBundle/DataFixtures/ORM/LoadTransferFile.php
namespace SyncBundle\DataFixtures\ORM;

use DateTime;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use SyncBundle\Entity\BankingServer\Transfer\TransferFile;

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

        $this->getReference('transfer_record_1')->setTransferFile($transferFile_1);

        // ---

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
