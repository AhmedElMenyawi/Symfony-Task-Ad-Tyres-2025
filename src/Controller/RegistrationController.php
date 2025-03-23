<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Address;
use App\Entity\Payment;
use App\Form\UserType;
use App\Form\AddressType;
use App\Form\PaymentType;
use App\Service\RegistrationStepValidator;
use App\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Psr\Log\LoggerInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RegistrationStepValidator $stepValidator,
        private EncryptionService $encryptionService,
        private LoggerInterface $logger
    ) {}

    #[Route('/register', name: 'app_register')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_register_step', ['step' => 1]);
    }

    #[Route('/register/step{step}', name: 'app_register_step', requirements: ['step' => '[1-3]'])]
    public function registerStep(Request $request, int $step): Response
    {
        $validationResult = $this->stepValidator->validateStep($step);
        
        if (!$validationResult['isValid']) {
            $this->addFlash('error', $validationResult['message']);
            if ($validationResult['redirectStep']) {
                return $this->redirectToRoute('app_register_step', ['step' => $validationResult['redirectStep']]);
            }
        }

        $formType = match($step) {
            1 => UserType::class,
            2 => AddressType::class,
            3 => PaymentType::class,
            default => throw new \InvalidArgumentException('Invalid step')
        };

        $data = match($step) {
            1 => new User(),
            2 => new Address(),
            3 => (function() {
                $payment = new Payment();
                $payment->setEncryptionService($this->encryptionService);
                return $payment;
            })(),
            default => throw new \InvalidArgumentException('Invalid step')
        };

        $form = $this->createForm($formType, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            if ($step === 1) {
                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data->getEmail()]);
                if ($existingUser) {
                    $this->addFlash('error', 'This email is already registered.');
                    return $this->render('registration/step1.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }

                // Log the form data for step 1
                $this->logger->info('Step 1 Registration Data', [
                    'name' => $data->getName(),
                    'email' => $data->getEmail(),
                    'phoneCountryCode' => $data->getPhoneCountryCode(),
                    'phoneNumber' => $data->getPhoneNumber(),
                    'subscriptionType' => $data->getSubscriptionType(),
                    'timestamp' => (new \DateTime())->format('Y-m-d H:i:s')
                ]);
            }

            $formData = match($step) {
                1 => [
                    'name' => $data->getName(),
                    'email' => $data->getEmail(),
                    'phoneCountryCode' => $data->getPhoneCountryCode(),
                    'phoneNumber' => $data->getPhoneNumber(),
                    'subscriptionType' => $data->getSubscriptionType()
                ],
                2 => [
                    'addressLine1' => $data->getAddressLine1(),
                    'city' => $data->getCity(),
                    'postalCode' => $data->getPostalCode(),
                    'country' => $data->getCountry(),
                    'stateProvince' => $data->getStateProvince()
                ],
                3 => [
                    'cardNumber' => $data->getCardNumber(),
                    'expirationDate' => $data->getExpirationDate(),
                    'cvv' => $data->getCvv()
                ],
                default => throw new \InvalidArgumentException('Invalid step')
            };

            $this->stepValidator->saveStepData($step, $formData);

            if ($step === 2) {
                // Check if user selected free subscription in step 1
                $step1Data = $this->stepValidator->getStepData(1);
                if ($step1Data['subscriptionType'] === 'free') {
                    return $this->handleFinalStep();
                }
            }

            if ($step === 3) {
                return $this->handleFinalStep();
            }

            return $this->redirectToRoute('app_register_step', ['step' => $step + 1]);
        }

        $template = match($step) {
            1 => 'registration/step1.html.twig',
            2 => 'registration/step2.html.twig',
            3 => 'registration/step3.html.twig',
            default => throw new \InvalidArgumentException('Invalid step')
        };

        return $this->render($template, [
            'form' => $form->createView(),
        ]);
    }

    private function handleFinalStep(): Response
    {
        $userData = $this->stepValidator->getStepData(1);
        $addressData = $this->stepValidator->getStepData(2);
        
        $user = new User();
        $user->setName($userData['name']);
        $user->setEmail($userData['email']);
        $user->setPhoneCountryCode($userData['phoneCountryCode']);
        $user->setPhoneNumber($userData['phoneNumber']);
        $user->setSubscriptionType($userData['subscriptionType']);

        $address = new Address();
        $address->setAddressLine1($addressData['addressLine1']);
        $address->setCity($addressData['city']);
        $address->setPostalCode($addressData['postalCode']);
        $address->setCountry($addressData['country']);
        $address->setStateProvince($addressData['stateProvince'] ?? 'N/A');
        $address->setUser($user);

        $this->entityManager->persist($user);
        $this->entityManager->persist($address);

        // Only handle payment if not a free subscription
        if ($userData['subscriptionType'] !== 'free') {
            $paymentData = $this->stepValidator->getStepData(3);
            $payment = new Payment();
            $payment->setEncryptionService($this->encryptionService);
            $payment->setCardNumber($paymentData['cardNumber']);
            $payment->setExpirationDate($paymentData['expirationDate']);
            $payment->setCvv($paymentData['cvv']);
            $payment->setUser($user);
            $this->entityManager->persist($payment);
        }

        $this->entityManager->flush();
        $this->stepValidator->clearRegistrationData();

        return $this->redirectToRoute('app_register_success');
    }

    #[Route('/register/success', name: 'app_register_success')]
    public function success(): Response
    {
        return $this->render('registration/success.html.twig');
    }
} 