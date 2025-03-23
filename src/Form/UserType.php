<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Type;

class UserType extends AbstractFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'constraints' => [
                    new NotBlank(['message' => 'Name field is required']),
                    new Length([
                        'min' => 2, 
                        'minMessage' => 'Name must be at least {{ limit }} characters long',
                        'maxMessage' => 'Name cannot be longer than {{ limit }} characters'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'required' => true,
                'mapped' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Email field is required']),
                    new Email(['message' => 'Email must be a valid email address'])
                ]
            ]);

        $builder
            ->add('phoneCountryCode', ChoiceType::class, [
                'choices' => [
                    'United States (+1)' => '+1',
                    'United Kingdom (+44)' => '+44',
                    'Canada (+1)' => '+1',
                    'France (+33)' => '+33',
                    'Germany (+49)' => '+49',
                    'Italy (+39)' => '+39',
                    'Spain (+34)' => '+34',
                    'Australia (+61)' => '+61',
                    'Japan (+81)' => '+81',
                    'China (+86)' => '+86',
                    'India (+91)' => '+91',
                    'Brazil (+55)' => '+55',
                    'Mexico (+52)' => '+52',
                    'South Korea (+82)' => '+82',
                    'Russia (+7)' => '+7',
                    'Saudi Arabia (+966)' => '+966',
                    'UAE (+971)' => '+971',
                    'Egypt (+20)' => '+20',
                    'South Africa (+27)' => '+27',
                    'Singapore (+65)' => '+65'
                ],
                'attr' => [
                    'class' => 'form-select',
                    'style' => 'width: 200px;'
                ],
                'label_attr' => ['class' => 'form-label'],
                'constraints' => [
                    new NotBlank(['message' => 'Country code is required']),
                    new Type(['type' => 'string', 'message' => 'Country code must be a string'])
                ]
            ])
            ->add('phoneNumber', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '1234567890',
                    'maxlength' => 15
                ],
                'label_attr' => ['class' => 'form-label'],
                'constraints' => [
                    new NotBlank(['message' => 'Phone number is required']),
                    new Type(['type' => 'string', 'message' => 'Phone number must be a string']),
                    new Regex([
                        'pattern' => '/^[0-9]{8,15}$/',
                        'message' => 'Phone number must be between 8 and 15 digits'
                    ])
                ]
            ])
            ->add('subscriptionType', ChoiceType::class, [
                'choices' => [
                    'Free' => 'free',
                    'Premium' => 'premium'
                ],
                'expanded' => true,
                'multiple' => false,
                'attr' => [
                    'class' => 'subscription-option'
                ],
                'label_attr' => ['class' => 'form-label'],
                'constraints' => [
                    new NotBlank(['message' => 'Subscription type is required']),
                    new Choice([
                        'choices' => ['free', 'premium'],
                        'message' => 'Subscription type must be either "free" or "premium"',
                        'strict' => true
                    ])
                ],
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'form-check-input'];
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['Default', 'step1']
        ]);
    }
} 