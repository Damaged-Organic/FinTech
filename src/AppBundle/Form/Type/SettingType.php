<?php
// src/AppBundle/Form/Type/SettingType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CollectionType,
    Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('settingsDecimal', CollectionType::class, [
                'entry_type' => new SettingDecimalType
            ])
            ->add('settingsString', CollectionType::class, [
                'entry_type' => new SettingStringType
            ])
            ->add('update', SubmitType::class, ['label' => 'common.update.label']);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Entity\Setting\Setting',
            'translation_domain' => 'forms'
        ]);
    }

    public function getBlockPrefix()
    {
        return 'setting';
    }
}
