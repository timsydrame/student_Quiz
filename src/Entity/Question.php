<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 1000)]
    private ?string $enonce = null;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $quiz = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: CandidateResponse::class, orphanRemoval: true)]
    private Collection $candidateResponses;

    public function __construct()
    {
        $this->candidateResponses = new ArrayCollection();
    }

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

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): static
    {
        $this->quiz = $quiz;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }


   

   

    /**
     * @return Collection<int, CandidateResponse>
     */
    public function getCandidateResponses(): Collection
    {
        return $this->candidateResponses;
    }

    public function addCandidateResponse(CandidateResponse $candidateResponse): static
    {
        if (!$this->candidateResponses->contains($candidateResponse)) {
            $this->candidateResponses->add($candidateResponse);
            $candidateResponse->setQuestion($this);
        }

        return $this;
    }

    public function removeCandidateResponse(CandidateResponse $candidateResponse): static
    {
        if ($this->candidateResponses->removeElement($candidateResponse)) {
            // set the owning side to null (unless already changed)
            if ($candidateResponse->getQuestion() === $this) {
                $candidateResponse->setQuestion(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getEnonce(); // Ou toute autre propriété qui peut être utilisée comme libellé
    }
}
