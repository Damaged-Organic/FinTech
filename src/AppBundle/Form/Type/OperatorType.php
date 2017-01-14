<?php
// src/AppBundle/Form/Type/OperatorType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType,
    Symfony\Component\Form\Extension\Core\Type\CheckboxType,
    Symfony\Component\Form\Extension\Core\Type\SubmitType,
    Symfony\Bridge\Doctrine\Form\Type\EntityType;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\FormType
 */
class OperatorType extends AbstractType
{
    /** @DI\Inject("translator") */
    public $_translator;

    /** @DI\Inject("security.token_storage") */
    public $_tokenStorage;

    private $boundlessAccess;

    private $updateOrganizationAccess;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->boundlessAccess          = $options['boundlessAccess'];
        $this->updateOrganizationAccess = $options['updateOrganizationAccess'];

        $builder
            ->add('name', TextType::class, [
                'label' => 'operator.name.label',
                'attr'  => [
                    'placeholder'         => 'operator.name.placeholder',
                    'data-rule-required'  => "true",
                    'data-msg-required'   => $this->_translator->trans('operator.name.not_blank', [], 'validators'),
                    'data-rule-minlength' => 2,
                    'data-msg-minlength'  => $this->_translator->trans('operator.name.length.min', [], 'validators'),
                    'data-rule-maxlength' => 100,
                    'data-msg-maxlength'  => $this->_translator->trans('operator.name.length.max', [], 'validators'),
                ]
            ])
            ->add('surname', TextType::class, [
                'label' => 'operator.surname.label',
                'attr'  => [
                    'placeholder'         => 'operator.surname.placeholder',
                    'data-rule-required'  => "true",
                    'data-msg-required'   => $this->_translator->trans('operator.surname.not_blank', [], 'validators'),
                    'data-rule-minlength' => 2,
                    'data-msg-minlength'  => $this->_translator->trans('operator.surname.length.min', [], 'validators'),
                    'data-rule-maxlength' => 100,
                    'data-msg-maxlength'  => $this->_translator->trans('operator.surname.length.max', [], 'validators'),
                ]
            ])
            ->add('patronymic', TextType::class, [
                'label' => 'operator.patronymic.label',
                'attr'  => [
                    'placeholder'         => 'operator.patronymic.placeholder',
                    'data-rule-required'  => "true",
                    'data-msg-required'   => $this->_translator->trans('operator.patronymic.not_blank', [], 'validators'),
                    'data-rule-minlength' => 2,
                    'data-msg-minlength'  => $this->_translator->trans('operator.patronymic.length.min', [], 'validators'),
                    'data-rule-maxlength' => 100,
                    'data-msg-maxlength'  => $this->_translator->trans('operator.patronymic.length.max', [], 'validators'),
                ]
            ])
            ->add('phoneNumber', TextType::class, [
                'required' => FALSE,
                'label'    => 'operator.phone_number.label',
                'attr'     => [
                    'placeholder' => 'operator.phone_number.placeholder'
                ]
            ])

        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $operator = $event->getData();
                $operatorExists = ($operator && $operator->getId() !== NULL);

                $form = $event->getForm();

                if( $operatorExists ) {
                    if( $this->updateOrganizationAccess )
                    {
                        $form
                            ->add('organization', EntityType::class, [
                                'class'           => 'AppBundle\Entity\Organization\Organization',
                                'empty_data'      => 0,
                                'choice_label'    => 'name',
                                'label'           => 'operator.organization.label',
                                'placeholder'     => 'common.choice.placeholder',
                                'invalid_message' => $this->_translator->trans('operator.organization.invalid_massage', [], 'validators'),
                            ])
                        ;
                    } else {
                        $form
                            ->add('organization', TextType::class, [
                                'required'   => FALSE,
                                'disabled'   => TRUE,
                                'data_class' => 'AppBundle\Entity\Organization\Organization',
                                'label'      => 'employee.organization.label',
                                'attr'       => [
                                    'readonly' => TRUE,
                                ],
                            ])
                        ;
                    }

                    $form
                        ->add('operatorGroup', TextType::class, [
                            'required'   => FALSE,
                            'disabled'   => TRUE,
                            'data_class' => 'AppBundle\Entity\Operator\OperatorGroup',
                            'label'      => 'operator.operator_group.label',
                            'attr'       => [
                                'readonly' => TRUE,
                            ],
                        ])
                        ->add('isEnabled', CheckboxType::class, [
                            'required' => FALSE,
                            'label'    => 'operator.is_enabled.label'
                        ])
                        ->add('update', SubmitType::class, ['label' => 'common.update.label'])
                    ;

                    if( $this->boundlessAccess )
                        $form->add('update_and_return', SubmitType::class, ['label' => 'common.update_and_return.label']);
                } else {
                    if( $this->updateOrganizationAccess )
                    {
                        $form
                            ->add('organization', EntityType::class, [
                                'class'           => 'AppBundle\Entity\Organization\Organization',
                                'empty_data'      => 0,
                                'choice_label'    => 'name',
                                'label'           => 'operator.organization.label',
                                'placeholder'     => 'common.choice.placeholder',
                                'invalid_message' => $this->_translator->trans('operator.organization.invalid_massage', [], 'validators'),
                            ])
                        ;
                    } else {
                        $user = $this->_tokenStorage->getToken()->getUser();

                        if( $user && $user->getOrganization() )
                        {
                            $form
                                ->add('organization', TextType::class, [
                                    'disabled'   => TRUE,
                                    'data_class' => 'AppBundle\Entity\Organization\Organization',
                                    'label'      => 'employee.organization.label',
                                    'data'       => $user->getOrganization(),
                                    'attr'       => [
                                        'readonly' => TRUE,
                                    ],
                                ])
                            ;
                        }
                    }

                    $form
                        ->add('operatorGroup', EntityType::class, [
                            'class'           => 'AppBundle\Entity\Operator\OperatorGroup',
                            'empty_data'      => 0,
                            'choice_label'    => "name",
                            'label'           => 'operator.operator_group.label',
                            'placeholder'     => 'common.choice.placeholder',
                            'invalid_message' => $this->_translator->trans('operator.operator_group.invalid_massage', [], 'validators'),
                        ])
                        ->add('create', SubmitType::class, ['label' => 'common.create.label'])
                    ;

                    if( $this->boundlessAccess )
                        $form->add('create_and_return', SubmitType::class, ['label' => 'common.create_and_return.label']);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'               => 'AppBundle\Entity\Operator\Operator',
            'translation_domain'       => 'forms',
            'boundlessAccess'          => NULL,
            'updateOrganizationAccess' => NULL,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'operator';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
