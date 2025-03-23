<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class RegistrationStepValidator
{
    private SessionInterface $session;

    public function __construct(
        RequestStack $requestStack
    ) {
        $this->session = $requestStack->getSession();
    }

    private const REQUIRED_STEPS = [
        1 => ['name', 'email', 'phoneCountryCode', 'phoneNumber', 'subscriptionType'],
        2 => ['addressLine1', 'city', 'postalCode', 'country'],
        3 => ['cardNumber', 'expirationDate', 'cvv']
    ];

    public function validateStep(int $step): array
    {
        $result = ['isValid' => true, 'message' => null, 'redirectStep' => null];

        if ($step === 1) {
            return $result;
        }

        $previousStepData = $this->getStepData($step - 1);
        if (!$previousStepData) {
            $result['isValid'] = false;
            $result['message'] = 'Please complete the previous step first.';
            $result['redirectStep'] = $step - 1;
            return $result;
        }

        // For step 3, check if user selected free subscription
        if ($step === 3) {
            $step1Data = $this->getStepData(1);
            if ($step1Data && $step1Data['subscriptionType'] === 'free') {
                $result['isValid'] = false;
                $result['message'] = 'Payment information is not required for free subscriptions.';
                $result['redirectStep'] = 2;
                return $result;
            }
        }

        return $result;
    }

    public function isStepCompleted(int $step): bool
    {
        $stepData = $this->session->get('registration_step_' . $step, []);
        return $this->validateStepData($step, $stepData, false);
    }

    private function validateStepData(int $step, array $data, bool $throwException = true): bool
    {
        if (!isset(self::REQUIRED_STEPS[$step])) {
            return true;
        }

        $missingFields = [];
        foreach (self::REQUIRED_STEPS[$step] as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missingFields[] = $field;
            }
        }

        return empty($missingFields);
    }

    public function saveStepData(int $step, array $data): void
    {
        $this->session->set('registration_step_' . $step, $data);
    }

    public function getStepData(int $step): array
    {
        return $this->session->get('registration_step_' . $step, []);
    }

    public function clearRegistrationData(): void
    {
        foreach (array_keys(self::REQUIRED_STEPS) as $step) {
            $this->session->remove('registration_step_' . $step);
        }
    }
} 