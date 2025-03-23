<?php

namespace App\Form;

use App\Entity\Payment;
use App\Form\EventSubscriber\PaymentFormSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentType extends AbstractType
{
    public function __construct(
        private PaymentFormSubscriber $paymentFormSubscriber
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cardNumber', TextType::class, [
                'label' => 'Card Number',
                'attr' => [
                    'placeholder' => '1234 5678 9012 3456',
                    'maxlength' => 16
                ]
            ])
            ->add('expirationDate', TextType::class, [
                'label' => 'Expiration Date (MM/YY)',
                'attr' => [
                    'placeholder' => 'MM/YY',
                    'maxlength' => 5
                ]
            ])
            ->add('cvv', TextType::class, [
                'label' => 'CVV',
                'attr' => [
                    'placeholder' => '123',
                    'maxlength' => 4
                ]
            ])
            ->addEventSubscriber($this->paymentFormSubscriber)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Payment::class,
            'validation_groups' => ['Default', 'step3']
        ]);
    }
} 