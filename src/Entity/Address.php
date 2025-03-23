<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['step2'])]
    #[Assert\Length(max: 255, groups: ['step2'])]
    private ?string $addressLine1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, groups: ['step2'])]
    private ?string $addressLine2 = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(groups: ['step2'])]
    #[Assert\Length(max: 100, groups: ['step2'])]
    private ?string $city = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(groups: ['step2'])]
    #[Assert\Length(max: 20, groups: ['step2'])]
    private ?string $postalCode = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(groups: ['step2'])]
    #[Assert\Length(max: 100, groups: ['step2'])]
    private ?string $stateProvince = null;

    #[ORM\Column(length: 2)]
    #[Assert\NotBlank(groups: ['step2'])]
    #[Assert\Country(groups: ['step2'])]
    private ?string $country = null;

    #[ORM\OneToOne(inversedBy: 'address')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function setAddressLine1(string $addressLine1): static
    {
        $this->addressLine1 = $addressLine1;
        return $this;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function setAddressLine2(?string $addressLine2): static
    {
        $this->addressLine2 = $addressLine2;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getStateProvince(): ?string
    {
        return $this->stateProvince;
    }

    public function setStateProvince(string $stateProvince): static
    {
        $this->stateProvince = $stateProvince;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;
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
} 