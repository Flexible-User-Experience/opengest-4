<?php

namespace App\Entity\Enterprise;

use App\Entity\AbstractBase;
use App\Entity\Partner\Partner;
use App\Entity\Sale\SaleRequest;
use App\Entity\Sale\SaleTariff;
use App\Entity\Setting\City;
use App\Entity\Setting\User;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Mirmit\EFacturaBundle\Interfaces\SellerFacturaEInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
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
class Enterprise extends AbstractBase implements \Serializable, SellerFacturaEInterface
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
     * @return Enterprise
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getTaxIdentificationNumber()
    {
        return $this->taxIdentificationNumber;
    }

    /**
     * @param string $taxIdentificationNumber
     *
     * @return Enterprise
     */
    public function setTaxIdentificationNumber($taxIdentificationNumber)
    {
        $this->taxIdentificationNumber = $taxIdentificationNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessName()
    {
        return $this->businessName;
    }

    /**
     * @param string $businessName
     *
     * @return Enterprise
     */
    public function setBusinessName($businessName)
    {
        $this->businessName = $businessName;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     *
     * @return Enterprise
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param City $city
     *
     * @return Enterprise
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone1()
    {
        return $this->phone1;
    }

    /**
     * @param string $phone1
     *
     * @return Enterprise
     */
    public function setPhone1($phone1)
    {
        $this->phone1 = $phone1;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone2()
    {
        return $this->phone2;
    }

    /**
     * @param string $phone2
     *
     * @return Enterprise
     */
    public function setPhone2($phone2)
    {
        $this->phone2 = $phone2;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone3()
    {
        return $this->phone3;
    }

    /**
     * @param string $phone3
     *
     * @return Enterprise
     */
    public function setPhone3($phone3)
    {
        $this->phone3 = $phone3;

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
     * @return Enterprise
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
     * @return Enterprise
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
     * @return Enterprise
     */
    public function setWww($www)
    {
        $this->www = $www;

        return $this;
    }

    /**
     * @return File
     */
    public function getLogoFile()
    {
        return $this->logoFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setLogoFile(File $logoFile = null)
    {
        $this->logoFile = $logoFile;
        if ($logoFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param string $logo
     *
     * @return Enterprise
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return File
     */
    public function getDeedOfIncorporationFile()
    {
        return $this->deedOfIncorporationFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setDeedOfIncorporationFile(File $deedOfIncorporationFile = null)
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
    public function getDeedOfIncorporation()
    {
        return $this->deedOfIncorporation;
    }

    /**
     * @param string $deedOfIncorporation
     *
     * @return Enterprise
     */
    public function setDeedOfIncorporation($deedOfIncorporation)
    {
        $this->deedOfIncorporation = $deedOfIncorporation;

        return $this;
    }

    /**
     * @return File
     */
    public function getTaxIdentificationNumberCardFile()
    {
        return $this->taxIdentificationNumberCardFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setTaxIdentificationNumberCardFile(File $taxIdentificationNumberCardFile = null)
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
    public function getTaxIdentificationNumberCard()
    {
        return $this->taxIdentificationNumberCard;
    }

    /**
     * @param string $taxIdentificationNumberCard
     *
     * @return Enterprise
     */
    public function setTaxIdentificationNumberCard($taxIdentificationNumberCard)
    {
        $this->taxIdentificationNumberCard = $taxIdentificationNumberCard;

        return $this;
    }

    /**
     * @return File
     */
    public function getTc1ReceiptFile()
    {
        return $this->tc1ReceiptFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setTc1ReceiptFile(File $tc1ReceiptFile = null)
    {
        $this->tc1ReceiptFile = $tc1ReceiptFile;
        if ($tc1ReceiptFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTc1Receipt()
    {
        return $this->tc1Receipt;
    }

    /**
     * @param string $tc1Receipt
     *
     * @return Enterprise
     */
    public function setTc1Receipt($tc1Receipt)
    {
        $this->tc1Receipt = $tc1Receipt;

        return $this;
    }

    /**
     * @return File
     */
    public function getTc2ReceiptFile()
    {
        return $this->tc2ReceiptFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setTc2ReceiptFile(File $tc2ReceiptFile = null)
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
    public function getTc2Receipt()
    {
        return $this->tc2Receipt;
    }

    /**
     * @param string $tc2Receipt
     *
     * @return Enterprise
     */
    public function setTc2Receipt($tc2Receipt)
    {
        $this->tc2Receipt = $tc2Receipt;

        return $this;
    }

    /**
     * @return File
     */
    public function getSsRegistrationFile()
    {
        return $this->ssRegistrationFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setSsRegistrationFile(File $ssRegistrationFile = null)
    {
        $this->ssRegistrationFile = $ssRegistrationFile;
        if ($ssRegistrationFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getSsRegistration()
    {
        return $this->ssRegistration;
    }

    /**
     * @param string $ssRegistration
     *
     * @return Enterprise
     */
    public function setSsRegistration($ssRegistration)
    {
        $this->ssRegistration = $ssRegistration;

        return $this;
    }

    /**
     * @return File
     */
    public function getSsPaymentCertificateFile()
    {
        return $this->ssPaymentCertificateFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setSsPaymentCertificateFile(File $ssPaymentCertificateFile = null)
    {
        $this->ssPaymentCertificateFile = $ssPaymentCertificateFile;
        if ($ssPaymentCertificateFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getSsPaymentCertificate()
    {
        return $this->ssPaymentCertificate;
    }

    /**
     * @param string $ssPaymentCertificate
     *
     * @return Enterprise
     */
    public function setSsPaymentCertificate($ssPaymentCertificate)
    {
        $this->ssPaymentCertificate = $ssPaymentCertificate;

        return $this;
    }

    /**
     * @return File
     */
    public function getRc1InsuranceFile()
    {
        return $this->rc1InsuranceFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setRc1InsuranceFile(File $rc1InsuranceFile = null)
    {
        $this->rc1InsuranceFile = $rc1InsuranceFile;
        if ($rc1InsuranceFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getRc1Insurance()
    {
        return $this->rc1Insurance;
    }

    /**
     * @param string $rc1Insurance
     *
     * @return Enterprise
     */
    public function setRc1Insurance($rc1Insurance)
    {
        $this->rc1Insurance = $rc1Insurance;

        return $this;
    }

    /**
     * @return File
     */
    public function getRc2InsuranceFile()
    {
        return $this->rc2InsuranceFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setRc2InsuranceFile(File $rc2InsuranceFile = null)
    {
        $this->rc2InsuranceFile = $rc2InsuranceFile;
        if ($rc2InsuranceFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getRc2Insurance()
    {
        return $this->rc2Insurance;
    }

    /**
     * @param string $rc2Insurance
     *
     * @return Enterprise
     */
    public function setRc2Insurance($rc2Insurance)
    {
        $this->rc2Insurance = $rc2Insurance;

        return $this;
    }

    /**
     * @return File
     */
    public function getRcReceiptFile()
    {
        return $this->rcReceiptFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setRcReceiptFile(File $rcReceiptFile = null)
    {
        $this->rcReceiptFile = $rcReceiptFile;
        if ($rcReceiptFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getRcReceipt()
    {
        return $this->rcReceipt;
    }

    /**
     * @param string $rcReceipt
     *
     * @return Enterprise
     */
    public function setRcReceipt($rcReceipt)
    {
        $this->rcReceipt = $rcReceipt;

        return $this;
    }

    /**
     * @return File
     */
    public function getPreventionServiceContractFile()
    {
        return $this->preventionServiceContractFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setPreventionServiceContractFile(File $preventionServiceContractFile = null)
    {
        $this->preventionServiceContractFile = $preventionServiceContractFile;
        if ($preventionServiceContractFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPreventionServiceContract()
    {
        return $this->preventionServiceContract;
    }

    /**
     * @param string $preventionServiceContract
     *
     * @return Enterprise
     */
    public function setPreventionServiceContract($preventionServiceContract)
    {
        $this->preventionServiceContract = $preventionServiceContract;

        return $this;
    }

    /**
     * @return File
     */
    public function getPreventionServiceInvoiceFile()
    {
        return $this->preventionServiceInvoiceFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setPreventionServiceInvoiceFile(File $preventionServiceInvoiceFile = null)
    {
        $this->preventionServiceInvoiceFile = $preventionServiceInvoiceFile;
        if ($preventionServiceInvoiceFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPreventionServiceInvoice()
    {
        return $this->preventionServiceInvoice;
    }

    /**
     * @param string $preventionServiceInvoice
     *
     * @return Enterprise
     */
    public function setPreventionServiceInvoice($preventionServiceInvoice)
    {
        $this->preventionServiceInvoice = $preventionServiceInvoice;

        return $this;
    }

    /**
     * @return File
     */
    public function getPreventionServiceReceiptFile()
    {
        return $this->preventionServiceReceiptFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setPreventionServiceReceiptFile(File $preventionServiceReceiptFile = null)
    {
        $this->preventionServiceReceiptFile = $preventionServiceReceiptFile;
        if ($this->preventionServiceReceiptFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPreventionServiceReceipt()
    {
        return $this->preventionServiceReceipt;
    }

    /**
     * @param string $preventionServiceReceipt
     *
     * @return Enterprise
     */
    public function setPreventionServiceReceipt($preventionServiceReceipt)
    {
        $this->preventionServiceReceipt = $preventionServiceReceipt;

        return $this;
    }

    /**
     * @return File
     */
    public function getOccupationalAccidentsInsuranceFile()
    {
        return $this->occupationalAccidentsInsuranceFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setOccupationalAccidentsInsuranceFile(File $occupationalAccidentsInsuranceFile = null)
    {
        $this->occupationalAccidentsInsuranceFile = $occupationalAccidentsInsuranceFile;
        if ($occupationalAccidentsInsuranceFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getOccupationalAccidentsInsurance()
    {
        return $this->occupationalAccidentsInsurance;
    }

    /**
     * @param string $occupationalAccidentsInsurance
     *
     * @return Enterprise
     */
    public function setOccupationalAccidentsInsurance($occupationalAccidentsInsurance)
    {
        $this->occupationalAccidentsInsurance = $occupationalAccidentsInsurance;

        return $this;
    }

    /**
     * @return File
     */
    public function getOccupationalReceiptFile()
    {
        return $this->occupationalReceiptFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setOccupationalReceiptFile(File $occupationalReceiptFile = null)
    {
        $this->occupationalReceiptFile = $occupationalReceiptFile;
        if ($this->occupationalReceiptFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getOccupationalReceipt()
    {
        return $this->occupationalReceipt;
    }

    /**
     * @param string $occupationalReceipt
     *
     * @return Enterprise
     */
    public function setOccupationalReceipt($occupationalReceipt)
    {
        $this->occupationalReceipt = $occupationalReceipt;

        return $this;
    }

    /**
     * @return File
     */
    public function getLaborRiskAssessmentFile()
    {
        return $this->laborRiskAssessmentFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setLaborRiskAssessmentFile(File $laborRiskAssessmentFile = null)
    {
        $this->laborRiskAssessmentFile = $laborRiskAssessmentFile;
        if ($laborRiskAssessmentFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getLaborRiskAssessment()
    {
        return $this->laborRiskAssessment;
    }

    /**
     * @param string $laborRiskAssessment
     *
     * @return Enterprise
     */
    public function setLaborRiskAssessment($laborRiskAssessment)
    {
        $this->laborRiskAssessment = $laborRiskAssessment;

        return $this;
    }

    /**
     * @return File
     */
    public function getSecurityPlanFile()
    {
        return $this->securityPlanFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setSecurityPlanFile(File $securityPlanFile = null)
    {
        $this->securityPlanFile = $securityPlanFile;
        if ($securityPlanFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getSecurityPlan()
    {
        return $this->securityPlan;
    }

    /**
     * @param string $securityPlan
     *
     * @return Enterprise
     */
    public function setSecurityPlan($securityPlan)
    {
        $this->securityPlan = $securityPlan;

        return $this;
    }

    /**
     * @return File
     */
    public function getReaCertificateFile()
    {
        return $this->reaCertificateFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setReaCertificateFile(File $reaCertificateFile = null)
    {
        $this->reaCertificateFile = $reaCertificateFile;
        if ($reaCertificateFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getReaCertificate()
    {
        return $this->reaCertificate;
    }

    /**
     * @param string $reaCertificate
     *
     * @return Enterprise
     */
    public function setReaCertificate($reaCertificate)
    {
        $this->reaCertificate = $reaCertificate;

        return $this;
    }

    /**
     * @return File
     */
    public function getOilCertificateFile()
    {
        return $this->oilCertificateFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setOilCertificateFile(File $oilCertificateFile = null)
    {
        $this->oilCertificateFile = $oilCertificateFile;
        if ($oilCertificateFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getOilCertificate()
    {
        return $this->oilCertificate;
    }

    /**
     * @param string $oilCertificate
     *
     * @return Enterprise
     */
    public function setOilCertificate($oilCertificate)
    {
        $this->oilCertificate = $oilCertificate;

        return $this;
    }

    /**
     * @return File
     */
    public function getGencatPaymentCertificateFile()
    {
        return $this->gencatPaymentCertificateFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setGencatPaymentCertificateFile(File $gencatPaymentCertificateFile = null)
    {
        $this->gencatPaymentCertificateFile = $gencatPaymentCertificateFile;
        if ($gencatPaymentCertificateFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getGencatPaymentCertificate()
    {
        return $this->gencatPaymentCertificate;
    }

    /**
     * @param string $gencatPaymentCertificate
     *
     * @return Enterprise
     */
    public function setGencatPaymentCertificate($gencatPaymentCertificate)
    {
        $this->gencatPaymentCertificate = $gencatPaymentCertificate;

        return $this;
    }

    /**
     * @return File
     */
    public function getDeedsOfPowersFile()
    {
        return $this->deedsOfPowersFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setDeedsOfPowersFile(File $deedsOfPowersFile = null)
    {
        $this->deedsOfPowersFile = $deedsOfPowersFile;
        if ($deedsOfPowersFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDeedsOfPowers()
    {
        return $this->deedsOfPowers;
    }

    /**
     * @param string $deedsOfPowers
     *
     * @return Enterprise
     */
    public function setDeedsOfPowers($deedsOfPowers)
    {
        $this->deedsOfPowers = $deedsOfPowers;

        return $this;
    }

    /**
     * @return File
     */
    public function getIaeRegistrationFile()
    {
        return $this->iaeRegistrationFile;
    }

    /**
     * @param File|null $iaeRegistrationFile
     *
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setIaeRegistrationFile($iaeRegistrationFile)
    {
        $this->iaeRegistrationFile = $iaeRegistrationFile;
        if ($iaeRegistrationFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getIaeRegistration()
    {
        return $this->iaeRegistration;
    }

    /**
     * @param string $iaeRegistration
     *
     * @return Enterprise
     */
    public function setIaeRegistration($iaeRegistration)
    {
        $this->iaeRegistration = $iaeRegistration;

        return $this;
    }

    /**
     * @return File
     */
    public function getIaeReceiptFile()
    {
        return $this->iaeReceiptFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setIaeReceiptFile(File $iaeReceiptFile = null)
    {
        $this->iaeReceiptFile = $iaeReceiptFile;
        if ($this->iaeReceiptFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIaeReceipt()
    {
        return $this->iaeReceipt;
    }

    /**
     * @param mixed $iaeReceipt
     *
     * @return Enterprise
     */
    public function setIaeReceipt($iaeReceipt)
    {
        $this->iaeReceipt = $iaeReceipt;

        return $this;
    }

    /**
     * @return File
     */
    public function getMutualPartnershipFile()
    {
        return $this->mutualPartnershipFile;
    }

    /**
     * @return Enterprise
     *
     * @throws Exception
     */
    public function setMutualPartnershipFile(File $mutualPartnershipFile = null)
    {
        $this->mutualPartnershipFile = $mutualPartnershipFile;
        if ($mutualPartnershipFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getMutualPartnership()
    {
        return $this->mutualPartnership;
    }

    /**
     * @param string $mutualPartnership
     *
     * @return Enterprise
     */
    public function setMutualPartnership($mutualPartnership)
    {
        $this->mutualPartnership = $mutualPartnership;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param ArrayCollection $users
     *
     * @return Enterprise
     */
    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @return $this
     */
    public function addUser(User $user)
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
    public function removeUser(User $user)
    {
        $user->removeEnterprise($this);
        $this->users->removeElement($user);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getEnterpriseGroupBounties()
    {
        return $this->enterpriseGroupBounties;
    }

    /**
     * @param EnterpriseGroupBounty $enterpriseGroupBounties
     *
     * @return $this
     */
    public function setEnterpriseGroupBounties($enterpriseGroupBounties)
    {
        $this->enterpriseGroupBounties = $enterpriseGroupBounties;

        return $this;
    }

    /**
     * @return $this
     */
    public function addEnterpriseGroupBounty(EnterpriseGroupBounty $enterpriseGroupBounty)
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
    public function removeEnterpriseGroupBounty(EnterpriseGroupBounty $enterpriseGroupBounty)
    {
        if ($this->enterpriseGroupBounties->contains($enterpriseGroupBounty)) {
            $this->enterpriseGroupBounties->removeElement($enterpriseGroupBounty);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getEnterpriseTransferAccounts()
    {
        return $this->enterpriseTransferAccounts;
    }

    /**
     * @param ArrayCollection $enterpriseTransferAccounts
     *
     * @return $this
     */
    public function setEnterpriseTransferAccounts($enterpriseTransferAccounts)
    {
        $this->enterpriseTransferAccounts = $enterpriseTransferAccounts;

        return $this;
    }

    /**
     * @param EnterpriseTransferAccount $enterpriseTransferAccount
     *
     * @return $this
     */
    public function addEnterpriseTransferAccount($enterpriseTransferAccount)
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
    public function removeEnterpriseTransferAccount($enterpriseTransferAccount)
    {
        if ($this->enterpriseTransferAccounts->contains($enterpriseTransferAccount)) {
            $this->enterpriseTransferAccounts->removeElement($enterpriseTransferAccount);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPartners()
    {
        return $this->partners;
    }

    /**
     * @param ArrayCollection $partners
     *
     * @return $this
     */
    public function setPartners($partners)
    {
        $this->partners = $partners;

        return $this;
    }

    /**
     * @param Partner $partner
     *
     * @return $this
     */
    public function addPartner($partner)
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
    public function removePartner($partner)
    {
        if ($this->partners->contains($partner)) {
            $this->partners->removeElement($partner);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getEnterpriseHolidays()
    {
        return $this->enterpriseHolidays;
    }

    /**
     * @param ArrayCollection $enterpriseHolidays
     *
     * @return $this
     */
    public function setEnterpriseHolidays($enterpriseHolidays)
    {
        $this->enterpriseHolidays = $enterpriseHolidays;

        return $this;
    }

    /**
     * @param EnterpriseHolidays $enterpriseHoliday
     *
     * @return $this
     */
    public function addEnterpriseHoliday($enterpriseHoliday)
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
    public function removeEnterpriseHoliday($enterpriseHoliday)
    {
        if ($this->enterpriseHolidays->contains($enterpriseHoliday)) {
            $this->enterpriseHolidays->removeElement($enterpriseHoliday);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSaleTariffs()
    {
        return $this->saleTariffs;
    }

    /**
     * @param ArrayCollection $saleTariffs
     *
     * @return $this
     */
    public function setSaleTariffs($saleTariffs)
    {
        $this->saleTariffs = $saleTariffs;

        return $this;
    }

    /**
     * @param SaleTariff $saleTariff
     *
     * @return $this
     */
    public function addSaleTariff($saleTariff)
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
    public function removeSaleTariff($saleTariff)
    {
        if ($this->saleTariffs->contains($saleTariff)) {
            $this->saleTariffs->removeElement($saleTariff);
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
            $saleRequest->setEnterprise($this);
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
    public function getActivityLines()
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
    public function addActivityLine($activityLine)
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
    public function removeActivityLine($activityLine)
    {
        if ($this->activityLines->contains($activityLine)) {
            $this->activityLines->removeElement($activityLine);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getCollectionDocumentTypes()
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
    public function addCollectionDocumentType($collectionDocumentType)
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
        return $this->getAddress();
    }

    public function getPostalCodeFacturaE(): string
    {
        // TODO return actual postal code
        return '';
    }

    public function getTownFacturaE(): string
    {
        // TODO return actual town
        return '';
    }

    public function getProvinceFacturaE(): string
    {
        // TODO return actual province
        return '';
    }

    public function getCountryCodeFacturaE(): string
    {
        // TODO return actual country
        return 'ES';
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
    public function __toString()
    {
        return $this->id ? $this->getName().' · '.$this->getTaxIdentificationNumber() : '---';
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->logo,
        ]);
    }

    public function unserialize($data)
    {
        list(
            $this->id) = unserialize($data);
    }
}
