<?php
// AppBundle/Form/Type/EmployeeType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\OptionsResolver\OptionsResolver,
    Symfony\Component\Translation\TranslatorInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType,
    Symfony\Component\Form\Extension\Core\Type\EmailType,
    Symfony\Component\Form\Extension\Core\Type\PasswordType,
    Symfony\Component\Form\Extension\Core\Type\RepeatedType,
    Symfony\Component\Form\Extension\Core\Type\CheckboxType,
    Symfony\Component\Form\Extension\Core\Type\SubmitType,
    Symfony\Bridge\Doctrine\Form\Type\EntityType;

use AppBundle\Entity\Employee\Repository\EmployeeGroupRepository;

class EmployeeType extends AbstractType
{
    private $_translator;

    private $boundlessAccess;
    private $boundedAccess;

    public function __construct(TranslatorInterface $translator, $boundlessAccess, $boundedAccess = NULL)
    {
        $this->_translator = $translator;

        /*
         * TRICKY: $this->boundlessAccess is a string containing exact user role,
         * which also equals TRUE during loose (==) authorization check
         */
        $this->boundlessAccess = $boundlessAccess;
        $this->boundedAccess   = $boundedAccess;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $employee = $event->getData();

                $form = $event->getForm();

                if( ($employee && $employee->getId() === NULL && $this->boundlessAccess) ||
                    ($employee && $employee->getId() !== NULL && $this->boundedAccess)) {
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
            });

        $builder
            ->add('name', TextType::class, [
                'required' => FALSE,
                'label'    => 'employee.name.label',
                'attr'     => [
                    'placeholder' => 'employee.name.placeholder'
                ]
            ])
            ->add('surname', TextType::class, [
                'required' => FALSE,
                'label'    => 'employee.surname.label',
                'attr'     => [
                    'placeholder' => 'employee.surname.placeholder'
                ]
            ])
            ->add('patronymic', TextType::class, [
                'required' => FALSE,
                'label'    => 'employee.patronymic.label',
                'attr'     => [
                    'placeholder' => 'employee.patronymic.placeholder'
                ]
            ])
            ->add('email', EmailType::class, [
                'required' => FALSE,
                'label'    => 'employee.email.label',
                'attr'     => [
                    'placeholder' => 'employee.email.placeholder'
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

                    if( $this->boundedAccess ) {
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
                            'query_builder'   => function (EmployeeGroupRepository $repository) {
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
            'data_class'         => 'AppBundle\Entity\Employee\Employee',
            'translation_domain' => 'forms'
        ]);
    }

    public function getBlockPrefix()
    {
        return 'employee';
    }
}
