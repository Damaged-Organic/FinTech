<?php
// src/AppBundle/Form/Type/AccountType.php
namespace AppBundle\Form\Type;

use DateTime;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\IntegerType,
    Symfony\Component\Form\Extension\Core\Type\TextType,
    Symfony\Component\Form\Extension\Core\Type\NumberType,
    Symfony\Component\Form\Extension\Core\Type\CheckboxType,
    Symfony\Component\Form\Extension\Core\Type\DateType,
    Symfony\Component\Form\Extension\Core\Type\SubmitType,
    Symfony\Bridge\Doctrine\Form\Type\EntityType;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Entity\Account\Account,
    AppBundle\Entity\Account\Repository\AccountGroupRepository;

/**
 * @DI\FormType
 */
class AccountType extends AbstractType
{
    /** @DI\Inject("translator") */
    public $_translator;

    /** @DI\Inject("security.token_storage") */
    public $_tokenStorage;

    private $boundlessReadAccess;

    private $boundlessUpdateAccountGroupAccess;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->boundlessReadAccess               = $options['boundlessReadAccess'];
        $this->boundlessUpdateAccountGroupAccess = $options['boundlessUpdateAccountGroupAccess'];

        $builder
            ->add('name', TextType::class, [
                'label' => 'account.name.label',
                'attr'  => [
                    'placeholder'         => 'account.name.placeholder',
                    'data-rule-required'  => "true",
                    'data-msg-required'   => $this->_translator->trans('account.name.not_blank', [], 'validators'),
                    'data-rule-minlength' => 2,
                    'data-msg-minlength'  => $this->_translator->trans('account.name.length.min', [], 'validators'),
                    'data-rule-maxlength' => 100,
                    'data-msg-maxlength'  => $this->_translator->trans('account.name.length.max', [], 'validators'),
                ]
            ])
            ->add('percent', NumberType::class, [
                'required' => FALSE,
                'scale'    => 2,
                'label'    => 'account.percent.label',
                'attr'     => [
                    'placeholder'        => 'account.percent.placeholder',
                    'data-rule-required' => "true",
                    'data-rule-min'      => 0.00,
                    'data-msg-min'       => $this->_translator->trans('account.percent.range.min', [], 'validators'),
                    'data-rule-max'      => 100.00,
                    'data-msg-max'       => $this->_translator->trans('account.percent.range.max', [], 'validators'),
                    'data-mask'          => "#0.00",
                    'data-mask-reverse'  => "true",
                ]
            ])
            ->add('mfoOfBankA', NumberType::class, [
                'label'      => 'account.mfo_of_bank_a.label',
                'empty_data' => NULL,
                'attr'       => [
                    'placeholder'         => 'account.mfo_of_bank_a.placeholder',
                    'data-rule-required'  => "true",
                    'data-msg-required'   => $this->_translator->trans('account.mfo_of_bank_a.not_blank', [], 'validators'),
                    'data-rule-maxlength' => Account::MFO_OF_BANK_A_LENGTH,
                    'data-msg-maxlength'  => $this->_translator->trans('account.mfo_of_bank_a.length.max', [], 'validators'),
                ]
            ])
            ->add('personalAccountOfBankA', TextType::class, [
                'label'      => 'account.personal_account_of_bank_a.label',
                'empty_data' => NULL,
                'attr'       => [
                    'placeholder'         => 'account.personal_account_of_bank_a.placeholder',
                    'data-rule-required'  => "true",
                    'data-msg-required'   => $this->_translator->trans('account.personal_account_of_bank_a.not_blank', [], 'validators'),
                    'data-rule-maxlength' => Account::PERSONAL_ACCOUNT_OF_BANK_A_LENGTH,
                    'data-msg-maxlength'  => $this->_translator->trans('account.personal_account_of_bank_a.length.max', [], 'validators'),
                ]
            ])
            ->add('mfoOfBankB', IntegerType::class, [
                'label'      => 'account.mfo_of_bank_b.label',
                'empty_data' => NULL,
                'attr'       => [
                    'placeholder'         => 'account.mfo_of_bank_b.placeholder',
                    'data-rule-required'  => "true",
                    'data-msg-required'   => $this->_translator->trans('account.mfo_of_bank_b.not_blank', [], 'validators'),
                    'data-rule-maxlength' => Account::MFO_OF_BANK_B_LENGTH,
                    'data-msg-maxlength'  => $this->_translator->trans('account.mfo_of_bank_b.length.max', [], 'validators'),
                ]
            ])
            ->add('personalAccountOfBankB', TextType::class, [
                'label'      => 'account.personal_account_of_bank_b.label',
                'empty_data' => NULL,
                'attr'       => [
                    'placeholder'         => 'account.personal_account_of_bank_b.placeholder',
                    'data-rule-required'  => "true",
                    'data-msg-required'   => $this->_translator->trans('account.personal_account_of_bank_b.not_blank', [], 'validators'),
                    'data-rule-maxlength' => Account::PERSONAL_ACCOUNT_OF_BANK_B_LENGTH,
                    'data-msg-maxlength'  => $this->_translator->trans('account.personal_account_of_bank_b.length.max', [], 'validators'),
                ]
            ])
            ->add('debitCreditPaymentFlag', CheckboxType::class, [
                'required' => FALSE,
                'label'    => 'account.debit_credit_payment_flag.label'
            ])
            ->add('paymentAmount', NumberType::class, [
                'label'      => 'account.payment_amount.label',
                'empty_data' => NULL,
                'attr'       => [
                    'placeholder'         => 'account.payment_amount.placeholder',
                    'data-rule-required'  => "true",
                    'data-msg-required'   => $this->_translator->trans('account.payment_amount.not_blank', [], 'validators'),
                    'data-rule-maxlength' => Account::PAYMENT_AMOUNT_LENGTH,
                    'data-msg-maxlength'  => $this->_translator->trans('account.payment_amount.length.max', [], 'validators'),
                ]
            ])
            ->add('paymentDocumentType', IntegerType::class, [
                'label'      => 'account.payment_document_type.label',
                'empty_data' => NULL,
                'attr'       => [
                    'placeholder'         => 'account.payment_document_type.placeholder',
                    'data-rule-required'  => "true",
                    'data-msg-required'   => $this->_translator->trans('account.payment_document_type.not_blank', [], 'validators'),
                    'data-rule-maxlength' => Account::PAYMENT_DOCUMENT_TYPE_LENGTH,
                    'data-msg-maxlength'  => $this->_translator->trans('account.payment_document_type.length.max', [], 'validators'),
                ]
            ])
            ->add('paymentOperationalNumber', IntegerType::class, [
                'label'       => 'account.payment_operational_number.label',
                'required'    => FALSE,
                'empty_data'  => '',
                'attr'        => [
                    'placeholder'         => 'account.payment_operational_number.placeholder',
                    'data-rule-maxlength' => Account::PAYMENT_OPERATIONAL_NUMBER_LENGTH,
                    'data-msg-maxlength'  => $this->_translator->trans('account.payment_operational_number.length.max', [], 'validators'),
                ]
            ])
            ->add('paymentCurrency', IntegerType::class, [
                'label'      => 'account.payment_currency.label',
                'empty_data' => NULL,
                'attr'       => [
                    'placeholder'         => 'account.payment_currency.placeholder',
                    'data-rule-required'  => "true",
                    'data-msg-required'   => $this->_translator->trans('account.payment_currency.not_blank', [], 'validators'),
                    'data-rule-maxlength' => Account::PAYMENT_CURRENCY_LENGTH,
                    'data-msg-maxlength'  => $this->_translator->trans('account.payment_currency.length.max', [], 'validators'),
                ]
            ])
            ->add('paymentDocumentDate', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'data'   => new DateTime(),
                'label'  => 'account.payment_document_date.label',
                'attr'   => [
                    'placeholder'        => 'account.payment_document_date.placeholder',
                    'data-rule-required' => "true",
                    'data-msg-required'  => $this->_translator->trans('account.payment_document_date.not_blank', [], 'validators'),
                ]
            ])
            ->add('paymentDocumentArrivalDateToBankA', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'data'   => new DateTime(),
                'label'  => 'account.payment_document_arrival_date_to_bank_a.label',
                'attr'   => [
                    'placeholder'        => 'account.payment_document_arrival_date_to_bank_a.placeholder',
                    'data-rule-required' => "true",
                    'data-msg-required'  => $this->_translator->trans('account.payment_document_arrival_date_to_bank_a.not_blank', [], 'validators'),
                ]
            ])
            ->add('payerNameOfClientA', TextType::class, [
                'label' => 'account.payer_name_of_client_a.label',
                'attr'  => [
                    'placeholder'         => 'account.payer_name_of_client_a.placeholder',
                    'data-rule-maxlength' => Account::PAYER_NAME_OF_CLIENT_A_LENGTH,
                    'data-msg-maxlength'  => $this->_translator->trans('account.payer_name_of_client_a.length.max', [], 'validators'),
                ]
            ])
            ->add('payerNameOfClientB', TextType::class, [
                'label' => 'account.payer_name_of_client_b.label',
                'attr'  => [
                    'placeholder'         => 'account.payer_name_of_client_b.placeholder',
                    'data-rule-maxlength' => Account::PAYER_NAME_OF_CLIENT_B_LENGTH,
                    'data-msg-maxlength'  => $this->_translator->trans('account.payer_name_of_client_b.length.max', [], 'validators'),
                ]
            ])
            ->add('paymentDestination', TextType::class, [
                'label' => 'account.payment_destination.label',
                'attr'  => [
                    'placeholder'         => 'account.payment_destination.placeholder',
                    'data-rule-required'  => "true",
                    'data-msg-required'   => $this->_translator->trans('account.payment_destination.not_blank', [], 'validators'),
                    'data-rule-maxlength' => Account::PAYMENT_DESTINATION_LENGTH,
                    'data-msg-maxlength'  => $this->_translator->trans('account.payment_destination.length.max', [], 'validators'),
                ]
            ])
            ->add('supportingProps', TextType::class, [
                'label'    => 'account.supporting_props.label',
                'required' => FALSE,
                'attr'     => [
                    'placeholder'         => 'account.supporting_props.placeholder',
                    'data-rule-maxlength' => Account::SUPPORTING_PROPS_LENGTH,
                    'data-msg-maxlength'  => $this->_translator->trans('account.supporting_props.length.max', [], 'validators'),
                ]
            ])
            ->add('paymentDestinationCode', TextType::class, [
                'label'    => 'account.payment_destination_code.label',
                'required' => FALSE,
                'attr'     => [
                    'placeholder'         => 'account.payment_destination_code.placeholder',
                    'data-rule-maxlength' => Account::PAYMENT_DESTINATION_CODE_LENGTH,
                    'data-msg-maxlength'  => $this->_translator->trans('account.payment_destination_code.length.max', [], 'validators'),
                ]
            ])
            ->add('stringsNumberInBlock', TextType::class, [
                'label'    => 'account.strings_number_in_block.label',
                'required' => FALSE,
                'attr'     => [
                    'placeholder'         => 'account.strings_number_in_block.placeholder',
                    'data-rule-maxlength' => Account::STRINGS_NUMBER_IN_BLOCK_LENGTH,
                    'data-msg-maxlength'  => $this->_translator->trans('account.strings_number_in_block.length.max', [], 'validators'),
                ]
            ])
            ->add('clientIdentifierA', TextType::class, [
                'label'    => 'account.client_identifier_a.label',
                'required' => FALSE,
                'attr'     => [
                    'placeholder'         => 'account.client_identifier_a.placeholder',
                    'data-rule-maxlength' => Account::CLIENT_IDENTIFIER_A_LENGTH,
                    'data-msg-maxlength'  => $this->_translator->trans('account.client_identifier_a.length.max', [], 'validators'),
                ]
            ])
            ->add('clientIdentifierB', TextType::class, [
                'label'    => 'account.client_identifier_b.label',
                'required' => FALSE,
                'attr'     => [
                    'placeholder'         => 'account.client_identifier_b.placeholder',
                    'data-rule-maxlength' => Account::CLIENT_IDENTIFIER_B_LENGTH,
                    'data-msg-maxlength'  => $this->_translator->trans('account.client_identifier_b.length.max', [], 'validators'),
                ]
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $operator       = $event->getData();
                $operatorExists = ($operator && $operator->getId() !== NULL);

                $form = $event->getForm();

                if( $operatorExists )
                {
                    $form->add('update', SubmitType::class, ['label' => 'common.update.label']);

                    if( $this->boundlessReadAccess )
                        $form->add('update_and_return', SubmitType::class, ['label' => 'common.update_and_return.label']);
                } else {
                    $form->add('create', SubmitType::class, ['label' => 'common.create.label']);

                    if( $this->boundlessReadAccess )
                        $form->add('create_and_return', SubmitType::class, ['label' => 'common.create_and_return.label']);
                }
            })
        ;

        // $builder
        //     ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
        //     {
        //         $form = $event->getForm();
        //
        //         if( $this->boundlessUpdateAccountGroupAccess )
        //         {
        //             $form
        //                 ->add('accountGroup', EntityType::class, [
        //                     'class'           => 'AppBundle\Entity\Account\AccountGroup',
        //                     'empty_data'      => 0,
        //                     'choice_label'    => 'name',
        //                     'label'           => 'account.account_group.label',
        //                     'placeholder'     => 'common.choice.placeholder',
        //                     'invalid_message' => $this->_translator->trans('account.account_group.invalid_massage', [], 'validators')
        //                 ])
        //             ;
        //         } else {
        //             $user = $this->_tokenStorage->getToken()->getUser();
        //
        //             if( $user && $user->getOrganization() )
        //             {
        //                 $form
        //                     ->add('accountGroup', EntityType::class, [
        //                         'class'           => 'AppBundle\Entity\Account\AccountGroup',
        //                         'empty_data'      => 0,
        //                         'choice_label'    => 'name',
        //                         'label'           => 'account.account_group.label',
        //                         'placeholder'     => 'common.choice.placeholder',
        //                         'invalid_message' => $this->_translator->trans('account.account_group.invalid_massage', [], 'validators'),
        //                         'query_builder'   => function(AccountGroupRepository $repository) use($user) {
        //                             $name = $user->getOrganization()->getName();
        //
        //                             return $repository->getManagedGroupsQuery($name);
        //                         }
        //                     ])
        //                 ;
        //             }
        //         }
        //     })
        // ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'                  => 'AppBundle\Entity\Account\Account',
            'translation_domain'          => 'forms',
            'boundlessReadAccess'             => NULL,
            'boundlessUpdateAccountGroupAccess' => NULL,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'account';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
