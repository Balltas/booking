<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as BookingValidator;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\Date]
    #[BookingValidator\FutureDate]
    private string $date_from;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\Date]
    #[BookingValidator\FutureDate]
    private string $date_to;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateFrom(): string
    {
        return $this->date_from;
    }

    public function setDateFrom(string $date_from): self
    {
        $this->date_from = $date_from;

        return $this;
    }

    public function getDateTo(): string
    {
        return $this->date_to;
    }

    public function setDateTo(string $date_to): self
    {
        $this->date_to = $date_to;

        return $this;
    }
}
