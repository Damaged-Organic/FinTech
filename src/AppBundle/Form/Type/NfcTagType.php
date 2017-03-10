<?php
// src/AppBundle/Form/Type/NfcTagType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType,
    Symfony\Component\Form\Extension\Core\Type\SubmitType,
    Symfony\Bridge\Doctrine\Form\Type\EntityType;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\FormType
 */
class NfcTagType extends AbstractType
{
    /** @DI\Inject("translator") */
    public $_translator;

    private $boundlessAccess;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->boundlessAccess = $options['boundlessAccess'];

        $builder
            ->add('number', TextType::class, [
                'label' => 'nfc_tag.number.label',
                'attr'  => [
                    'placeholder'         => 'nfc_tag.number.placeholder',
                    'data-rule-required'  => "true",
                    'data-msg-required'   => $this->_translator->trans('nfc_tag.number.not_blank', [], 'validators'),
                ]
            ])
            ->add('code', TextType::class, [
                'label' => 'nfc_tag.code.label',
                'attr'  => [
                    'placeholder'         => 'nfc_tag.code.placeholder',
                    'data-rule-required'  => "true",
                    'data-msg-required'   => $this->_translator->trans('nfc_tag.code.not_blank', [], 'validators'),
                ]
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $nfcTag = $event->getData();

                $form = $event->getForm();

                if( $nfcTag && $nfcTag->getId() !== NULL )
                {
                    $form->add('update', SubmitType::class, ['label' => 'common.update.label']);

                    if( $this->boundlessAccess )
                        $form->add('update_and_return', SubmitType::class, ['label' => 'common.update_and_return.label']);
                } else {
                    $form->add('create', SubmitType::class, ['label' => 'common.create.label']);

                    if( $this->boundlessAccess )
                        $form->add('create_and_return', SubmitType::class, ['label' => 'common.create_and_return.label']);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Entity\NfcTag\NfcTag',
            'validation_groups'  => ['NfcTag'],
            'translation_domain' => 'forms',
            'boundlessAccess'    => NULL,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'nfc_tag';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
