<?php

namespace App\Entity\Operator;

use App\Entity\AbstractBase;
use DateTime;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

abstract class OperatorCheckingBase extends AbstractBase
{
    /**
     * Methods.
     */
    public function getOperator(): ?Operator
    {
        return $this->operator;
    }

    public function setOperator($operator): static
    {
        $this->operator = $operator;

        return $this;
    }

    public function getType(): ?OperatorCheckingType
    {
        return $this->type;
    }

    public function setType($type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getBegin(): ?DateTime
    {
        return $this->begin;
    }

    public function setBegin(?DateTime $begin): static
    {
        $this->begin = $begin;

        return $this;
    }

    public function getEnd(): ?DateTime
    {
        return $this->end;
    }

    public function setEnd(?DateTime $end): static
    {
        $this->end = $end;

        return $this;
    }

    public function getStatus(): bool
    {
        return true;
    }

    public function getUploadedFile(): ?File
    {
        return $this->uploadedFile;
    }

    public function setUploadedFile(?File $uploadedFile): self
    {
        $this->uploadedFile = $uploadedFile;
        if ($uploadedFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getUploadedFileName(): ?string
    {
        return $this->uploadedFileName;
    }

    public function setUploadedFileName(?string $uploadedFileName): self
    {
        $this->uploadedFileName = $uploadedFileName;

        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->getEnd() < $this->getBegin()) {
            $context
                ->buildViolation('La data ha de ser més gran que la data d\'expedició')
                ->atPath('end')
                ->addViolation();
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getBegin()->format('d/m/Y').' · '.$this->getType().' · '.$this->getOperator() : '---';
    }
}
