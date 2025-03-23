<?php

namespace App\Entity;

use App\Repository\PaymentInfoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PaymentInfoRepository::class)]
class PaymentInfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 1024)]
    #[Assert\NotBlank]
    #[Assert\CardScheme(schemes: ['VISA', 'MASTERCARD', 'AMEX'])]
    private ?string $cardNumber = null;

    #[ORM\Column(length: 1024)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^(0[1-9]|1[0-2])\/([0-9]{4})$/', message: 'Expiration date must be in MM/YYYY format')]
    private ?string $expirationDate = null;

    #[ORM\Column(length: 1024)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 4)]
    #[Assert\Regex(pattern: '/^[0-9]{3,4}$/', message: 'CVV must be 3 or 4 digits')]
    private ?string $cvv = null;

    #[ORM\OneToOne(mappedBy: 'paymentInfo', targetEntity: User::class)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCardNumber(): ?string
    {
        return $this->cardNumber;
    }

    public function setCardNumber(string $cardNumber): static
    {
        $this->cardNumber = $cardNumber;
        return $this;
    }

    public function getExpirationDate(): ?string
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(string $expirationDate): static
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    public function getCvv(): ?string
    {
        return $this->cvv;
    }

    public function setCvv(string $cvv): static
    {
        $this->cvv = $cvv;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setPaymentInfo(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getPaymentInfo() !== $this) {
            $user->setPaymentInfo($this);
        }

        $this->user = $user;
        return $this;
    }

    public function getObfuscatedCardNumber(): string
    {
        $length = strlen($this->cardNumber);
        return str_repeat('*', $length - 4) . substr($this->cardNumber, -4);
    }
} 