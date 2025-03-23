<?php

namespace App\Form;

use App\Entity\PaymentInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cardNumber', TextType::class, [
                'label' => 'Credit Card Number',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your card number',
                    'data-controller' => 'payment',
                    'data-action' => 'input->payment#formatCardNumber'
                ]
            ])
            ->add('expirationDate', TextType::class, [
                'label' => 'Expiration Date',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'MM/YYYY',
                    'data-controller' => 'payment',
                    'data-action' => 'input->payment#formatExpirationDate'
                ]
            ])
            ->add('cvv', TextType::class, [
                'label' => 'CVV',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter CVV',
                    'maxlength' => 4,
                    'data-controller' => 'payment',
                    'data-action' => 'input->payment#validateCvv'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PaymentInfo::class,
            'validation_groups' => ['Default']
        ]);
    }
} 