<?php

namespace App\Entity;

use App\Repository\CandidateResponseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidateResponseRepository::class)]
class CandidateResponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 1000)]
    private ?string $enoncer = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageResponse = null;

    #[ORM\Column]
    private ?bool $iscorrect = null;

    #[ORM\ManyToOne(inversedBy: 'candidateResponses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Question $question = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnoncer(): ?string
    {
        return $this->enoncer;
    }

    public function setEnoncer(string $enoncer): static
    {
        $this->enoncer = $enoncer;

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

    public function isIscorrect(): ?bool
    {
        return $this->iscorrect;
    }

    public function setIscorrect(bool $iscorrect): static
    {
        $this->iscorrect = $iscorrect;

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
