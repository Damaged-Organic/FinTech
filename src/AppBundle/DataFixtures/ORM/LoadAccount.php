<?php
// src/AppBundle/DataFixtures/ORM/LoadAccount.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Account\Account;

class LoadAccount extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $account_1 = (new Account)
            ->setName("Test account 1")
            ->setPercent(75)
            ->setMfoOfBankA(999999999)
            ->setPersonalAccountOfBankA(99999999999999)
            ->setMfoOfBankB(999999999)
            ->setPersonalAccountOfBankB(99999999999999)
            ->setDebitCreditPaymentFlag(TRUE)
            ->setPaymentAmount(9999999999999999)
            ->setPaymentDocumentType(99)
            ->setPaymentOperationalNumber(9999999999)
            ->setPaymentCurrency(999)
            ->setPaymentDocumentDate(new \DateTime())
            ->setPaymentDocumentArrivalDateToBankA(new \DateTime())
            ->setPayerNameOfClientA("Test client A 1")
            ->setPayerNameOfClientB("Test client B 1")
            ->setPaymentDestination("Test destination 1")
            ->setSupportingProps("Supporting props 1")
            ->setPaymentDestinationCode("999")
            ->setStringsNumberInBlock("99")
            ->setClientIdentifierA("99999999999999")
            ->setClientIdentifierB("99999999999999")
        ;
        $manager->persist($account_1);

        // ---

        $account_2 = (new Account)
            ->setName("Test account 2")
            ->setPercent(25)
            ->setMfoOfBankA(999999999)
            ->setPersonalAccountOfBankA(99999999999999)
            ->setMfoOfBankB(999999999)
            ->setPersonalAccountOfBankB(99999999999999)
            ->setDebitCreditPaymentFlag(TRUE)
            ->setPaymentAmount(9999999999999999)
            ->setPaymentDocumentType(99)
            ->setPaymentOperationalNumber(9999999999)
            ->setPaymentCurrency(999)
            ->setPaymentDocumentDate(new \DateTime())
            ->setPaymentDocumentArrivalDateToBankA(new \DateTime())
            ->setPayerNameOfClientA("Test client A 2")
            ->setPayerNameOfClientB("Test client B 2")
            ->setPaymentDestination("Test destination 2")
            ->setSupportingProps("Supporting props 2")
            ->setPaymentDestinationCode("999")
            ->setStringsNumberInBlock("99")
            ->setClientIdentifierA("99999999999999")
            ->setClientIdentifierB("99999999999999")
        ;
        $manager->persist($account_2);

        // ---

        $account_3 = (new Account)
            ->setName("Test account 3")
            ->setPercent(100)
            ->setMfoOfBankA(999999999)
            ->setPersonalAccountOfBankA(99999999999999)
            ->setMfoOfBankB(999999999)
            ->setPersonalAccountOfBankB(99999999999999)
            ->setDebitCreditPaymentFlag(TRUE)
            ->setPaymentAmount(9999999999999999)
            ->setPaymentDocumentType(99)
            ->setPaymentOperationalNumber(9999999999)
            ->setPaymentCurrency(999)
            ->setPaymentDocumentDate(new \DateTime())
            ->setPaymentDocumentArrivalDateToBankA(new \DateTime())
            ->setPayerNameOfClientA("Test client A 3")
            ->setPayerNameOfClientB("Test client B 3")
            ->setPaymentDestination("Test destination 3")
            ->setSupportingProps("Supporting props 3")
            ->setPaymentDestinationCode("999")
            ->setStringsNumberInBlock("99")
            ->setClientIdentifierA("99999999999999")
            ->setClientIdentifierB("99999999999999")
        ;
        $manager->persist($account_3);

        // ---

        $manager->flush();

        // ---

        $this->addReference('account_1', $account_1);
        $this->addReference('account_2', $account_2);
        $this->addReference('account_3', $account_3);
    }

    public function getOrder()
    {
        return 3;
    }
}
