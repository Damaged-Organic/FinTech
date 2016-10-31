<?php
// AppBundle/Service/Repository/GlobalRepository.php
namespace AppBundle\Service\Repository;

use Doctrine\DBAL\Connection;

class GlobalRepository
{
    private $_connection;

    public function setConnection(Connection $connection)
    {
        $this->_connection = $connection;
    }

    public function countEntities()
    {
        $query = "
            SELECT
              (SELECT COUNT(id) FROM employees) AS employees,
              (SELECT COUNT(id) FROM organizations) AS organizations,
              (SELECT COUNT(id) FROM banking_machines) AS bankingMachines,
              (SELECT COUNT(id) FROM operators) AS operators,
              (SELECT COUNT(id) FROM nfc_tags) AS nfcTags
        ";

        $statement = $this->_connection->prepare($query);

        $statement->execute();

        return $statement->fetchAll()[0];
    }
}
