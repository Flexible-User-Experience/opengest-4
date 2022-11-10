<?php

namespace App\Entity\Partner;

use App\Entity\AbstractBase;
use App\Entity\Enterprise\CollectionDocumentType;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Enterprise\EnterpriseTransferAccount;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleRequest;
use App\Entity\Sale\SaleTariff;
use App\Entity\Setting\City;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Mirmit\EFacturaBundle\Interfaces\BuyerFacturaEInterface;
use Symfony\Component\Intl\Countries;
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
class Partner extends AbstractBase implements BuyerFacturaEInterface
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
     * @Groups({"api"})
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
     * @var CollectionDocumentType
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\CollectionDocumentType", inversedBy="partners")
     */
    private $collectionDocumentType;

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
     * @Assert\Email(
     *     message = "El email '{{ value }}' no es un email válido."
     * )
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
     * @Groups({"api"})
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
     * @Groups({"api"})
     */
    private $iban;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Bic()
     * @Groups({"api"})
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
     * @ORM\OneToMany(targetEntity="App\Entity\Partner\PartnerOrder", mappedBy="partner", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    private $orders;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Partner\PartnerProject", mappedBy="partner", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    private $projects;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Partner\PartnerBuildingSite", mappedBy="partner", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    private $buildingSites;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Partner\PartnerContact", mappedBy="partner", cascade={"persist","remove"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    private $contacts;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Partner\PartnerDeliveryAddress", mappedBy="partner", cascade={"persist","remove"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    private $partnerDeliveryAddresses;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleRequest", mappedBy="partner")
     */
    private $saleRequests;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Partner\PartnerUnableDays", mappedBy="partner", cascade={"persist"}, orphanRemoval=true)
     */
    private $partnerUnableDays;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleDeliveryNote", mappedBy="partner")
     */
    private $saleDeliveryNotes;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleInvoice", mappedBy="partner")
     */
    private $saleInvoices;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Purchase\PurchaseInvoice", mappedBy="partner")
     */
    private Collection $purchaseInvoices;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Email(
     *     message = "El email '{{ value }}' no es un email válido."
     * )
     */
    private ?string $invoiceEmail = null;

    /**
     * @var ?integer
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Length(
     *     min = 10,
     *     max=10
     *     )
     */
    private ?int $accountingAccount = null;

    /**
     * @var ?integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $costAccountingAccount = null;

    /**
     * @var ?integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $collectionTerm1 = null;

    /**
     * @var ?integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $collectionTerm2 = null;

    /**
     * @var ?integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $collectionTerm3 = null;

    /**
     * @var ?integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $payDay1 = null;

    /**
     * @var ?integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $payDay2 = null;

    /**
     * @var ?integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $payDay3 = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $invoiceCopiesNumber = null;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $defaultIva = null;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $defaultIrpf = null;

    /**
     * @ORM\Column(type="boolean", options={"default" : false})
     */
    private bool $blocked = false;

    /**
     * Methods.
     */

    /**
     * Partner constructor.
     */
    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->buildingSites = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->partnerDeliveryAddresses = new ArrayCollection();
        $this->saleRequests = new ArrayCollection();
        $this->partnerUnableDays = new ArrayCollection();
        $this->saleDeliveryNotes = new ArrayCollection();
        $this->saleInvoices = new ArrayCollection();
        $this->purchaseInvoices = new ArrayCollection();
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
     * @return $this
     */
    public function removeOrder(PartnerOrder $order)
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
        }

        return $this;
    }

    public function getProjects(): Collection
    {
        return $this->projects;
    }

    /**
     * @param ArrayCollection $projects
     *
     * @return $this
     */
    public function setProjects($projects): Partner
    {
        $this->projects = $projects;

        return $this;
    }

    /**
     * @return $this
     */
    public function addProject(PartnerProject $project): Partner
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->setPartner($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeProject(PartnerProject $project): Partner
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
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

    public function getPartnerDeliveryAddresses(): Collection
    {
        return $this->partnerDeliveryAddresses;
    }

    /**
     * @return $this
     */
    public function setPartnerDeliveryAddresses(ArrayCollection $partnerDeliveryAddresses): Partner
    {
        $this->partnerDeliveryAddresses = $partnerDeliveryAddresses;

        return $this;
    }

    /**
     * @return $this
     */
    public function addPartnerDeliveryAddress(PartnerDeliveryAddress $partnerDeliveryAddress): Partner
    {
        if (!$this->partnerDeliveryAddresses->contains($partnerDeliveryAddress)) {
            $this->partnerDeliveryAddresses->add($partnerDeliveryAddress);
            $partnerDeliveryAddress->setPartner($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removePartnerDeliveryAddress(PartnerDeliveryAddress $partnerDeliveryAddress): Partner
    {
        if ($this->partnerDeliveryAddresses->contains($partnerDeliveryAddress)) {
            $this->partnerDeliveryAddresses->removeElement($partnerDeliveryAddress);
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

    public function getSaleTariffs(): ArrayCollection
    {
        return $this->saleTariffs;
    }

    public function setSaleTariffs(ArrayCollection $saleTariffs): Partner
    {
        $this->saleTariffs = $saleTariffs;

        return $this;
    }

    public function addSaleTariff(SaleTariff $saleTariff): Partner
    {
        if (!$this->saleTariffs->contains($saleTariff)) {
            $this->saleTariffs->add($saleTariff);
            $saleTariff->setPartner($this);
        }

        return $this;
    }

    /**
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
     * @return ?Collection
     */
    public function getSaleInvoices(): ?Collection
    {
        return $this->saleInvoices;
    }

    public function setSaleInvoices(ArrayCollection $saleInvoices): Partner
    {
        $this->saleInvoices = $saleInvoices;

        return $this;
    }

    /**
     * @return ?Collection
     */
    public function getPurchaseInvoices(): ?Collection
    {
        return $this->purchaseInvoices;
    }

    public function setPurchaseInvoices(ArrayCollection $purchaseInvoices): Partner
    {
        $this->purchaseInvoices = $purchaseInvoices;

        return $this;
    }

    public function getCollectionDocumentType(): ?CollectionDocumentType
    {
        return $this->collectionDocumentType;
    }

    public function setCollectionDocumentType(CollectionDocumentType $collectionDocumentType): Partner
    {
        $this->collectionDocumentType = $collectionDocumentType;

        return $this;
    }

    public function getInvoiceEmail(): ?string
    {
        return $this->invoiceEmail;
    }

    public function setInvoiceEmail(?string $invoiceEmail): Partner
    {
        $this->invoiceEmail = $invoiceEmail;

        return $this;
    }

    public function getAccountingAccount(): ?int
    {
        return $this->accountingAccount;
    }

    public function setAccountingAccount(?int $accountingAccount): Partner
    {
        $this->accountingAccount = $accountingAccount;

        return $this;
    }

    public function getCostAccountingAccount(): ?int
    {
        return $this->costAccountingAccount;
    }

    public function setCostAccountingAccount(?int $costAccountingAccount): Partner
    {
        $this->costAccountingAccount = $costAccountingAccount;

        return $this;
    }

    public function getCollectionTerm1(): ?int
    {
        return $this->collectionTerm1;
    }

    public function setCollectionTerm1(?int $collectionTerm1): Partner
    {
        $this->collectionTerm1 = $collectionTerm1;

        return $this;
    }

    public function getCollectionTerm2(): ?int
    {
        return $this->collectionTerm2;
    }

    public function setCollectionTerm2(?int $collectionTerm2): Partner
    {
        $this->collectionTerm2 = $collectionTerm2;

        return $this;
    }

    public function getCollectionTerm3(): ?int
    {
        return $this->collectionTerm3;
    }

    public function setCollectionTerm3(?int $collectionTerm3): Partner
    {
        $this->collectionTerm3 = $collectionTerm3;

        return $this;
    }

    public function getPayDay1(): ?int
    {
        return $this->payDay1;
    }

    public function setPayDay1(?int $payDay1): Partner
    {
        $this->payDay1 = $payDay1;

        return $this;
    }

    public function getPayDay2(): ?int
    {
        return $this->payDay2;
    }

    public function setPayDay2(?int $payDay2): Partner
    {
        $this->payDay2 = $payDay2;

        return $this;
    }

    public function getPayDay3(): ?int
    {
        return $this->payDay3;
    }

    public function setPayDay3(?int $payDay3): Partner
    {
        $this->payDay3 = $payDay3;

        return $this;
    }

    public function getInvoiceCopiesNumber(): ?int
    {
        return $this->invoiceCopiesNumber;
    }

    public function setInvoiceCopiesNumber(?int $invoiceCopiesNumber = null): Partner
    {
        $this->invoiceCopiesNumber = $invoiceCopiesNumber;

        return $this;
    }

    public function getDefaultIva(): ?float
    {
        return $this->defaultIva;
    }

    /**
     * @param ?float $defaultIva
     */
    public function setDefaultIva(?float $defaultIva): Partner
    {
        $this->defaultIva = $defaultIva;

        return $this;
    }

    public function getDefaultIrpf(): ?float
    {
        return $this->defaultIrpf;
    }

    /**
     * @param ?float $defaultIrpf
     */
    public function setDefaultIrpf(?float $defaultIrpf): Partner
    {
        $this->defaultIrpf = $defaultIrpf;

        return $this;
    }

    public function isBlocked(): bool
    {
        return $this->blocked;
    }

    public function setBlocked(bool $blocked): void
    {
        $this->blocked = $blocked;
    }

    /**
     * FacturaE Methods.
     */
    public function getIsLegalEntityFacturaE(): bool
    {
        return !preg_match('~[0-9]+~', substr($this->getCifNif(), 0, 1));
    }

    public function getTaxNumberFacturaE(): string
    {
        return $this->getCifNif();
    }

    public function getNameFacturaE(): string
    {
        if ($this->getIsLegalEntityFacturaE()) {
            return $this->getName();
        } else {
            return explode($this->getName(), ' ', 2)[0];
        }
    }

    public function getAddressFacturaE(): string
    {
        return $this->getMainAddress();
    }

    public function getPostalCodeFacturaE(): string
    {
        return $this->getMainCity()->getPostalCode();
    }

    public function getTownFacturaE(): string
    {
        return $this->getMainCity()->getName();
    }

    public function getProvinceFacturaE(): string
    {
        return $this->getMainCity()->getProvince()->getName();
    }

    public function getCountryCodeFacturaE(): string
    {
        return Countries::getAlpha3Code($this->getMainCity()->getProvince()->getCountry());
    }

    public function getEmailFacturaE(): string
    {
        return $this->getEmail() ?? '';
    }

    public function getFirstSurnameFacturaE(): string
    {
        if ($this->getIsLegalEntityFacturaE()) {
            return '';
        } else {
            return explode(explode($this->getName(), ' ', 2)[1], ' ', 2)[0];
        }
    }

    public function getLastSurnameFacturaE(): string
    {
        if ($this->getIsLegalEntityFacturaE()) {
            return '';
        } else {
            return explode(explode($this->getName(), ' ', 2)[1], ' ', 2)[1];
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getCode().' - '.$this->getName() : '---';
    }
}
