<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Product
{
    private ?int $id;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    #[Assert\GreaterThanOrEqual(0)]
    private ?int $quantity = null;

    public function __construct(?int $id = null)
    {
        $this->id = $id;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'quantity' => $this->quantity,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
