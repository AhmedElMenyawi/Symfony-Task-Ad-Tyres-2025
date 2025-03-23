<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\EntityManagerInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'This email is already registered')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['step1'])]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Name must be at least {{ limit }} characters long',
        maxMessage: 'Name cannot be longer than {{ limit }} characters',
        groups: ['step1']
    )]
    private ?string $name = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Please enter your email', groups: ['step1'])]
    #[Assert\Email(message: 'Please enter a valid email address', groups: ['step1'])]
    private ?string $email = null;

    #[ORM\Column(length: 5)]
    #[Assert\NotBlank(message: 'Please enter your country code', groups: ['step1'])]
    #[Assert\Regex(
        pattern: '/^\+[0-9]{1,4}$/',
        message: 'Please enter a valid country code (e.g., +1, +44)',
        groups: ['step1']
    )]
    private ?string $phoneCountryCode = null;

    #[ORM\Column(length: 15)]
    #[Assert\NotBlank(message: 'Please enter your phone number', groups: ['step1'])]
    #[Assert\Regex(
        pattern: '/^[0-9]{8,15}$/',
        message: 'Please enter a valid phone number (8-15 digits)',
        groups: ['step1']
    )]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(groups: ['step1'])]
    #[Assert\Choice(['free', 'premium'], groups: ['step1'])]
    private ?string $subscriptionType = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Address $address = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Payment $payment = null;

    #[ORM\OneToOne(inversedBy: 'user', targetEntity: PaymentInfo::class)]
    private ?PaymentInfo $paymentInfo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPhoneCountryCode(): ?string
    {
        return $this->phoneCountryCode;
    }

    public function setPhoneCountryCode(string $phoneCountryCode): static
    {
        $this->phoneCountryCode = $phoneCountryCode;
        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getFullPhoneNumber(): string
    {
        return $this->phoneCountryCode . $this->phoneNumber;
    }

    public function getSubscriptionType(): ?string
    {
        return $this->subscriptionType;
    }

    public function setSubscriptionType(string $subscriptionType): static
    {
        $this->subscriptionType = $subscriptionType;
        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): static
    {
        if ($address === null && $this->address !== null) {
            $this->address->setUser(null);
        }
        if ($address !== null && $address->getUser() !== $this) {
            $address->setUser($this);
        }
        $this->address = $address;
        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): static
    {
        if ($payment === null && $this->payment !== null) {
            $this->payment->setUser(null);
        }
        if ($payment !== null && $payment->getUser() !== $this) {
            $payment->setUser($this);
        }
        $this->payment = $payment;
        return $this;
    }

    public function getPaymentInfo(): ?PaymentInfo
    {
        return $this->paymentInfo;
    }

    public function setPaymentInfo(?PaymentInfo $paymentInfo): static
    {
        $this->paymentInfo = $paymentInfo;
        return $this;
    }

    public static function isEmailRegistered(EntityManagerInterface $entityManager, string $email): bool
    {
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        return $existingUser !== null;
    }
} 