<?php
// src/AppBundle/DataFixtures/ORM/LoadTransfer.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Transfer\Transfer;

class LoadTransfer extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $transfer_1 = (new Transfer)
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
        $manager->persist($transfer_1);

        // ---

        $manager->flush();

        // ---

        $this->addReference('transfer_1', $transfer_1);
    }

    public function getOrder()
    {
        return 11;
    }
}
