<?php

namespace App\Entity\Partner;

use App\Entity\AbstractBase;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PartnerContact.
 *
 * @category Entity
 *
 * @author   Rubèn Hierro <info@rubenhierro.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Partner\PartnerContactRepository")
 * @ORM\Table(name="partner_contact")
 */
class PartnerContact extends AbstractBase
{
    /**
     * @var Partner
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\Partner", inversedBy="contacts")
     */
    private $partner;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Groups({"api"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $care;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"api"})
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"api"})
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Email(
     *     message = "El email '{{ value }}' no es un email válido."
     * )
     * @Groups({"api"})
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=4000, nullable=true)
     */
    private $notes;

    /**
     * Methods.
     */

    /**
     * @return Partner
     */
    public function getPartner()
    {
        return $this->partner;
    }

    /**
     * @param Partner $partner
     *
     * @return $this
     */
    public function setPartner($partner)
    {
        $this->partner = $partner;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getCare()
    {
        return $this->care;
    }

    /**
     * @param string $care
     *
     * @return $this
     */
    public function setCare($care)
    {
        $this->care = $care;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     *
     * @return $this
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param string $fax
     *
     * @return $this
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     *
     * @return $this
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * @Groups({"api"})
     *
     * @return string
     */
    public function getPublicPhone()
    {
        if ($this->getPhone() or $this->getMobile()) {
            if ($this->getMobile()) {
                return $this->getMobile();
            } else {
                return $this->getPhone();
            }
        }

        return '---';
    }

    /**
     * @Groups({"api"})
     *
     * @return string
     */
    public function getText()
    {
        return $this->name.' - '.$this->getPublicPhone();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getName() : '???';
    }
}
