<?php
// AppBundle/Entity/Setting/Repository/SettingRepository.php
namespace AppBundle\Entity\Setting\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class SettingRepository extends ExtendedEntityRepository
{
    public function findOne()
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->setMaxResults(1)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
        ;

        return $query->getOneOrNullResult();
    }

    public function findSettingBySettingKey($settingKey)
    {
        $query = $this->createQueryBuilder('s')
            ->select('s, ss, sd')
            ->leftJoin('s.settingsString', 'ss')
            ->leftJoin('s.settingsDecimal', 'sd')
            ->where('ss.settingKey = :settingKey')
            ->orWhere('sd.settingKey = :settingKey')
            ->setParameter('settingKey', $settingKey)
            ->setMaxResults(1)
            ->getQuery()
        ;

        return $query->getSingleResult();
    }

    public function findSomeDecimalSetting()
    {
        return $this->findSettingBySettingKey('some_decimal_setting')
            ->getSettingsDecimal()[0];
    }

    public function findSomeStringSetting()
    {
        return $this->findSettingBySettingKey('some_string_setting')
            ->getSettingsString()[0];
    }
}
