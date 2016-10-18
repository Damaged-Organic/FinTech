<?php
// src/AppBundle/DataFixtures/ORM/LoadSetting.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Setting\Setting,
    AppBundle\Entity\Setting\SettingDecimal,
    AppBundle\Entity\Setting\SettingString;

class LoadSetting extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $setting = (new Setting);

        $manager->persist($setting);
        $manager->flush();

        $this->addReference('setting', $setting);

        // ---

        $decimalSetting = (new SettingDecimal)
            ->setSetting($this->getReference('setting'))
            ->setName("Decimal Setting")
            ->setSettingKey("some_decimal_setting")
            ->setSettingValue(0.00)
        ;
        $manager->persist($decimalSetting);

        // ---

        $stringSetting = (new SettingString)
            ->setSetting($this->getReference('setting'))
            ->setName("String Setting")
            ->setSettingKey("some_string_setting")
            ->setSettingValue("")
        ;
        $manager->persist($stringSetting);

        // ---

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
