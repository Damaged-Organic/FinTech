<?php
// src/AppBundle/Entity/Banknote/Repository/BanknoteListRepository.php
namespace AppBundle\Entity\Banknote\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository,
    AppBundle\Entity\Banknote\BanknoteList;

class BanknoteListRepository extends ExtendedEntityRepository
{
    public function rawInsertBanknoteLists(array $banknoteLists)
    {
        $queryString = '';
        $queryArgs   = [];

        foreach( $banknoteLists as $banknoteList )
        {
            if( $banknoteList instanceof BanknoteList )
            {
                $boundTokens = [
                    $banknoteList->getTransaction()->getId(),
                    $banknoteList->getBanknote()->getId(),
                    $banknoteList->getQuantity()
                ];
                $boundTokensNumber = count($boundTokens);

                $queryString .= "(" . substr(str_repeat("?,", $boundTokensNumber), 0, -1) . "),";
                $queryArgs    = array_merge($queryArgs, $boundTokens);
            }
        }

        if( !$queryArgs )
            return;

        $queryString = substr($queryString, 0, -1);

        $queryString = "
            INSERT INTO banknotes_lists (
                transaction_id,
                banknote_id,
                quantity
            ) VALUES " . $queryString
        ;

        $stmt = $this->getEntityManager()->getConnection()->prepare($queryString);

        $stmt->execute($queryArgs);
    }
}
