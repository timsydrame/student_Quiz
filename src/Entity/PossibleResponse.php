<?php

namespace App\Entity;

use App\Repository\PossibleResponseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PossibleResponseRepository::class)]
class PossibleResponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 1000)]
    private ?string $enonce = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $imageResponse = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isCorrecte = false;

    #[ORM\ManyToOne(inversedBy: 'possibleResponses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Question $question = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnonce(): ?string
    {
        return $this->enonce;
    }

    public function setEnonce(string $enonce): static
    {
        $this->enonce = $enonce;

        return $this;
    }

    public function getImageResponse(): ?string
    {
        return $this->imageResponse;
    }

    public function setImageResponse(?string $imageResponse): static
    {
        $this->imageResponse = $imageResponse;

        return $this;
    }

    public function isCorrecte(): bool
    {
        return $this->isCorrecte;
    }

    public function setIsCorrect(bool $isCorrecte): static
    {
        $this->isCorrecte = $isCorrecte;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): static
    {
        $this->question = $question;

        return $this;
    }
}
