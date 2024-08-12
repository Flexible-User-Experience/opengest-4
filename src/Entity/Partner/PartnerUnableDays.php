<?php

namespace App\Entity\Partner;

use App\Entity\AbstractBase;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class PartnerUnableDays.
 *
 * @category Entity
 *
 * @author   Rubèn Hierro <info@rubenhierro.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Partner\PartnerUnableDaysRepository")
 * @ORM\Table(name="partner_unable_days")
 */
class PartnerUnableDays extends AbstractBase
{
    /**
     * @var Partner
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\Partner", inversedBy="partnerUnableDays")
     */
    private $partner;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private $begin;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private $end;

    /**
     * Methods.
     */
    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(Partner $partner): static
    {
        $this->partner = $partner;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getBegin(): DateTime
    {
        return $this->begin;
    }

    /**
     * @param DateTime $begin
     *
     * @return $this
     */
    public function setBegin($begin): static
    {
        $this->begin = $begin;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEnd(): DateTime
    {
        return $this->end;
    }

    /**
     * @param DateTime $end
     *
     * @return $this
     */
    public function setEnd($end): static
    {
        $this->end = $end;

        return $this;
    }

    /**
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->getEnd() < $this->getBegin()) {
            $context
                ->buildViolation('La data fi de ser més gran que la data d\'inici')
                ->atPath('end')
                ->addViolation();
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getPartner()->getName().' : '.$this->getBegin()->format('d/m/Y') : '---';
    }
}
