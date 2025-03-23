<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('addressLine1', TextType::class, [
                'label' => 'Address Line 1',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your street address'
                ]
            ])
            ->add('addressLine2', TextType::class, [
                'label' => 'Address Line 2 (Optional)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Apartment, suite, unit, etc.'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'City',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your city'
                ]
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Postal Code',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your postal code'
                ]
            ])
            ->add('stateProvince', TextType::class, [
                'label' => 'State/Province',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your state or province'
                ]
            ])
            ->add('country', CountryType::class, [
                'label' => 'Country',
                'attr' => [
                    'class' => 'form-select'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'validation_groups' => ['Default', 'step2']
        ]);
    }
} 