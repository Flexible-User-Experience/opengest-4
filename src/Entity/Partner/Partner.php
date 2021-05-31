<?php

namespace App\Entity\Partner;

use App\Entity\AbstractBase;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Enterprise\EnterpriseTransferAccount;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleRequest;
use App\Entity\Sale\SaleTariff;
use App\Entity\Setting\City;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Partner.
 *
 * @category Entity
 *
 * @author   Rubèn Hierro <info@rubenhierro.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Partner\PartnerRepository")
 * @ORM\Table(name="partner")
 */
class Partner extends AbstractBase
{
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Groups({"api"})
     */
    private $cifNif;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var Enterprise
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\Enterprise", inversedBy="partners")
     */
    private $enterprise;

    /**
     * @var PartnerClass
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\PartnerClass", inversedBy="partners")
     */
    private $class;

    /**
     * @var PartnerType
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\PartnerType", inversedBy="partners")
     */
    private $type;

    /**
     * @var EnterpriseTransferAccount
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\EnterpriseTransferAccount", inversedBy="partners")
     */
    private $transferAccount;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleTariff", mappedBy="partner")
     */
    private $saleTariffs;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=4000, nullable=true)
     */
    private $notes;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"api"})
     */
    private $mainAddress;

    /**
     * @var City
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Setting\City")
     * @Groups({"api"})
     */
    private $mainCity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $secondaryAddress;

    /**
     * @var City
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Setting\City")
     */
    private $secondaryCity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $phoneNumber1;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $phoneNumber2;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $phoneNumber3;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $phoneNumber4;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $phoneNumber5;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $faxNumber1;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $faxNumber2;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Email(strict=true, checkMX=true, checkHost=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $www;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $discount;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $providerReference;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $reference;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ivaTaxFree;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Iban()
     */
    private $iban;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Bic()
     */
    private $swift;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $bankCode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $officeNumber;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $controlDigit;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $accountNumber;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Partner\PartnerOrder", mappedBy="partner")
     */
    private $orders;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Partner\PartnerBuildingSite", mappedBy="partner")
     */
    private $buildingSites;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Partner\PartnerContact", mappedBy="partner")
     */
    private $contacts;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleRequest", mappedBy="partner")
     */
    private $saleRequests;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Partner\PartnerUnableDays", mappedBy="partner")
     */
    private $partnerUnableDays;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleDeliveryNote", mappedBy="partner")
     */
    private $saleDeliveryNotes;

    /**
     * Methods.
     */

    /**
     * Partner constructor.
     */
    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->buildingSites = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->saleRequests = new ArrayCollection();
        $this->partnerUnableDays = new ArrayCollection();
        $this->saleDeliveryNotes = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getCifNif()
    {
        return $this->cifNif;
    }

    /**
     * @param string $cifNif
     *
     * @return $this
     */
    public function setCifNif($cifNif)
    {
        $this->cifNif = $cifNif;

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
     * @return Enterprise
     */
    public function getEnterprise()
    {
        return $this->enterprise;
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return $this
     */
    public function setEnterprise($enterprise)
    {
        $this->enterprise = $enterprise;

        return $this;
    }

    /**
     * @return PartnerClass
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param PartnerClass $class
     *
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return PartnerType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param PartnerType $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return EnterpriseTransferAccount
     */
    public function getTransferAccount()
    {
        return $this->transferAccount;
    }

    /**
     * @param EnterpriseTransferAccount $transferAccount
     *
     * @return $this
     */
    public function setTransferAccount($transferAccount)
    {
        $this->transferAccount = $transferAccount;

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
     * @return string
     */
    public function getMainAddress()
    {
        return $this->mainAddress;
    }

    /**
     * @param string $mainAddress
     *
     * @return $this
     */
    public function setMainAddress($mainAddress)
    {
        $this->mainAddress = $mainAddress;

        return $this;
    }

    /**
     * @return City
     */
    public function getMainCity()
    {
        return $this->mainCity;
    }

    /**
     * @return string
     */
    public function getMainCityName()
    {
        if ($this->mainCity) {
            return $this->getMainCity()->getName();
        }

        return null;
    }

    /**
     * @param City $mainCity
     *
     * @return $this
     */
    public function setMainCity($mainCity)
    {
        $this->mainCity = $mainCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecondaryAddress()
    {
        return $this->secondaryAddress;
    }

    /**
     * @param string $secondaryAddress
     *
     * @return $this
     */
    public function setSecondaryAddress($secondaryAddress)
    {
        $this->secondaryAddress = $secondaryAddress;

        return $this;
    }

    /**
     * @return City
     */
    public function getSecondaryCity()
    {
        return $this->secondaryCity;
    }

    /**
     * @param City $secondaryCity
     *
     * @return $this
     */
    public function setSecondaryCity($secondaryCity)
    {
        $this->secondaryCity = $secondaryCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber1()
    {
        return $this->phoneNumber1;
    }

    /**
     * @param string $phoneNumber1
     *
     * @return $this
     */
    public function setPhoneNumber1($phoneNumber1)
    {
        $this->phoneNumber1 = $phoneNumber1;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber2()
    {
        return $this->phoneNumber2;
    }

    /**
     * @param string $phoneNumber2
     *
     * @return $this
     */
    public function setPhoneNumber2($phoneNumber2)
    {
        $this->phoneNumber2 = $phoneNumber2;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber3()
    {
        return $this->phoneNumber3;
    }

    /**
     * @param string $phoneNumber3
     *
     * @return $this
     */
    public function setPhoneNumber3($phoneNumber3)
    {
        $this->phoneNumber3 = $phoneNumber3;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber4()
    {
        return $this->phoneNumber4;
    }

    /**
     * @param string $phoneNumber4
     *
     * @return $this
     */
    public function setPhoneNumber4($phoneNumber4)
    {
        $this->phoneNumber4 = $phoneNumber4;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber5()
    {
        return $this->phoneNumber5;
    }

    /**
     * @param string $phoneNumber5
     *
     * @return $this
     */
    public function setPhoneNumber5($phoneNumber5)
    {
        $this->phoneNumber5 = $phoneNumber5;

        return $this;
    }

    /**
     * @return string
     */
    public function getFaxNumber1()
    {
        return $this->faxNumber1;
    }

    /**
     * @param string $faxNumber1
     *
     * @return $this
     */
    public function setFaxNumber1($faxNumber1)
    {
        $this->faxNumber1 = $faxNumber1;

        return $this;
    }

    /**
     * @return string
     */
    public function getFaxNumber2()
    {
        return $this->faxNumber2;
    }

    /**
     * @param string $faxNumber2
     *
     * @return $this
     */
    public function setFaxNumber2($faxNumber2)
    {
        $this->faxNumber2 = $faxNumber2;

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
    public function getWww()
    {
        return $this->www;
    }

    /**
     * @param string $www
     *
     * @return $this
     */
    public function setWww($www)
    {
        $this->www = $www;

        return $this;
    }

    /**
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param float $discount
     *
     * @return $this
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getProviderReference()
    {
        return $this->providerReference;
    }

    /**
     * @param string $providerReference
     *
     * @return $this
     */
    public function setProviderReference($providerReference)
    {
        $this->providerReference = $providerReference;

        return $this;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     *
     * @return $this
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @return bool
     */
    public function isIvaTaxFree()
    {
        return $this->ivaTaxFree;
    }

    /**
     * @param bool $ivaTaxFree
     *
     * @return $this
     */
    public function setIvaTaxFree($ivaTaxFree)
    {
        $this->ivaTaxFree = $ivaTaxFree;

        return $this;
    }

    /**
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * @param string $iban
     *
     * @return $this
     */
    public function setIban($iban)
    {
        $this->iban = $iban;

        return $this;
    }

    /**
     * @return string
     */
    public function getSwift()
    {
        return $this->swift;
    }

    /**
     * @param string $swift
     *
     * @return $this
     */
    public function setSwift($swift)
    {
        $this->swift = $swift;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankCode()
    {
        return $this->bankCode;
    }

    /**
     * @param string $bankCode
     *
     * @return $this
     */
    public function setBankCode($bankCode)
    {
        $this->bankCode = $bankCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getOfficeNumber()
    {
        return $this->officeNumber;
    }

    /**
     * @param string $officeNumber
     *
     * @return $this
     */
    public function setOfficeNumber($officeNumber)
    {
        $this->officeNumber = $officeNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getControlDigit()
    {
        return $this->controlDigit;
    }

    /**
     * @param string $controlDigit
     *
     * @return $this
     */
    public function setControlDigit($controlDigit)
    {
        $this->controlDigit = $controlDigit;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * @param string $accountNumber
     *
     * @return $this
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param ArrayCollection $orders
     *
     * @return $this
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;

        return $this;
    }

    /**
     * @param PartnerOrder $order
     *
     * @return $this
     */
    public function addOrder(PartnerOrder $order)
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setPartner($this);
        }

        return $this;
    }

    /**
     * @param PartnerOrder $order
     *
     * @return $this
     */
    public function remmoveOrder(PartnerOrder $order)
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getBuildingSites()
    {
        return $this->buildingSites;
    }

    /**
     * @param ArrayCollection $buildingSites
     *
     * @return $this
     */
    public function setBuildingSites($buildingSites)
    {
        $this->buildingSites = $buildingSites;

        return $this;
    }

    /**
     * @param PartnerBuildingSite $buildingSite
     *
     * @return $this
     */
    public function addBuildingSite(PartnerBuildingSite $buildingSite)
    {
        if (!$this->buildingSites->contains($buildingSite)) {
            $this->buildingSites->add($buildingSite);
            $buildingSite->setPartner($this);
        }

        return $this;
    }

    /**
     * @param PartnerBuildingSite $buildingSite
     *
     * @return $this
     */
    public function removeBuildingSite(PartnerBuildingSite $buildingSite)
    {
        if ($this->buildingSites->contains($buildingSite)) {
            $this->buildingSites->removeElement($buildingSite);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * @return string
     */
    public function getMainContactName()
    {
        $result = '';
        if (count($this->getContacts()) > 0) {
            $result = $this->getContacts()[0]->getName();
        }

        return $result;
    }

    /**
     * @param ArrayCollection $contacts
     *
     * @return $this
     */
    public function setContacts($contacts)
    {
        $this->contacts = $contacts;

        return $this;
    }

    /**
     * @param PartnerContact $contact
     *
     * @return $this
     */
    public function addContact($contact)
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->setPartner($this);
        }

        return $this;
    }

    /**
     * @param PartnerContact $contact
     *
     * @return $this
     */
    public function removeContact($contact)
    {
        if ($this->contacts->contains($contact)) {
            $this->contacts->removeElement($contact);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSaleRequests()
    {
        return $this->saleRequests;
    }

    /**
     * @param ArrayCollection $saleRequests
     *
     * @return $this
     */
    public function setSaleRequests($saleRequests)
    {
        $this->saleRequests = $saleRequests;

        return $this;
    }

    /**
     * @param SaleRequest $saleRequest
     *
     * @return $this
     */
    public function addSaleRequest($saleRequest)
    {
        if (!$this->saleRequests->contains($saleRequest)) {
            $this->saleRequests->add($saleRequest);
            $saleRequest->setPartner($this);
        }

        return $this;
    }

    /**
     * @param SaleRequest $saleRequest
     *
     * @return $this
     */
    public function removeSaleRequest($saleRequest)
    {
        if ($this->saleRequests->contains($saleRequest)) {
            $this->saleRequests->removeElement($saleRequest);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPartnerUnableDays()
    {
        return $this->partnerUnableDays;
    }

    /**
     * @param ArrayCollection $partnerUnableDays
     */
    public function setPartnerUnableDays($partnerUnableDays): void
    {
        $this->partnerUnableDays = $partnerUnableDays;
    }

    /**
     * @param PartnerUnableDays $partnerUnableDays
     *
     * @return $this
     */
    public function addPartnerUnableDay($partnerUnableDays)
    {
        if (!$this->partnerUnableDays->contains($partnerUnableDays)) {
            $this->partnerUnableDays->add($partnerUnableDays);
            $partnerUnableDays->setPartner($this);
        }

        return $this;
    }

    public function removePartnerUnableDay($partnerUnableDays)
    {
        if ($this->partnerUnableDays->contains($partnerUnableDays)) {
            $this->partnerUnableDays->removeElement($partnerUnableDays);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSaleDeliveryNotes()
    {
        return $this->saleDeliveryNotes;
    }

    /**
     * @param ArrayCollection $saleDeliveryNotes
     *
     * @return $this
     */
    public function setSaleDeliveryNotes($saleDeliveryNotes)
    {
        $this->saleDeliveryNotes = $saleDeliveryNotes;

        return $this;
    }

    /**
     * @param SaleDeliveryNote $saleDeliveryNote
     *
     * @return $this
     */
    public function addSaleDeliveryNote($saleDeliveryNote)
    {
        if (!$this->saleDeliveryNotes->contains($saleDeliveryNote)) {
            $this->saleDeliveryNotes->add($saleDeliveryNote);
            $saleDeliveryNote->setPartner($this);
        }

        return $this;
    }

    /**
     * @param SaleDeliveryNote $saleDeliveryNote
     *
     * @return $this
     */
    public function removeSaleDeliverynote($saleDeliveryNote)
    {
        if ($this->saleDeliveryNotes->contains($saleDeliveryNote)) {
            $this->saleDeliveryNotes->removeElement($saleDeliveryNote);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSaleTariffs(): ArrayCollection
    {
        return $this->saleTariffs;
    }

    /**
     * @param ArrayCollection $saleTariffs
     *
     * @return Partner
     */
    public function setSaleTariffs(ArrayCollection $saleTariffs): Partner
    {
        $this->saleTariffs = $saleTariffs;

        return $this;
    }

    /**
     * @param SaleTariff $saleTariff
     *
     * @return Partner
     */
    public function addSaleTariff(SaleTariff $saleTariff): Partner
    {
        if (!$this->saleTariffs->contains($saleTariff)) {
            $this->saleTariffs->add($saleTariff);
            $saleTariff->setPartner($this);
        }

        return $this;
    }

    /**
     * @param SaleTariff $saleTariff
     *
     * @return $this
     */
    public function removeSaleTariff(SaleTariff $saleTariff)
    {
        if ($this->saleTariffs->contains($saleTariff)) {
            $this->saleTariffs->removeElement($saleTariff);
            $saleTariff->setPartner();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getName() : '---';
    }
}
