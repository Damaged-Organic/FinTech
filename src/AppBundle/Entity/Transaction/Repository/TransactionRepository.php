<?php
// src/AppBundle/Entity/Transaction/Repository/TransactionRepository.php
namespace AppBundle\Entity\Transaction\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository,
    AppBundle\Entity\Transaction\Replenishment;

class TransactionRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('ta')
            ->select('ta')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'ta');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'ta.transactionId',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods

    public function rawInsertReplenishment(Replenishment $replenishment)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryArgs = [
            $replenishment->getSyncId(),
            $replenishment->getSyncAt()->format('Y-m-d H:i:s'),
            $replenishment->getTransactionAt()->format('Y-m-d H:i:s'),
            $replenishment->getBankingMachine()->getId(),
            $replenishment->getOrganization()->getId(),
            $replenishment->getOperator()->getId(),
            $replenishment->getAccountGroup()->getId(),
            'replenishment'
        ];
        $queryArgsNumber = count($queryArgs);

        if( !$queryArgs )
            return;

        $queryString = "
            INSERT INTO transactions (
                sync_id,
                sync_at,
                transaction_at,
                banking_machine_id,
                organization_id,
                operator_id,
                account_group_id,
                discriminator
            ) VALUES (" . substr(str_repeat("?,", $queryArgsNumber), 0, -1) . ")"
        ;

        $stmt = $conn->prepare($queryString);

        $stmt->execute($queryArgs);

        return $conn->lastInsertId();
    }

    public function rawUpdateTransactionsFunds(array $transactionsArray)
    {
        $queryStringWhen = $queryStringIds = '';
        $queryArgsWhen = $queryArgsIds = $queryArgs = [];

        foreach( $transactionsArray as $transaction )
        {
            $queryStringWhen .= " WHEN ? THEN ? ";
            $queryStringIds  .= "?,";

            $queryArgsWhen = array_merge($queryArgsWhen, [
                $transaction->getId(),
                $transaction->getTransactionFunds()
            ]);

            $queryArgsIds = array_merge($queryArgsIds, [
                $transaction->getId()
            ]);
        }

        $queryArgs = array_merge($queryArgsWhen, $queryArgsIds);

        if( !$queryArgs )
            return;

        $queryStringIds = substr($queryStringIds, 0, -1);

        $queryString = "
            UPDATE transactions
            SET transaction_funds =
            (CASE id {$queryStringWhen} END)
            WHERE id IN ({$queryStringIds})
        ";

        $stmt = $this->getEntityManager()->getConnection()->prepare($queryString);

        $stmt->execute($queryArgs);
    }
}
