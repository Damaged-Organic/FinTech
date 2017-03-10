<?php
// AppBundle/Form/Type/EmployeeType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType,
    Symfony\Component\Form\Extension\Core\Type\EmailType,
    Symfony\Component\Form\Extension\Core\Type\PasswordType,
    Symfony\Component\Form\Extension\Core\Type\RepeatedType,
    Symfony\Component\Form\Extension\Core\Type\CheckboxType,
    Symfony\Component\Form\Extension\Core\Type\SubmitType,
    Symfony\Bridge\Doctrine\Form\Type\EntityType;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Entity\Employee\Repository\EmployeeGroupRepository;

/**
 * @DI\FormType
 */
class EmployeeType extends AbstractType
{
    /** @DI\Inject("translator") */
    public $_translator;

    private $boundlessAccess;

    private $updateSystemAccess;

    private $readOrganizationAccess;
    private $updateOrganizationAccess;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * TRICKY: $this->boundlessAccess is a string containing exact user role,
         * which also equals TRUE during loose (==) authorization check
         */
        $this->boundlessAccess = $options['boundlessAccess'];

        $this->updateSystemAccess = $options['updateSystemAccess'];

        $this->readOrganizationAccess   = $options['readOrganizationAccess'];
        $this->updateOrganizationAccess = $options['updateOrganizationAccess'];

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $employee       = $event->getData();
                $employeeExists = ($employee && $employee->getId() !== NULL);

                $form = $event->getForm();

                if( !$employeeExists )
                {
                    if( $this->boundlessAccess )
                    {
                        $form
                            ->add('username', TextType::class, [
                                'label' => 'employee.username.label',
                                'attr'  => [
                                    'placeholder'         => 'employee.username.placeholder',
                                    'data-rule-required'  => "true",
                                    'data-msg-required'   => $this->_translator->trans('employee.username.not_blank', [], 'validators'),
                                    'data-rule-minlength' => 3,
                                    'data-msg-minlength'  => $this->_translator->trans('employee.username.length.min', [], 'validators'),
                                    'data-rule-maxlength' => 200,
                                    'data-msg-maxlength'  => $this->_translator->trans('employee.username.length.max', [], 'validators'),
                                ]
                            ])
                            ->add('organization', EntityType::class, [
                                'class'        => 'AppBundle\Entity\Organization\Organization',
                                'choice_label' => 'name',
                                'label'        => 'employee.organization.label',
                                'empty_value'  => 'common.choice.placeholder',
                            ])
                        ;
                    }
                } else {
                    if( $this->updateSystemAccess )
                    {
                        $form
                            ->add('username', TextType::class, [
                                'label' => 'employee.username.label',
                                'attr'  => [
                                    'placeholder'         => 'employee.username.placeholder',
                                    'data-rule-required'  => "true",
                                    'data-msg-required'   => $this->_translator->trans('employee.username.not_blank', [], 'validators'),
                                    'data-rule-minlength' => 3,
                                    'data-msg-minlength'  => $this->_translator->trans('employee.username.length.min', [], 'validators'),
                                    'data-rule-maxlength' => 200,
                                    'data-msg-maxlength'  => $this->_translator->trans('employee.username.length.max', [], 'validators'),
                                ]
                            ])
                        ;
                    }

                    if( $this->readOrganizationAccess )
                    {
                        if( $this->updateOrganizationAccess )
                        {
                            $form
                                ->add('organization', EntityType::class, [
                                    'class'        => 'AppBundle\Entity\Organization\Organization',
                                    'choice_label' => 'name',
                                    'label'        => 'employee.organization.label',
                                    'empty_value'  => 'common.choice.placeholder',
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
                    }
                }
            });

        $builder
            ->add('name', TextType::class, [
                'required' => FALSE,
                'label'    => 'employee.name.label',
                'attr'     => [
                    'placeholder'         => 'employee.name.placeholder',
                    'data-rule-minlength' => 2,
                    'data-msg-minlength'  => $this->_translator->trans('common.human_name.length.min', [], 'validators'),
                    'data-rule-maxlength' => 100,
                    'data-msg-maxlength'  => $this->_translator->trans('common.human_name.length.max', [], 'validators'),
                ]
            ])
            ->add('surname', TextType::class, [
                'required' => FALSE,
                'label'    => 'employee.surname.label',
                'attr'     => [
                    'placeholder'         => 'employee.surname.placeholder',
                    'data-rule-minlength' => 2,
                    'data-msg-minlength'  => $this->_translator->trans('common.human_name.length.min', [], 'validators'),
                    'data-rule-maxlength' => 100,
                    'data-msg-maxlength'  => $this->_translator->trans('common.human_name.length.max', [], 'validators'),
                ]
            ])
            ->add('patronymic', TextType::class, [
                'required' => FALSE,
                'label'    => 'employee.patronymic.label',
                'attr'     => [
                    'placeholder'         => 'employee.patronymic.placeholder',
                    'data-rule-minlength' => 2,
                    'data-msg-minlength'  => $this->_translator->trans('common.human_name.length.min', [], 'validators'),
                    'data-rule-maxlength' => 100,
                    'data-msg-maxlength'  => $this->_translator->trans('common.human_name.length.max', [], 'validators'),
                ]
            ])
            ->add('email', EmailType::class, [
                'required' => FALSE,
                'label'    => 'employee.email.label',
                'attr'     => [
                    'placeholder'     => 'employee.email.placeholder',
                    'data-rule-email' => "true",
                    'data-msg-email'  =>  $this->_translator->trans('common.email.valid', [], 'validators'),
                ]
            ])
            ->add('phoneNumber', TextType::class, [
                'required' => FALSE,
                'label'    => 'employee.phone_number.label',
                'attr'     => [
                    'placeholder' => 'employee.phone_number.placeholder'
                ]
            ])
            ->add('skypeName', TextType::class, [
                'required' => FALSE,
                'label'    => 'employee.skype_name.label',
                'attr'     => [
                    'placeholder' => 'employee.skype_name.placeholder'
                ]
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $employee = $event->getData();

                $form = $event->getForm();

                if( $employee && $employee->getId() !== NULL )
                {
                    $form
                        ->add('employeeGroup', TextType::class, [
                            'required'   => FALSE,
                            'disabled'   => TRUE,
                            'data_class' => 'AppBundle\Entity\Employee\EmployeeGroup',
                            'label'      => 'employee.employee_group.label',
                            'attr'       => [
                                'readonly' => TRUE,
                            ],
                        ])
                        ->add('password', RepeatedType::class, [
                            'type'           => PasswordType::class,
                            'required'       => FALSE,
                            'first_name'     => "password",
                            'second_name'    => "password_confirm",
                            'first_options'  => [
                                'label' => 'employee.password.label',
                                'attr'  => [
                                    'placeholder'         => 'employee.password.placeholder',
                                    'data-rule-minlength' => 6,
                                    'data-msg-minlength'  => $this->_translator->trans('employee.password.length.min', [], 'validators'),
                                    'value'               => NULL,
                                ]
                            ],
                            'second_options' => [
                                'label' => 'employee.password_confirm.label',
                                'attr'  => [
                                    'placeholder' => 'employee.password_confirm.placeholder',
                                    'value'       => NULL,
                                ]
                            ]
                        ])
                    ;

                    if( $this->updateSystemAccess ) {
                        $form
                            ->add('isEnabled', CheckboxType::class, [
                                'required' => FALSE,
                                'label'    => 'employee.is_enabled.label'
                            ])
                        ;
                    }

                    $form->add('update', SubmitType::class, ['label' => 'common.update.label']);

                    if( $this->boundlessAccess )
                        $form->add('update_and_return', SubmitType::class, ['label' => 'common.update_and_return.label']);
                } else {
                    $form
                        ->add('employeeGroup', EntityType::class, [
                            'class'           => 'AppBundle\Entity\Employee\EmployeeGroup',
                            'empty_data'      => 0,
                            'choice_label'    => "name",
                            'label'           => 'employee.employee_group.label',
                            'placeholder'     => 'common.choice.placeholder',
                            'invalid_message' => $this->_translator->trans('employee.employee_group.invalid_massage', [], 'validators'),
                            'query_builder'   => function(EmployeeGroupRepository $repository) {
                                return $repository->getSubordinateRolesQuery($this->boundlessAccess);
                            }
                        ])
                        ->add('password', RepeatedType::class, [
                            'type'          => PasswordType::class,
                            'required'      => TRUE,
                            'first_name'    => "password",
                            'second_name'   => "password_confirm",
                            'first_options' => [
                                'label' => 'employee.password.label',
                                'attr'  => [
                                    'placeholder'         => 'employee.password.placeholder',
                                    'data-rule-required'  => "true",
                                    'data-msg-required'   => $this->_translator->trans('employee.password.not_blank', [], 'validators'),
                                    'data-rule-minlength' => 6,
                                    'data-msg-minlength'  => $this->_translator->trans('employee.password.length.min', [], 'validators'),
                                    'value'               => NULL,
                                ]
                            ],
                            'second_options' => [
                                'label' => 'employee.password_confirm.label',
                                'attr'  => [
                                    'placeholder'        => 'employee.password_confirm.placeholder',
                                    'data-rule-required' => "true",
                                    'data-msg-required'  => $this->_translator->trans('employee.password_confirm.not_blank', [], 'validators'),
                                    'value'              => NULL,
                                ]
                            ]
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
            'data_class'               => 'AppBundle\Entity\Employee\Employee',
            'validation_groups'        => ['Employee'],
            'translation_domain'       => 'forms',
            'boundlessAccess'          => NULL,
            'updateSystemAccess'       => NULL,
            'readOrganizationAccess'   => NULL,
            'updateOrganizationAccess' => NULL,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'employee';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
