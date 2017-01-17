<?php
// src/AppBundle/DataFixtures/ORM/LoadTransaction.php
namespace AppBundle\DataFixtures\ORM;

use DateTime;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Transaction\Transaction,
    AppBundle\Entity\Transaction\TransactionFrozen;

class LoadTransaction extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $transaction_1 = (new Transaction)
            ->setSyncId(1)
            ->setSyncAt(new DateTime())
            ->setBankingMachine($this->getReference('bankingMachine_1'))
            ->setOperator($this->getReference('operator_1'))
            ->setAccountGroup($this->getReference('accountGroup_1'))
        ;
        $transactionFrozen_1 = (new TransactionFrozen)->freeze($transaction_1);

        $manager->persist($transaction_1);
        $manager->persist($transactionFrozen_1);

        // ---

        $transaction_2 = (new Transaction)
            ->setSyncId(2)
            ->setSyncAt(new DateTime())
            ->setBankingMachine($this->getReference('bankingMachine_2'))
            ->setOperator($this->getReference('operator_2'))
            ->setAccountGroup($this->getReference('accountGroup_2'))
        ;
        $transactionFrozen_2 = (new TransactionFrozen)->freeze($transaction_2);

        $manager->persist($transaction_2);
        $manager->persist($transactionFrozen_2);

        // ---

        $this->addReference('transaction_1', $transaction_1);
        $this->addReference('transaction_2', $transaction_2);

        $manager->flush();
    }

    public function getOrder()
    {
        return 9;
    }
}
