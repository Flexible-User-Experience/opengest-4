<?php

namespace App\Entity\Enterprise;

use App\Entity\AbstractBase;
use App\Entity\Partner\Partner;
use App\Entity\Sale\SaleRequest;
use App\Entity\Sale\SaleTariff;
use App\Entity\Setting\City;
use App\Entity\Setting\Document;
use App\Entity\Setting\User;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Mirmit\EFacturaBundle\Interfaces\SellerFacturaEInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class Enterprise.
 *
 * @category    Entity
 *
 * @author      Wils Iglesias <wiglesias83@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Enterprise\EnterpriseRepository")
 * @ORM\Table(name="enterprise")
 * @Vich\Uploadable()
 * @UniqueEntity({"taxIdentificationNumber"})
 */
class Enterprise extends AbstractBase implements SellerFacturaEInterface
{
    public const GRUAS_ROMANI_TIN = 'A43030287';

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $taxIdentificationNumber;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $businessName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $address;

    /**
     * @var City
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Setting\City")
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $phone1;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $phone2;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $phone3;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
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
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="logo")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     * @Assert\Image(minWidth=100)
     */
    private $logoFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $logo;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="deedOfIncorporation")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $deedOfIncorporationFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $deedOfIncorporation;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="taxIdentificationNumberCard")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $taxIdentificationNumberCardFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $taxIdentificationNumberCard;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="tc1Receipt")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $tc1ReceiptFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true, name="tc1receipt")
     */
    private $tc1Receipt;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="tc2Receipt")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $tc2ReceiptFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true, name="tc2receipt")
     */
    private $tc2Receipt;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="ssRegistration")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $ssRegistrationFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $ssRegistration;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="ssPaymentCertificate")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $ssPaymentCertificateFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $ssPaymentCertificate;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="rc1Insurance")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $rc1InsuranceFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $rc1Insurance;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="rc2Insurance")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $rc2InsuranceFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $rc2Insurance;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="rcReceipt")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $rcReceiptFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $rcReceipt;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="preventionServiceContract")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $preventionServiceContractFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $preventionServiceContract;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="preventionServiceInvoice")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $preventionServiceInvoiceFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $preventionServiceInvoice;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="preventionServiceReceipt")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $preventionServiceReceiptFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $preventionServiceReceipt;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="occupationalAccidentsInsurance")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $occupationalAccidentsInsuranceFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $occupationalAccidentsInsurance;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="occupationalReceipt")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $occupationalReceiptFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $occupationalReceipt;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="laborRiskAssessment")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $laborRiskAssessmentFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $laborRiskAssessment;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="securityPlan")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $securityPlanFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $securityPlan;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="reaCertificate")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $reaCertificateFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $reaCertificate;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="oilCertificate")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $oilCertificateFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $oilCertificate;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="gencatPaymentCertificate")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $gencatPaymentCertificateFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $gencatPaymentCertificate;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="deedsOfPowers")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $deedsOfPowersFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $deedsOfPowers;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="iaeRegistration")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $iaeRegistrationFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $iaeRegistration;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="iaeReceipt")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $iaeReceiptFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $iaeReceipt;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="enterprise", fileNameProperty="mutualPartnership")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $mutualPartnershipFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $mutualPartnership;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Setting\User", mappedBy="enterprises")
     * @ORM\JoinTable(name="enterprises_users")
     */
    private $users;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Enterprise\EnterpriseGroupBounty", mappedBy="enterprise", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $enterpriseGroupBounties;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Enterprise\EnterpriseTransferAccount", mappedBy="enterprise", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $enterpriseTransferAccounts;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Partner\Partner", mappedBy="enterprise")
     */
    private $partners;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Enterprise\EnterpriseHolidays", mappedBy="enterprise", cascade={"persist", "remove"}, orphanRemoval=true )
     */
    private $enterpriseHolidays;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleTariff", mappedBy="enterprise", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $saleTariffs;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleRequest", mappedBy="enterprise")
     */
    private $saleRequests;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Enterprise\ActivityLine", mappedBy="enterprise")
     */
    private $activityLines;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Enterprise\CollectionDocumentType", mappedBy="enterprise")
     */
    private $collectionDocumentTypes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Setting\Document", mappedBy="enterprise", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"description" = "ASC"})
     */
    private ?Collection $documents = null;

    /**
     * Methods.
     */

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->enterpriseGroupBounties = new ArrayCollection();
        $this->enterpriseTransferAccounts = new ArrayCollection();
        $this->partners = new ArrayCollection();
        $this->enterpriseHolidays = new ArrayCollection();
        $this->saleTariffs = new ArrayCollection();
        $this->saleRequests = new ArrayCollection();
        $this->activityLines = new ArrayCollection();
        $this->collectionDocumentTypes = new ArrayCollection();
        $this->documents = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Enterprise
     */
    public function setName($name): Enterprise
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getTaxIdentificationNumber(): string
    {
        return $this->taxIdentificationNumber;
    }

    /**
     * @param string $taxIdentificationNumber
     *
     * @return Enterprise
     */
    public function setTaxIdentificationNumber($taxIdentificationNumber): Enterprise
    {
        $this->taxIdentificationNumber = $taxIdentificationNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessName(): string
    {
        return $this->businessName;
    }

    /**
     * @param string $businessName
     *
     * @return Enterprise
     */
    public function setBusinessName($businessName): Enterprise
    {
        $this->businessName = $businessName;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     *
     * @return Enterprise
     */
    public function setAddress($address): Enterprise
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return City
     */
    public function getCity(): City
    {
        return $this->city;
    }

    /**
     * @param City $city
     *
     * @return Enterprise
     */
    public function setCity($city): Enterprise
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone1(): string
    {
        return $this->phone1;
    }

    /**
     * @param string $phone1
     *
     * @return Enterprise
     */
    public function setPhone1($phone1): Enterprise
    {
        $this->phone1 = $phone1;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone2(): string
    {
        return $this->phone2;
    }

    /**
     * @param string $phone2
     *
     * @return Enterprise
     */
    public function setPhone2($phone2): Enterprise
    {
        $this->phone2 = $phone2;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone3(): string
    {
        return $this->phone3;
    }

    /**
     * @param string $phone3
     *
     * @return Enterprise
     */
    public function setPhone3($phone3): Enterprise
    {
        $this->phone3 = $phone3;

        return $this;
    }

    /**
     * @return string
     */
    public function getFax(): string
    {
        return $this->fax;
    }

    /**
     * @param string $fax
     *
     * @return Enterprise
     */
    public function setFax($fax): Enterprise
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return Enterprise
     */
    public function setEmail($email): Enterprise
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getWww(): string
    {
        return $this->www;
    }

    /**
     * @param string $www
     *
     * @return Enterprise
     */
    public function setWww($www): Enterprise
    {
        $this->www = $www;

        return $this;
    }

    public function getLogoFile(): ?File
    {
        return $this->logoFile;
    }

    /**
     * @throws Exception
     */
    public function setLogoFile(?File $logoFile = null): Enterprise
    {
        $this->logoFile = $logoFile;
        if ($logoFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    /**
     * @param string $logo
     *
     * @return Enterprise
     */
    public function setLogo($logo): Enterprise
    {
        $this->logo = $logo;

        return $this;
    }

    public function getDeedOfIncorporationFile(): ?File
    {
        return $this->deedOfIncorporationFile;
    }

    /**
     * @throws Exception
     */
    public function setDeedOfIncorporationFile(?File $deedOfIncorporationFile = null): Enterprise
    {
        $this->deedOfIncorporationFile = $deedOfIncorporationFile;
        if ($deedOfIncorporationFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDeedOfIncorporation(): ?string
    {
        return $this->deedOfIncorporation;
    }

    /**
     * @param string $deedOfIncorporation
     *
     * @return Enterprise
     */
    public function setDeedOfIncorporation($deedOfIncorporation): Enterprise
    {
        $this->deedOfIncorporation = $deedOfIncorporation;

        return $this;
    }

    public function getTaxIdentificationNumberCardFile(): ?File
    {
        return $this->taxIdentificationNumberCardFile;
    }

    /**
     * @throws Exception
     */
    public function setTaxIdentificationNumberCardFile(?File $taxIdentificationNumberCardFile = null): Enterprise
    {
        $this->taxIdentificationNumberCardFile = $taxIdentificationNumberCardFile;
        if ($taxIdentificationNumberCardFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTaxIdentificationNumberCard(): ?string
    {
        return $this->taxIdentificationNumberCard;
    }

    /**
     * @param string $taxIdentificationNumberCard
     *
     * @return Enterprise
     */
    public function setTaxIdentificationNumberCard($taxIdentificationNumberCard): Enterprise
    {
        $this->taxIdentificationNumberCard = $taxIdentificationNumberCard;

        return $this;
    }

    public function getTc1ReceiptFile(): ?File
    {
        return $this->tc1ReceiptFile;
    }

    /**
     * @throws Exception
     */
    public function setTc1ReceiptFile(?File $tc1ReceiptFile = null): Enterprise
    {
        $this->tc1ReceiptFile = $tc1ReceiptFile;
        if ($tc1ReceiptFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getTc1Receipt(): ?string
    {
        return $this->tc1Receipt;
    }

    /**
     * @return Enterprise
     */
    public function setTc1Receipt(string $tc1Receipt): Enterprise
    {
        $this->tc1Receipt = $tc1Receipt;

        return $this;
    }

    public function getTc2ReceiptFile(): ?File
    {
        return $this->tc2ReceiptFile;
    }

    /**
     * @throws Exception
     */
    public function setTc2ReceiptFile(?File $tc2ReceiptFile = null): Enterprise
    {
        $this->tc2ReceiptFile = $tc2ReceiptFile;
        if ($tc2ReceiptFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTc2Receipt(): ?string
    {
        return $this->tc2Receipt;
    }

    /**
     * @return Enterprise
     */
    public function setTc2Receipt(string $tc2Receipt): Enterprise
    {
        $this->tc2Receipt = $tc2Receipt;

        return $this;
    }

    public function getSsRegistrationFile(): ?File
    {
        return $this->ssRegistrationFile;
    }

    /**
     * @throws Exception
     */
    public function setSsRegistrationFile(?File $ssRegistrationFile = null): Enterprise
    {
        $this->ssRegistrationFile = $ssRegistrationFile;
        if ($ssRegistrationFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getSsRegistration(): ?string
    {
        return $this->ssRegistration;
    }

    public function setSsRegistration(string $ssRegistration): Enterprise
    {
        $this->ssRegistration = $ssRegistration;

        return $this;
    }

    public function getSsPaymentCertificateFile(): ?File
    {
        return $this->ssPaymentCertificateFile;
    }

    /**
     * @throws Exception
     */
    public function setSsPaymentCertificateFile(?File $ssPaymentCertificateFile = null): Enterprise
    {
        $this->ssPaymentCertificateFile = $ssPaymentCertificateFile;
        if ($ssPaymentCertificateFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getSsPaymentCertificate(): ?string
    {
        return $this->ssPaymentCertificate;
    }

    public function setSsPaymentCertificate(string $ssPaymentCertificate): Enterprise
    {
        $this->ssPaymentCertificate = $ssPaymentCertificate;

        return $this;
    }

    public function getRc1InsuranceFile(): ?File
    {
        return $this->rc1InsuranceFile;
    }

    /**
     * @throws Exception
     */
    public function setRc1InsuranceFile(?File $rc1InsuranceFile = null): Enterprise
    {
        $this->rc1InsuranceFile = $rc1InsuranceFile;
        if ($rc1InsuranceFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getRc1Insurance(): ?string
    {
        return $this->rc1Insurance;
    }

    public function setRc1Insurance(string $rc1Insurance): Enterprise
    {
        $this->rc1Insurance = $rc1Insurance;

        return $this;
    }

    public function getRc2InsuranceFile(): ?File
    {
        return $this->rc2InsuranceFile;
    }

    /**
     * @throws Exception
     */
    public function setRc2InsuranceFile(?File $rc2InsuranceFile = null): Enterprise
    {
        $this->rc2InsuranceFile = $rc2InsuranceFile;
        if ($rc2InsuranceFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getRc2Insurance(): ?string
    {
        return $this->rc2Insurance;
    }

    public function setRc2Insurance(string $rc2Insurance): Enterprise
    {
        $this->rc2Insurance = $rc2Insurance;

        return $this;
    }

    public function getRcReceiptFile(): ?File
    {
        return $this->rcReceiptFile;
    }

    /**
     * @throws Exception
     */
    public function setRcReceiptFile(?File $rcReceiptFile = null): Enterprise
    {
        $this->rcReceiptFile = $rcReceiptFile;
        if ($rcReceiptFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getRcReceipt(): ?string
    {
        return $this->rcReceipt;
    }

    public function setRcReceipt(string $rcReceipt): Enterprise
    {
        $this->rcReceipt = $rcReceipt;

        return $this;
    }

    public function getPreventionServiceContractFile(): ?File
    {
        return $this->preventionServiceContractFile;
    }

    /**
     * @throws Exception
     */
    public function setPreventionServiceContractFile(?File $preventionServiceContractFile = null): Enterprise
    {
        $this->preventionServiceContractFile = $preventionServiceContractFile;
        if ($preventionServiceContractFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getPreventionServiceContract(): ?string
    {
        return $this->preventionServiceContract;
    }

    public function setPreventionServiceContract(string $preventionServiceContract): Enterprise
    {
        $this->preventionServiceContract = $preventionServiceContract;

        return $this;
    }

    public function getPreventionServiceInvoiceFile(): ?File
    {
        return $this->preventionServiceInvoiceFile;
    }

    /**
     * @throws Exception
     */
    public function setPreventionServiceInvoiceFile(?File $preventionServiceInvoiceFile = null): Enterprise
    {
        $this->preventionServiceInvoiceFile = $preventionServiceInvoiceFile;
        if ($preventionServiceInvoiceFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getPreventionServiceInvoice(): ?string
    {
        return $this->preventionServiceInvoice;
    }

    public function setPreventionServiceInvoice(string $preventionServiceInvoice): Enterprise
    {
        $this->preventionServiceInvoice = $preventionServiceInvoice;

        return $this;
    }

    public function getPreventionServiceReceiptFile(): ?File
    {
        return $this->preventionServiceReceiptFile;
    }

    /**
     * @throws Exception
     */
    public function setPreventionServiceReceiptFile(?File $preventionServiceReceiptFile = null): Enterprise
    {
        $this->preventionServiceReceiptFile = $preventionServiceReceiptFile;
        if ($this->preventionServiceReceiptFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getPreventionServiceReceipt(): ?string
    {
        return $this->preventionServiceReceipt;
    }

    public function setPreventionServiceReceipt(string $preventionServiceReceipt): Enterprise
    {
        $this->preventionServiceReceipt = $preventionServiceReceipt;

        return $this;
    }

    public function getOccupationalAccidentsInsuranceFile(): ?File
    {
        return $this->occupationalAccidentsInsuranceFile;
    }

    /**
     * @throws Exception
     */
    public function setOccupationalAccidentsInsuranceFile(?File $occupationalAccidentsInsuranceFile = null): Enterprise
    {
        $this->occupationalAccidentsInsuranceFile = $occupationalAccidentsInsuranceFile;
        if ($occupationalAccidentsInsuranceFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getOccupationalAccidentsInsurance(): ?string
    {
        return $this->occupationalAccidentsInsurance;
    }

    public function setOccupationalAccidentsInsurance(string $occupationalAccidentsInsurance): Enterprise
    {
        $this->occupationalAccidentsInsurance = $occupationalAccidentsInsurance;

        return $this;
    }

    public function getOccupationalReceiptFile(): ?File
    {
        return $this->occupationalReceiptFile;
    }

    /**
     * @throws Exception
     */
    public function setOccupationalReceiptFile(?File $occupationalReceiptFile = null): Enterprise
    {
        $this->occupationalReceiptFile = $occupationalReceiptFile;
        if ($this->occupationalReceiptFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getOccupationalReceipt(): ?string
    {
        return $this->occupationalReceipt;
    }

    public function setOccupationalReceipt(string $occupationalReceipt): Enterprise
    {
        $this->occupationalReceipt = $occupationalReceipt;

        return $this;
    }

    public function getLaborRiskAssessmentFile(): ?File
    {
        return $this->laborRiskAssessmentFile;
    }

    /**
     * @throws Exception
     */
    public function setLaborRiskAssessmentFile(?File $laborRiskAssessmentFile = null): Enterprise
    {
        $this->laborRiskAssessmentFile = $laborRiskAssessmentFile;
        if ($laborRiskAssessmentFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getLaborRiskAssessment(): ?string
    {
        return $this->laborRiskAssessment;
    }

    public function setLaborRiskAssessment(string $laborRiskAssessment): Enterprise
    {
        $this->laborRiskAssessment = $laborRiskAssessment;

        return $this;
    }

    public function getSecurityPlanFile(): ?File
    {
        return $this->securityPlanFile;
    }

    /**
     * @throws Exception
     */
    public function setSecurityPlanFile(?File $securityPlanFile = null): Enterprise
    {
        $this->securityPlanFile = $securityPlanFile;
        if ($securityPlanFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getSecurityPlan(): ?string
    {
        return $this->securityPlan;
    }

    public function setSecurityPlan(string $securityPlan): Enterprise
    {
        $this->securityPlan = $securityPlan;

        return $this;
    }

    public function getReaCertificateFile(): ?File
    {
        return $this->reaCertificateFile;
    }

    /**
     * @throws Exception
     */
    public function setReaCertificateFile(?File $reaCertificateFile = null): Enterprise
    {
        $this->reaCertificateFile = $reaCertificateFile;
        if ($reaCertificateFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getReaCertificate(): ?string
    {
        return $this->reaCertificate;
    }

    public function setReaCertificate(string $reaCertificate): Enterprise
    {
        $this->reaCertificate = $reaCertificate;

        return $this;
    }

    public function getOilCertificateFile(): ?File
    {
        return $this->oilCertificateFile;
    }

    /**
     * @throws Exception
     */
    public function setOilCertificateFile(?File $oilCertificateFile = null): Enterprise
    {
        $this->oilCertificateFile = $oilCertificateFile;
        if ($oilCertificateFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getOilCertificate(): ?string
    {
        return $this->oilCertificate;
    }

    public function setOilCertificate(string $oilCertificate): Enterprise
    {
        $this->oilCertificate = $oilCertificate;

        return $this;
    }

    public function getGencatPaymentCertificateFile(): ?File
    {
        return $this->gencatPaymentCertificateFile;
    }

    /**
     * @throws Exception
     */
    public function setGencatPaymentCertificateFile(?File $gencatPaymentCertificateFile = null): Enterprise
    {
        $this->gencatPaymentCertificateFile = $gencatPaymentCertificateFile;
        if ($gencatPaymentCertificateFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getGencatPaymentCertificate(): ?string
    {
        return $this->gencatPaymentCertificate;
    }

    public function setGencatPaymentCertificate(string $gencatPaymentCertificate): Enterprise
    {
        $this->gencatPaymentCertificate = $gencatPaymentCertificate;

        return $this;
    }

    public function getDeedsOfPowersFile(): ?File
    {
        return $this->deedsOfPowersFile;
    }

    /**
     * @throws Exception
     */
    public function setDeedsOfPowersFile(?File $deedsOfPowersFile = null): Enterprise
    {
        $this->deedsOfPowersFile = $deedsOfPowersFile;
        if ($deedsOfPowersFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getDeedsOfPowers(): ?string
    {
        return $this->deedsOfPowers;
    }

    public function setDeedsOfPowers(string $deedsOfPowers): Enterprise
    {
        $this->deedsOfPowers = $deedsOfPowers;

        return $this;
    }

    public function getIaeRegistrationFile(): ?File
    {
        return $this->iaeRegistrationFile;
    }

    /**
     * @throws Exception
     */
    public function setIaeRegistrationFile(?File $iaeRegistrationFile = null): Enterprise
    {
        $this->iaeRegistrationFile = $iaeRegistrationFile;
        if ($iaeRegistrationFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getIaeRegistration(): ?string
    {
        return $this->iaeRegistration;
    }

    public function setIaeRegistration(string $iaeRegistration): Enterprise
    {
        $this->iaeRegistration = $iaeRegistration;

        return $this;
    }

    public function getIaeReceiptFile(): ?File
    {
        return $this->iaeReceiptFile;
    }

    /**
     * @throws Exception
     */
    public function setIaeReceiptFile(?File $iaeReceiptFile = null): Enterprise
    {
        $this->iaeReceiptFile = $iaeReceiptFile;
        if ($this->iaeReceiptFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getIaeReceipt(): mixed
    {
        return $this->iaeReceipt;
    }

    public function setIaeReceipt(mixed $iaeReceipt): Enterprise
    {
        $this->iaeReceipt = $iaeReceipt;

        return $this;
    }

    public function getMutualPartnershipFile(): ?File
    {
        return $this->mutualPartnershipFile;
    }

    /**
     * @throws Exception
     */
    public function setMutualPartnershipFile(?File $mutualPartnershipFile = null): Enterprise
    {
        $this->mutualPartnershipFile = $mutualPartnershipFile;
        if ($mutualPartnershipFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getMutualPartnership(): ?string
    {
        return $this->mutualPartnership;
    }

    public function setMutualPartnership(string $mutualPartnership): Enterprise
    {
        $this->mutualPartnership = $mutualPartnership;

        return $this;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function setUsers($users): Enterprise
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @return $this
     */
    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addEnterprise($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeUser(User $user): static
    {
        $user->removeEnterprise($this);
        $this->users->removeElement($user);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getEnterpriseGroupBounties(): Collection
    {
        return $this->enterpriseGroupBounties;
    }

    /**
     * @param EnterpriseGroupBounty $enterpriseGroupBounties
     *
     * @return $this
     */
    public function setEnterpriseGroupBounties($enterpriseGroupBounties): static
    {
        $this->enterpriseGroupBounties = $enterpriseGroupBounties;

        return $this;
    }

    /**
     * @return $this
     */
    public function addEnterpriseGroupBounty(EnterpriseGroupBounty $enterpriseGroupBounty): static
    {
        if (!$this->enterpriseGroupBounties->contains($enterpriseGroupBounty)) {
            $this->enterpriseGroupBounties->add($enterpriseGroupBounty);
            $enterpriseGroupBounty->setEnterprise($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeEnterpriseGroupBounty(EnterpriseGroupBounty $enterpriseGroupBounty): static
    {
        if ($this->enterpriseGroupBounties->contains($enterpriseGroupBounty)) {
            $this->enterpriseGroupBounties->removeElement($enterpriseGroupBounty);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getEnterpriseTransferAccounts(): Collection
    {
        return $this->enterpriseTransferAccounts;
    }

    /**
     * @param ArrayCollection $enterpriseTransferAccounts
     *
     * @return $this
     */
    public function setEnterpriseTransferAccounts($enterpriseTransferAccounts): static
    {
        $this->enterpriseTransferAccounts = $enterpriseTransferAccounts;

        return $this;
    }

    /**
     * @param EnterpriseTransferAccount $enterpriseTransferAccount
     *
     * @return $this
     */
    public function addEnterpriseTransferAccount($enterpriseTransferAccount): static
    {
        if (!$this->enterpriseTransferAccounts->contains($enterpriseTransferAccount)) {
            $this->enterpriseTransferAccounts->add($enterpriseTransferAccount);
            $enterpriseTransferAccount->setEnterprise($this);
        }

        return $this;
    }

    /**
     * @param EnterpriseTransferAccount $enterpriseTransferAccount
     *
     * @return $this
     */
    public function removeEnterpriseTransferAccount($enterpriseTransferAccount): static
    {
        if ($this->enterpriseTransferAccounts->contains($enterpriseTransferAccount)) {
            $this->enterpriseTransferAccounts->removeElement($enterpriseTransferAccount);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPartners(): Collection
    {
        return $this->partners;
    }

    /**
     * @param ArrayCollection $partners
     *
     * @return $this
     */
    public function setPartners($partners): static
    {
        $this->partners = $partners;

        return $this;
    }

    /**
     * @param Partner $partner
     *
     * @return $this
     */
    public function addPartner($partner): static
    {
        if (!$this->partners->contains($partner)) {
            $this->partners->add($partner);
            $partner->setEnterprise($this);
        }

        return $this;
    }

    /**
     * @param Partner $partner
     *
     * @return $this
     */
    public function removePartner($partner): static
    {
        if ($this->partners->contains($partner)) {
            $this->partners->removeElement($partner);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getEnterpriseHolidays(): Collection
    {
        return $this->enterpriseHolidays;
    }

    /**
     * @param ArrayCollection $enterpriseHolidays
     *
     * @return $this
     */
    public function setEnterpriseHolidays($enterpriseHolidays): static
    {
        $this->enterpriseHolidays = $enterpriseHolidays;

        return $this;
    }

    /**
     * @param EnterpriseHolidays $enterpriseHoliday
     *
     * @return $this
     */
    public function addEnterpriseHoliday($enterpriseHoliday): static
    {
        if (!$this->enterpriseHolidays->contains($enterpriseHoliday)) {
            $this->enterpriseHolidays->add($enterpriseHoliday);
            $enterpriseHoliday->setEnterprise($this);
        }

        return $this;
    }

    /**
     * @param EnterpriseHolidays $enterpriseHoliday
     *
     * @return $this
     */
    public function removeEnterpriseHoliday($enterpriseHoliday): static
    {
        if ($this->enterpriseHolidays->contains($enterpriseHoliday)) {
            $this->enterpriseHolidays->removeElement($enterpriseHoliday);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSaleTariffs(): Collection
    {
        return $this->saleTariffs;
    }

    /**
     * @param ArrayCollection $saleTariffs
     *
     * @return $this
     */
    public function setSaleTariffs($saleTariffs): static
    {
        $this->saleTariffs = $saleTariffs;

        return $this;
    }

    /**
     * @param SaleTariff $saleTariff
     *
     * @return $this
     */
    public function addSaleTariff($saleTariff): static
    {
        if (!$this->saleTariffs->contains($saleTariff)) {
            $this->saleTariffs->add($saleTariff);
            $saleTariff->setEnterprise($this);
        }

        return $this;
    }

    /**
     * @param SaleTariff $saleTariff
     *
     * @return $this
     */
    public function removeSaleTariff($saleTariff): static
    {
        if ($this->saleTariffs->contains($saleTariff)) {
            $this->saleTariffs->removeElement($saleTariff);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSaleRequests(): Collection
    {
        return $this->saleRequests;
    }

    /**
     * @param ArrayCollection $saleRequests
     *
     * @return $this
     */
    public function setSaleRequests($saleRequests): static
    {
        $this->saleRequests = $saleRequests;

        return $this;
    }

    /**
     * @param SaleRequest $saleRequest
     *
     * @return $this
     */
    public function addSaleRequest($saleRequest): static
    {
        if (!$this->saleRequests->contains($saleRequest)) {
            $this->saleRequests->add($saleRequest);
            $saleRequest->setEnterprise($this);
        }

        return $this;
    }

    /**
     * @param SaleRequest $saleRequest
     *
     * @return $this
     */
    public function removeSaleRequest($saleRequest): static
    {
        if ($this->saleRequests->contains($saleRequest)) {
            $this->saleRequests->removeElement($saleRequest);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getActivityLines(): Collection
    {
        return $this->activityLines;
    }

    /**
     * @param ArrayCollection $activityLines
     */
    public function setActivityLines($activityLines): void
    {
        $this->activityLines = $activityLines;
    }

    /**
     * @param ActivityLine $activityLine
     *
     * @return $this
     */
    public function addActivityLine($activityLine): static
    {
        if (!$this->activityLines->contains($activityLine)) {
            $this->activityLines->add($activityLine);
            $activityLine->setEnterprise($this);
        }

        return $this;
    }

    /**
     * @param ActivityLine $activityLine
     *
     * @return $this
     */
    public function removeActivityLine($activityLine): static
    {
        if ($this->activityLines->contains($activityLine)) {
            $this->activityLines->removeElement($activityLine);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getCollectionDocumentTypes(): Collection
    {
        return $this->collectionDocumentTypes;
    }

    /**
     * @param ArrayCollection $collectionDocumentTypes
     */
    public function setCollectionDocumentTypes($collectionDocumentTypes): void
    {
        $this->collectionDocumentTypes = $collectionDocumentTypes;
    }

    /**
     * @param CollectionDocumentType $collectionDocumentType
     *
     * @return $this
     */
    public function addCollectionDocumentType($collectionDocumentType): static
    {
        if (!$this->collectionDocumentTypes->contains($collectionDocumentType)) {
            $this->collectionDocumentTypes->add($collectionDocumentType);
            $collectionDocumentType->setEnterprise($this);
        }

        return $this;
    }

    public function removeCollectionDocumentType($collectionDocumentType)
    {
        if ($this->collectionDocumentTypes->contains($collectionDocumentType)) {
            $this->collectionDocumentTypes->removeElement($collectionDocumentType);
        }

        return $this;
    }

    public function getDocuments(): ?Collection
    {
        return $this->documents;
    }

    public function setDocuments($documents): self
    {
        $this->documents = $documents;

        return $this;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setEnterprise($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
        }

        return $this;
    }

    /**
     * FacturaE Methods.
     */
    public function getIsLegalEntityFacturaE(): bool
    {
        return true;
    }

    public function getTaxNumberFacturaE(): string
    {
        return $this->getTaxIdentificationNumber();
    }

    public function getNameFacturaE(): string
    {
        return $this->getBusinessName();
    }

    public function getAddressFacturaE(): string
    {
        return $this->getAddress() ?? '';
    }

    public function getPostalCodeFacturaE(): string
    {
        return $this->getCity()->getPostalCode();
    }

    public function getTownFacturaE(): string
    {
        return $this->getCity()->getName();
    }

    public function getProvinceFacturaE(): string
    {
        return $this->getCity()->getProvince()->getName();
    }

    public function getCountryCodeFacturaE(): string
    {
        return Countries::getAlpha3Code($this->getCity()->getProvince()->getCountry());
    }

    public function getEmailFacturaE(): string
    {
        return $this->getEmail();
    }

    public function getFirstSurnameFacturaE(): string
    {
        return '';
    }

    public function getLastSurnameFacturaE(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getName().' · '.$this->getTaxIdentificationNumber() : '---';
    }
}
