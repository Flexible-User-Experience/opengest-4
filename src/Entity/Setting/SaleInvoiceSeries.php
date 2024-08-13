<?php

namespace App\Entity\Setting;

use App\Entity\AbstractBase;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class SaleInvoiceSeries.
 *
 * @category Entity
 */
#[ORM\Table(name: 'sale_invoice_series')]
#[ORM\Entity(repositoryClass: \App\Repository\Setting\SaleInvoiceSeriesRepository::class)]
class SaleInvoiceSeries extends AbstractBase
{
    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    private $name;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $prefix;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private $isDefault = false;

    /**
     * Methods.
     */

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
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     *
     * @return $this
     */
    public function setPrefix($prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    /**
     * @param bool $isDefault
     *
     * @return $this
     */
    public function setIsDefault($isDefault): static
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getName().' · '.$this->getPrefix() : '---';
    }
}
