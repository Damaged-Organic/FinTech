<?php
// src/AppBundle/Form/Type/BankingMachineType.php
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
class BankingMachineType extends AbstractType
{
    /** @DI\Inject("translator") */
    public $_translator;

    private $boundlessAccess;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->boundlessAccess = $options['boundlessAccess'];

        $builder
            ->add('serial', TextType::class, [
                'label' => 'banking_machine.serial.label',
                'attr'  => [
                    'placeholder'         => 'banking_machine.serial.placeholder',
                    'data-rule-required'  => "true",
                    'data-msg-required'   => $this->_translator->trans('banking_machine.serial.not_blank', [], 'validators'),
                    'data-rule-minlength' => 1,
                    'data-msg-minlength'  => $this->_translator->trans('banking_machine.serial.length.min', [], 'validators'),
                    'data-rule-maxlength' => 16,
                    'data-msg-maxlength'  => $this->_translator->trans('banking_machine.serial.length.max', [], 'validators'),
                ]
            ])
            ->add('login', TextType::class, [
                'required' => FALSE,
                'label'    => 'banking_machine.login.label',
                'attr'     => [
                    'placeholder'         => 'banking_machine.login.placeholder',
                    'data-rule-minlength' => 1,
                    'data-msg-minlength'  => $this->_translator->trans('banking_machine.login.length.min', [], 'validators'),
                    'data-rule-maxlength' => 16,
                    'data-msg-maxlength'  => $this->_translator->trans('banking_machine.login.length.max', [], 'validators'),
                ]
            ])
            ->add('password', TextType::class, [
                'required' => FALSE,
                'label'    => 'banking_machine.password.label',
                'attr'     => [
                    'placeholder'         => 'banking_machine.password.placeholder',
                    'data-rule-minlength' => 8,
                    'data-msg-minlength'  => $this->_translator->trans('banking_machine.password.length.min', [], 'validators'),
                    'data-rule-maxlength' => 64,
                    'data-msg-maxlength'  => $this->_translator->trans('banking_machine.password.length.max', [], 'validators'),
                ]
            ])
            ->add('organization', EntityType::class, [
                'required'     => FALSE,
                'class'        => 'AppBundle\Entity\Organization\Organization',
                'choice_label' => 'name',
                'label'        => 'banking_machine.organization.label',
                'empty_value'  => 'common.choice.placeholder',
            ])
            ->add('name', TextType::class, [
                'label' => 'banking_machine.name.label',
                'attr'  => [
                    'placeholder'         => 'banking_machine.name.placeholder',
                    'data-rule-required'  => "true",
                    'data-msg-required'   => $this->_translator->trans('banking_machine.name.not_blank', [], 'validators'),
                    'data-rule-minlength' => 4,
                    'data-msg-minlength'  => $this->_translator->trans('banking_machine.name.length.min', [], 'validators'),
                    'data-rule-maxlength' => 64,
                    'data-msg-maxlength'  => $this->_translator->trans('banking_machine.name.length.max', [], 'validators'),
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'banking_machine.address.label',
                'attr'  => [
                    'placeholder'         => 'banking_machine.address.placeholder',
                    'data-rule-required'  => "true",
                    'data-msg-required'   => $this->_translator->trans('banking_machine.address.not_blank', [], 'validators'),
                    'data-rule-minlength' => 2,
                    'data-msg-minlength'  => $this->_translator->trans('banking_machine.address.length.min', [], 'validators'),
                    'data-rule-maxlength' => 500,
                    'data-msg-maxlength'  => $this->_translator->trans('banking_machine.address.length.max', [], 'validators'),
                ]
            ])
            ->add('location', TextType::class, [
                'label' => 'banking_machine.location.label',
                'attr'  => [
                    'placeholder'         => 'banking_machine.location.placeholder',
                    'data-rule-required'  => "true",
                    'data-msg-required'   => $this->_translator->trans('banking_machine.location.not_blank', [], 'validators'),
                    'data-rule-minlength' => 2,
                    'data-msg-minlength'  => $this->_translator->trans('banking_machine.location.length.min', [], 'validators'),
                    'data-rule-maxlength' => 500,
                    'data-msg-maxlength'  => $this->_translator->trans('banking_machine.location.length.max', [], 'validators'),
                ]
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $bankingMachine = $event->getData();

                $form = $event->getForm();

                if( $bankingMachine && $bankingMachine->getId() !== NULL )
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
            'data_class'         => 'AppBundle\Entity\BankingMachine\BankingMachine',
            'validation_groups'  => ['BankingMachine'],
            'translation_domain' => 'forms',
            'boundlessAccess'    => NULL,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'banking_machine';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
