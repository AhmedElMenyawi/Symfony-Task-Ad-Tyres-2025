<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Service\EncryptionService;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 1024)]
    #[Assert\NotBlank(groups: ['step3'])]
    private ?string $cardNumber = null;

    #[ORM\Column(length: 1024)]
    #[Assert\NotBlank(groups: ['step3'])]
    private ?string $expirationDate = null;

    #[ORM\Column(length: 1024)]
    #[Assert\NotBlank(groups: ['step3'])]
    private ?string $cvv = null;

    #[ORM\OneToOne(inversedBy: 'payment')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    private ?EncryptionService $encryptionService = null;
    private ?string $rawCardNumber = null;
    private ?string $rawExpirationDate = null;
    private ?string $rawCvv = null;

    public function setEncryptionService(EncryptionService $encryptionService): void
    {
        $this->encryptionService = $encryptionService;
    }

    public static function validateCardNumber($value, \Symfony\Component\Validator\Context\ExecutionContextInterface $context): void
    {
        if ($value === null) {
            return;
        }

        // Remove any spaces from the input
        $value = str_replace(' ', '', $value);
        
        if (!preg_match('/^[0-9]{16}$/', $value)) {
            $context->buildViolation('Card number must be exactly 16 digits')
                ->addViolation();
        }
    }

    public static function validateCvv($value, \Symfony\Component\Validator\Context\ExecutionContextInterface $context): void
    {
        if ($value === null) {
            return;
        }

        if (!preg_match('/^[0-9]{3}$/', $value)) {
            $context->buildViolation('CVV must be exactly 3 digits')
                ->addViolation();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCardNumber(): ?string
    {
        if ($this->cardNumber === null || $this->encryptionService === null) {
            return null;
        }
        return $this->encryptionService->decrypt($this->cardNumber);
    }

    public function getMaskedCardNumber(): ?string
    {
        if ($this->cardNumber === null || $this->encryptionService === null) {
            return null;
        }
        return $this->encryptionService->maskCardNumber($this->encryptionService->decrypt($this->cardNumber));
    }

    public function setCardNumber(string $cardNumber): static
    {
        // Store raw data for validation
        $this->rawCardNumber = $cardNumber;
        
        // If validation passes, encrypt and store
        if ($this->encryptionService === null) {
            throw new \RuntimeException('EncryptionService not set');
        }
        $this->cardNumber = $this->encryptionService->encrypt($cardNumber);
        return $this;
    }

    public function getExpirationDate(): ?string
    {
        if ($this->expirationDate === null || $this->encryptionService === null) {
            return null;
        }
        return $this->encryptionService->decrypt($this->expirationDate);
    }

    public function getMaskedExpirationDate(): ?string
    {
        if ($this->expirationDate === null || $this->encryptionService === null) {
            return null;
        }
        return $this->encryptionService->maskExpirationDate($this->encryptionService->decrypt($this->expirationDate));
    }

    public function setExpirationDate(string $expirationDate): static
    {
        // Store raw data for validation
        $this->rawExpirationDate = $expirationDate;
        
        // If validation passes, encrypt and store
        if ($this->encryptionService === null) {
            throw new \RuntimeException('EncryptionService not set');
        }
        $this->expirationDate = $this->encryptionService->encrypt($expirationDate);
        return $this;
    }

    public function getCvv(): ?string
    {
        if ($this->cvv === null || $this->encryptionService === null) {
            return null;
        }
        return $this->encryptionService->decrypt($this->cvv);
    }

    public function getMaskedCvv(): ?string
    {
        if ($this->cvv === null || $this->encryptionService === null) {
            return null;
        }
        return $this->encryptionService->maskCvv($this->encryptionService->decrypt($this->cvv));
    }

    public function setCvv(string $cvv): static
    {
        // Store raw data for validation
        $this->rawCvv = $cvv;
        
        // If validation passes, encrypt and store
        if ($this->encryptionService === null) {
            throw new \RuntimeException('EncryptionService not set');
        }
        $this->cvv = $this->encryptionService->encrypt($cvv);
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getRawCardNumber(): ?string
    {
        return $this->rawCardNumber;
    }

    public function setRawCardNumber(?string $rawCardNumber): self
    {
        $this->rawCardNumber = $rawCardNumber;
        return $this;
    }

    public function getRawExpirationDate(): ?string
    {
        return $this->rawExpirationDate;
    }

    public function setRawExpirationDate(?string $rawExpirationDate): self
    {
        $this->rawExpirationDate = $rawExpirationDate;
        return $this;
    }

    public function getRawCvv(): ?string
    {
        return $this->rawCvv;
    }

    public function setRawCvv(?string $rawCvv): self
    {
        $this->rawCvv = $rawCvv;
        return $this;
    }
} 