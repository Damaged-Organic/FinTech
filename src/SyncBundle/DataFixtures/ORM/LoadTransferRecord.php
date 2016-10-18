<?php
// src/SyncBundle/DataFixtures/ORM/LoadTransferRecord.php
namespace SyncBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use SyncBundle\Entity\BankingServer\Transfer\TransferRecord;

class LoadTransferRecord extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $transferRecord_1 = (new TransferRecord)
            ->setMfoOffBankA(339050)
            ->setPersonalAccountOfBankA(1002107001)
            ->setMfoOfBankB(339050)
            ->setPersonalAccountOfBankB(2902407002)
            ->setDebitCreditPaymentFlag(TRUE)
            ->setPaymentAmount(878956)
            ->setPaymentDocumentType(68)
            ->setPaymentOperationalNumber(7658)
            ->setPaymentCurrency(980)
            ->setPaymentDocumentDate(new \DateTime('2016-08-11'))
            ->setPaymentDocumentArrivalDateToBankA(new \DateTime('2016-08-11'))
            ->setPayerNameOfClientA("")
            ->setPayerNameOfClientB("")
            ->setPaymentDestination("Платежi вiд населення згiдно дог. з ТОВ <Сонар> N7  вiд 12.05.2015, N8 вiд 12.05.2015")
            ->setSupportingProps("")
            ->setPaymentDestinationCode("")
            ->setStringsNumberInBlock("")
            ->setClientIdentifierA("")
            ->setClientIdentifierB("CASH5")
        ;
        $manager->persist($transferRecord_1);

        // ---

        $manager->flush();

        // ---

        $this->addReference('transfer_record_1', $transferRecord_1);
    }

    public function getOrder()
    {
        return 1;
    }
}
