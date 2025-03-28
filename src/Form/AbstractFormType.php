<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

abstract class AbstractFormType extends AbstractType
{
    public function __construct(
        private CsrfTokenManagerInterface $csrfTokenManager
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_token', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, [
                'mapped' => false,
                'attr' => [
                    'value' => $this->getCsrfToken()
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'authenticate'
        ]);
    }

    private function getCsrfToken(): string
    {
        return $this->csrfTokenManager->getToken('authenticate')->getValue();
    }
} 