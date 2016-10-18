<?php
// src/AppBundle/Form/Type/SettingStringType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolver,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\Form\FormEvent;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class SettingStringType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
        {
            $settingString = $event->getData();

            $form = $event->getForm();

            $form
                ->add('settingValue', TextType::class, [
                    'required' => TRUE,
                    'label'    => $settingString->getName(),
                ])
            ;
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Entity\Setting\SettingString',
            'translation_domain' => 'forms'
        ]);
    }

    public function getBlockPrefix()
    {
        return 'setting_string';
    }
}
