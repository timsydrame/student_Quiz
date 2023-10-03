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

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: Response::class, orphanRemoval: true)]
    private Collection $responses;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: PossibleResponse::class, orphanRemoval: true)]
    private Collection $possibleResponses;

    public function __construct()
    {
        $this->responses = new ArrayCollection();
        $this->possibleResponses = new ArrayCollection();
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
     * @return Collection<int, Response>
     */
    public function getResponses(): Collection
    {
        return $this->responses;
    }

    public function addResponse(Response $response): static
    {
        if (!$this->responses->contains($response)) {
            $this->responses->add($response);
            $response->setQuestion($this);
        }

        return $this;
    }

    public function removeResponse(Response $response): static
    {
        if ($this->responses->removeElement($response)) {
            // set the owning side to null (unless already changed)
            if ($response->getQuestion() === $this) {
                $response->setQuestion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PossibleResponse>
     */
    public function getPossibleResponses(): Collection
    {
        return $this->possibleResponses;
    }

    public function addPossibleResponse(PossibleResponse $possibleResponse): static
    {
        if (!$this->possibleResponses->contains($possibleResponse)) {
            $this->possibleResponses->add($possibleResponse);
            $possibleResponse->setQuestion($this);
        }

        return $this;
    }

    public function removePossibleResponse(PossibleResponse $possibleResponse): static
    {
        if ($this->possibleResponses->removeElement($possibleResponse)) {
            // set the owning side to null (unless already changed)
            if ($possibleResponse->getQuestion() === $this) {
                $possibleResponse->setQuestion(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getEnonce(); // Ou toute autre propriété qui peut être utilisée comme libellé
    }
}
