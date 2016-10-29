<?php
// src/AppBundle/Form/Type/OrganizationType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\OptionsResolver\OptionsResolver,
    Symfony\Component\Translation\TranslatorInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType,
    Symfony\Component\Form\Extension\Core\Type\SubmitType;

class OrganizationType extends AbstractType
{
    private $_translator;

    private $boundlessAccess;

    public function __construct(TranslatorInterface $translator, $boundlessAccess)
    {
        $this->_translator = $translator;

        $this->boundlessAccess = $boundlessAccess;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'organization.name.label',
                'attr'  => [
                    'placeholder'         => 'organization.name.placeholder',
                    'data-rule-required'  => "true",
                    'data-msg-required'   => $this->_translator->trans('organization.name.not_blank', [], 'validators'),
                    'data-rule-minlength' => 2,
                    'data-msg-minlength'  => $this->_translator->trans('organization.name.length.min', [], 'validators'),
                    'data-rule-maxlength' => 250,
                    'data-msg-maxlength'  => $this->_translator->trans('organization.name.length.max', [], 'validators'),
                ]
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $organization = $event->getData();

                $form = $event->getForm();

                if( $organization && $organization->getId() !== NULL )
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
            'data_class'         => 'AppBundle\Entity\Organization\Organization',
            'translation_domain' => 'forms'
        ]);
    }

    public function getBlockPrefix()
    {
        return 'organization';
    }
}
