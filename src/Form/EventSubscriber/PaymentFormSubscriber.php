<?php

namespace App\Form\EventSubscriber;

use App\Entity\Payment;
use App\Service\EncryptionService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaymentFormSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EncryptionService $encryptionService,
        private ValidatorInterface $validator
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    public function onPreSubmit(FormEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();
        $payment = $form->getData();

        if (!$payment instanceof Payment) {
            return;
        }

        // Store raw data for validation
        if (isset($data['cardNumber'])) {
            $payment->setRawCardNumber($data['cardNumber']);
            // Validate card number
            if (!preg_match('/^[0-9]{16}$/', str_replace(' ', '', $data['cardNumber']))) {
                $form->addError(new \Symfony\Component\Form\FormError('Card number must be exactly 16 digits'));
            }
        }

        if (isset($data['expirationDate'])) {
            $payment->setRawExpirationDate($data['expirationDate']);
            // Validate expiration date
            if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $data['expirationDate'], $matches)) {
                $form->addError(new \Symfony\Component\Form\FormError('Invalid expiration date format (MM/YY)'));
            } else {
                $month = (int)$matches[1];
                $year = (int)("20" . $matches[2]);
                $expirationDate = \DateTime::createFromFormat('Y-m', sprintf('%04d-%02d', $year, $month));
                $now = new \DateTime();
                if ($expirationDate < $now) {
                    $form->addError(new \Symfony\Component\Form\FormError('Expiration date must be in the future'));
                }
            }
        }

        if (isset($data['cvv'])) {
            $payment->setRawCvv($data['cvv']);
            // Validate CVV
            if (!preg_match('/^[0-9]{3}$/', $data['cvv'])) {
                $form->addError(new \Symfony\Component\Form\FormError('CVV must be exactly 3 digits'));
            }
        }

        if ($form->getErrors(true)->count() > 0) {
            $event->stopPropagation();
        }
    }

    public function onPostSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $payment = $form->getData();

        if (!$payment instanceof Payment) {
            return;
        }

        // Only encrypt if the form is valid
        if ($form->isValid()) {
            $payment->setEncryptionService($this->encryptionService);
            
            // Encrypt the raw data
            if ($payment->getRawCardNumber()) {
                $payment->setCardNumber($payment->getRawCardNumber());
            }
            if ($payment->getRawExpirationDate()) {
                $payment->setExpirationDate($payment->getRawExpirationDate());
            }
            if ($payment->getRawCvv()) {
                $payment->setCvv($payment->getRawCvv());
            }
        }
    }
} 