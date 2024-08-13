<?php

namespace App\Entity\Enterprise;

use App\Entity\AbstractBase;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class EnterpriseHolidays.
 *
 * @category
 **
 */
#[ORM\Table(name: 'enterprise_holidays')]
#[ORM\Entity(repositoryClass: \App\Repository\Enterprise\EnterpriseHolidaysRepository::class)]
class EnterpriseHolidays extends AbstractBase
{
    /**
     * @var Enterprise
     */
    #[ORM\ManyToOne(targetEntity: \App\Entity\Enterprise\Enterprise::class, inversedBy: 'enterpriseHolidays')]
    private $enterprise;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'datetime')]
    private $day;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $name;

    /**
     * Methods.
     */

    /**
     * @return Enterprise
     */
    public function getEnterprise(): Enterprise
    {
        return $this->enterprise;
    }

    /**
     * @param string $enterprise
     *
     * @return $this
     */
    public function setEnterprise($enterprise): static
    {
        $this->enterprise = $enterprise;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDay(): DateTime
    {
        return $this->day;
    }

    /**
     * @param DateTime $day
     *
     * @return $this
     */
    public function setDay($day): static
    {
        $this->day = $day;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getDay()->format('d/m/Y').' · '.$this->getName() : '---';
    }
}
