<?php

namespace App\Entity\Operator;

use App\Entity\AbstractBase;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Enterprise\EnterpriseGroupBounty;
use App\Entity\Payslip\Payslip;
use App\Entity\Payslip\PayslipOperatorDefaultLine;
use App\Entity\Sale\SaleRequest;
use App\Entity\Setting\City;
use App\Entity\Setting\Document;
use App\Enum\OperatorCheckingTypeCategoryEnum;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DoctrineExtensions\Query\Mysql\Date;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class Operator.
 *
 * @ORM\Entity(repositoryClass="App\Repository\Operator\OperatorRepository")
 * @ORM\Table(name="operator")
 * @Vich\Uploadable()
 * @UniqueEntity({"enterprise", "taxIdentificationNumber"})
 */
class Operator extends AbstractBase
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
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
    private $bancAccountNumber;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $socialSecurityNumber;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $hourCost;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $surname1;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $surname2;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $address;

    /**
     * @var Enterprise
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\Enterprise")
     */
    private $enterprise;

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
    private $enterpriseMobile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $ownPhone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $ownMobile;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private $brithDate;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private $registrationDate;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="operator", fileNameProperty="profilePhotoImage")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $profilePhotoImageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $profilePhotoImage;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="operator", fileNameProperty="taxIdentificationNumberImage")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $taxIdentificationNumberImageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $taxIdentificationNumberImage;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="operator", fileNameProperty="drivingLicenseImage")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $drivingLicenseImageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $drivingLicenseImage;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="operator", fileNameProperty="cranesOperatorLicenseImage")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $cranesOperatorLicenseImageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $cranesOperatorLicenseImage;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="operator", fileNameProperty="medicalCheckImage")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $medicalCheckImageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $medicalCheckImage;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="operator", fileNameProperty="episImage")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $episImageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $episImage;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="operator", fileNameProperty="trainingDocumentImage")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $trainingDocumentImageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $trainingDocumentImage;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="operator", fileNameProperty="informationImage")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $informationImageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $informationImage;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="operator", fileNameProperty="useOfMachineryAuthorizationImage")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $useOfMachineryAuthorizationImageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $useOfMachineryAuthorizationImage;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="operator", fileNameProperty="dischargeSocialSecurityImage")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $dischargeSocialSecurityImageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $dischargeSocialSecurityImage;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="operator", fileNameProperty="employmentContractImage")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private $employmentContractImageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $employmentContractImage;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $hasCarDrivingLicense = true;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $hasLorryDrivingLicense = true;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $hasTowingDrivingLicense = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $hasCraneDrivingLicense = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $shoeSize;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $jerseytSize;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $jacketSize;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $tShirtSize;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $pantSize;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $workingDressSize;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private int $type = 0;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Operator\OperatorDigitalTachograph", mappedBy="operator", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $operatorDigitalTachographs;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Operator\OperatorChecking", mappedBy="operator", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $operatorCheckings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Operator\OperatorCheckingPpe", mappedBy="operator", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $operatorCheckingPpes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Operator\OperatorCheckingTraining", mappedBy="operator", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $operatorCheckingTrainings;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Operator\OperatorAbsence", mappedBy="operator", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"begin" = "DESC"})
     */
    private $operatorAbsences;

    /**
     * @var EnterpriseGroupBounty
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\EnterpriseGroupBounty", inversedBy="operators")
     */
    private $enterpriseGroupBounty;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleRequest", mappedBy="operator")
     */
    private $saleRequests;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Operator\OperatorWorkRegisterHeader", mappedBy="operator")
     */
    private $workRegisterHeaders;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Payslip\Payslip", mappedBy="operator", cascade={"persist", "remove"})
     */
    private Collection $payslips;

    /**
     * @var ?Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Payslip\PayslipOperatorDefaultLine", mappedBy="operator", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private ?Collection $payslipOperatorDefaultLines;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Operator\OperatorVariousAmount", mappedBy="operator")
     */
    private $operatorVariousAmount;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Purchase\PurchaseInvoiceLine", mappedBy="operator")
     */
    private Collection $purchaseInvoiceLines;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Setting\Document", mappedBy="operator", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"description" = "ASC"})
     */
    private ?Collection $documents = null;

    /**
     * Methods.
     */
    public function __construct()
    {
        $this->operatorDigitalTachographs = new ArrayCollection();
        $this->operatorCheckings = new ArrayCollection();
        $this->operatorCheckingPpes = new ArrayCollection();
        $this->operatorCheckingTrainings = new ArrayCollection();
        $this->operatorAbsences = new ArrayCollection();
        $this->saleRequests = new ArrayCollection();
        $this->operatorVariousAmount = new ArrayCollection();
        $this->payslipOperatorDefaultLines = new ArrayCollection();
        $this->payslips = new ArrayCollection();
        $this->purchaseInvoiceLines = new ArrayCollection();
        $this->documents = new ArrayCollection();
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
     * @return Operator
     */
    public function setName($name): Operator
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
     * @return Operator
     */
    public function setTaxIdentificationNumber($taxIdentificationNumber): Operator
    {
        $this->taxIdentificationNumber = $taxIdentificationNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getBancAccountNumber(): string
    {
        return $this->bancAccountNumber;
    }

    /**
     * @param string $bancAccountNumber
     *
     * @return Operator
     */
    public function setBancAccountNumber($bancAccountNumber): Operator
    {
        $this->bancAccountNumber = $bancAccountNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getSocialSecurityNumber(): string
    {
        return $this->socialSecurityNumber;
    }

    /**
     * @param string $socialSecurityNumber
     *
     * @return Operator
     */
    public function setSocialSecurityNumber($socialSecurityNumber): Operator
    {
        $this->socialSecurityNumber = $socialSecurityNumber;

        return $this;
    }

    /**
     * @return float
     */
    public function getHourCost(): float
    {
        return $this->hourCost;
    }

    /**
     * @param float $hourCost
     *
     * @return Operator
     */
    public function setHourCost($hourCost): Operator
    {
        $this->hourCost = $hourCost;

        return $this;
    }

    /**
     * @return string
     */
    public function getSurname1(): string
    {
        return $this->surname1;
    }

    /**
     * @param string $surname1
     *
     * @return Operator
     */
    public function setSurname1($surname1): Operator
    {
        $this->surname1 = $surname1;

        return $this;
    }

    /**
     * @return string
     */
    public function getSurname2(): string
    {
        return $this->surname2;
    }

    /**
     * @param string $surname2
     *
     * @return Operator
     */
    public function setSurname2($surname2): Operator
    {
        $this->surname2 = $surname2;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->surname1.' '.$this->surname2.', '.$this->name;
    }

    /**
     * @return string
     */
    public function getShortFullName(): string
    {
        return $this->surname1.', '.$this->name;
    }

    /**
     * @return string
     */
    public function getUppercaseNameInitials(): string
    {
        return strtoupper(substr($this->name, 0, 1).substr($this->surname1, 0, 1));
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
     * @return Operator
     */
    public function setAddress($address): Operator
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Enterprise
     */
    public function getEnterprise(): Enterprise
    {
        return $this->enterprise;
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return Operator
     */
    public function setEnterprise($enterprise): Operator
    {
        $this->enterprise = $enterprise;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    /**
     * @param City $city
     *
     * @return Operator
     */
    public function setCity($city): Operator
    {
        $this->city = $city;

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
     * @return Operator
     */
    public function setEmail($email): Operator
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnterpriseMobile(): string
    {
        return $this->enterpriseMobile;
    }

    /**
     * @param string $enterpriseMobile
     *
     * @return Operator
     */
    public function setEnterpriseMobile($enterpriseMobile): Operator
    {
        $this->enterpriseMobile = $enterpriseMobile;

        return $this;
    }

    /**
     * @return string
     */
    public function getOwnPhone(): string
    {
        return $this->ownPhone;
    }

    /**
     * @param string $ownPhone
     *
     * @return Operator
     */
    public function setOwnPhone($ownPhone): Operator
    {
        $this->ownPhone = $ownPhone;

        return $this;
    }

    /**
     * @return string
     */
    public function getOwnMobile(): string
    {
        return $this->ownMobile;
    }

    /**
     * @param string $ownMobile
     *
     * @return Operator
     */
    public function setOwnMobile($ownMobile): Operator
    {
        $this->ownMobile = $ownMobile;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getBrithDate(): DateTime
    {
        return $this->brithDate;
    }

    /**
     * @return Operator
     */
    public function setBrithDate(DateTime $brithDate): Operator
    {
        $this->brithDate = $brithDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getRegistrationDate(): DateTime
    {
        return $this->registrationDate;
    }

    /**
     * @return Operator
     */
    public function setRegistrationDate(DateTime $registrationDate): Operator
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }


    public function getProfilePhotoImageFile(): ?File
    {
        return $this->profilePhotoImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setProfilePhotoImageFile(File $profilePhotoImageFile = null): Operator
    {
        $this->profilePhotoImageFile = $profilePhotoImageFile;
        if ($profilePhotoImageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getProfilePhotoImage(): ?string
    {
        return $this->profilePhotoImage;
    }

    /**
     * @param string $profilePhotoImage
     *
     * @return Operator
     */
    public function setProfilePhotoImage($profilePhotoImage): Operator
    {
        $this->profilePhotoImage = $profilePhotoImage;

        return $this;
    }


    public function getTaxIdentificationNumberImageFile(): ?File
    {
        return $this->taxIdentificationNumberImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setTaxIdentificationNumberImageFile(File $taxIdentificationNumberImageFile = null): Operator
    {
        $this->taxIdentificationNumberImageFile = $taxIdentificationNumberImageFile;
        if ($taxIdentificationNumberImageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTaxIdentificationNumberImage(): ?string
    {
        return $this->taxIdentificationNumberImage;
    }

    /**
     * @param string $taxIdentificationNumberImage
     *
     * @return Operator
     */
    public function setTaxIdentificationNumberImage($taxIdentificationNumberImage): Operator
    {
        $this->taxIdentificationNumberImage = $taxIdentificationNumberImage;

        return $this;
    }


    public function getDrivingLicenseImageFile(): ?File
    {
        return $this->drivingLicenseImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setDrivingLicenseImageFile(File $drivingLicenseImageFile = null): Operator
    {
        $this->drivingLicenseImageFile = $drivingLicenseImageFile;
        if ($drivingLicenseImageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDrivingLicenseImage(): ?string
    {
        return $this->drivingLicenseImage;
    }

    /**
     * @param string $drivingLicenseImage
     *
     * @return Operator
     */
    public function setDrivingLicenseImage($drivingLicenseImage): Operator
    {
        $this->drivingLicenseImage = $drivingLicenseImage;

        return $this;
    }


    public function getCranesOperatorLicenseImageFile(): ?File
    {
        return $this->cranesOperatorLicenseImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setCranesOperatorLicenseImageFile(File $cranesOperatorLicenseImageFile = null): Operator
    {
        $this->cranesOperatorLicenseImageFile = $cranesOperatorLicenseImageFile;
        if ($cranesOperatorLicenseImageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getCranesOperatorLicenseImage(): ?string
    {
        return $this->cranesOperatorLicenseImage;
    }

    /**
     * @param string $cranesOperatorLicenseImage
     *
     * @return Operator
     */
    public function setCranesOperatorLicenseImage($cranesOperatorLicenseImage): Operator
    {
        $this->cranesOperatorLicenseImage = $cranesOperatorLicenseImage;

        return $this;
    }


    public function getMedicalCheckImageFile(): ?File
    {
        return $this->medicalCheckImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setMedicalCheckImageFile(File $medicalCheckImageFile = null): Operator
    {
        $this->medicalCheckImageFile = $medicalCheckImageFile;
        if ($medicalCheckImageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getMedicalCheckImage(): ?string
    {
        return $this->medicalCheckImage;
    }

    /**
     * @param string $medicalCheckImage
     *
     * @return Operator
     */
    public function setMedicalCheckImage($medicalCheckImage): Operator
    {
        $this->medicalCheckImage = $medicalCheckImage;

        return $this;
    }


    public function getEpisImageFile(): ?File
    {
        return $this->episImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setEpisImageFile(File $episImageFile = null): Operator
    {
        $this->episImageFile = $episImageFile;
        if ($episImageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getEpisImage(): ?string
    {
        return $this->episImage;
    }

    /**
     * @param string $episImage
     *
     * @return Operator
     */
    public function setEpisImage($episImage): Operator
    {
        $this->episImage = $episImage;

        return $this;
    }


    public function getTrainingDocumentImageFile(): ?File
    {
        return $this->trainingDocumentImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setTrainingDocumentImageFile(File $trainingDocumentImageFile = null): Operator
    {
        $this->trainingDocumentImageFile = $trainingDocumentImageFile;
        if ($trainingDocumentImageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTrainingDocumentImage(): ?string
    {
        return $this->trainingDocumentImage;
    }

    /**
     * @param string $trainingDocumentImage
     *
     * @return Operator
     */
    public function setTrainingDocumentImage($trainingDocumentImage): Operator
    {
        $this->trainingDocumentImage = $trainingDocumentImage;

        return $this;
    }


    public function getInformationImageFile(): ?File
    {
        return $this->informationImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setInformationImageFile(File $informationImageFile = null): Operator
    {
        $this->informationImageFile = $informationImageFile;
        if ($informationImageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getInformationImage(): ?string
    {
        return $this->informationImage;
    }

    /**
     * @param string $informationImage
     *
     * @return Operator
     */
    public function setInformationImage($informationImage): Operator
    {
        $this->informationImage = $informationImage;

        return $this;
    }


    public function getUseOfMachineryAuthorizationImageFile(): ?File
    {
        return $this->useOfMachineryAuthorizationImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setUseOfMachineryAuthorizationImageFile(File $useOfMachineryAuthorizationImageFile = null): Operator
    {
        $this->useOfMachineryAuthorizationImageFile = $useOfMachineryAuthorizationImageFile;
        if ($useOfMachineryAuthorizationImageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getUseOfMachineryAuthorizationImage(): ?string
    {
        return $this->useOfMachineryAuthorizationImage;
    }

    /**
     * @param string $useOfMachineryAuthorizationImage
     *
     * @return Operator
     */
    public function setUseOfMachineryAuthorizationImage($useOfMachineryAuthorizationImage): Operator
    {
        $this->useOfMachineryAuthorizationImage = $useOfMachineryAuthorizationImage;

        return $this;
    }


    public function getDischargeSocialSecurityImageFile(): ?File
    {
        return $this->dischargeSocialSecurityImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setDischargeSocialSecurityImageFile(File $dischargeSocialSecurityImageFile = null): Operator
    {
        $this->dischargeSocialSecurityImageFile = $dischargeSocialSecurityImageFile;
        if ($dischargeSocialSecurityImageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDischargeSocialSecurityImage(): ?string
    {
        return $this->dischargeSocialSecurityImage;
    }

    /**
     * @param string $dischargeSocialSecurityImage
     *
     * @return Operator
     */
    public function setDischargeSocialSecurityImage($dischargeSocialSecurityImage): Operator
    {
        $this->dischargeSocialSecurityImage = $dischargeSocialSecurityImage;

        return $this;
    }


    public function getEmploymentContractImageFile(): ?File
    {
        return $this->employmentContractImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setEmploymentContractImageFile(File $employmentContractImageFile = null): Operator
    {
        $this->employmentContractImageFile = $employmentContractImageFile;
        if ($employmentContractImageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getEmploymentContractImage(): ?string
    {
        return $this->employmentContractImage;
    }

    /**
     * @param string $employmentContractImage
     *
     * @return Operator
     */
    public function setEmploymentContractImage($employmentContractImage): Operator
    {
        $this->employmentContractImage = $employmentContractImage;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHasCarDrivingLicense(): bool
    {
        return $this->hasCarDrivingLicense;
    }

    /**
     * @param bool $hasCarDrivingLicense
     *
     * @return Operator
     */
    public function setHasCarDrivingLicense($hasCarDrivingLicense): Operator
    {
        $this->hasCarDrivingLicense = $hasCarDrivingLicense;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHasLorryDrivingLicense(): bool
    {
        return $this->hasLorryDrivingLicense;
    }

    /**
     * @param bool $hasLorryDrivingLicense
     *
     * @return Operator
     */
    public function setHasLorryDrivingLicense($hasLorryDrivingLicense): Operator
    {
        $this->hasLorryDrivingLicense = $hasLorryDrivingLicense;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHasTowingDrivingLicense(): bool
    {
        return $this->hasTowingDrivingLicense;
    }

    /**
     * @param bool $hasTowingDrivingLicense
     *
     * @return Operator
     */
    public function setHasTowingDrivingLicense($hasTowingDrivingLicense): Operator
    {
        $this->hasTowingDrivingLicense = $hasTowingDrivingLicense;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHasCraneDrivingLicense(): bool
    {
        return $this->hasCraneDrivingLicense;
    }

    /**
     * @param bool $hasCraneDrivingLicense
     *
     * @return Operator
     */
    public function setHasCraneDrivingLicense($hasCraneDrivingLicense): Operator
    {
        $this->hasCraneDrivingLicense = $hasCraneDrivingLicense;

        return $this;
    }

    /**
     * @return string
     */
    public function getShoeSize(): string
    {
        return $this->shoeSize;
    }

    /**
     * @param string $shoeSize
     *
     * @return Operator
     */
    public function setShoeSize($shoeSize): Operator
    {
        $this->shoeSize = $shoeSize;

        return $this;
    }

    /**
     * @return string
     */
    public function getJerseytSize(): string
    {
        return $this->jerseytSize;
    }

    /**
     * @param string $jerseytSize
     *
     * @return Operator
     */
    public function setJerseytSize($jerseytSize): Operator
    {
        $this->jerseytSize = $jerseytSize;

        return $this;
    }

    /**
     * @return string
     */
    public function getJacketSize(): string
    {
        return $this->jacketSize;
    }

    /**
     * @param string $jacketSize
     *
     * @return Operator
     */
    public function setJacketSize($jacketSize): Operator
    {
        $this->jacketSize = $jacketSize;

        return $this;
    }

    /**
     * @return string
     */
    public function getTShirtSize(): string
    {
        return $this->tShirtSize;
    }

    /**
     * @param string $tShirtSize
     *
     * @return Operator
     */
    public function setTShirtSize($tShirtSize): Operator
    {
        $this->tShirtSize = $tShirtSize;

        return $this;
    }

    /**
     * @return string
     */
    public function getPantSize(): string
    {
        return $this->pantSize;
    }

    /**
     * @param string $pantSize
     *
     * @return Operator
     */
    public function setPantSize($pantSize): Operator
    {
        $this->pantSize = $pantSize;

        return $this;
    }

    /**
     * @return string
     */
    public function getWorkingDressSize(): string
    {
        return $this->workingDressSize;
    }

    /**
     * @param string $workingDressSize
     *
     * @return Operator
     */
    public function setWorkingDressSize($workingDressSize): Operator
    {
        $this->workingDressSize = $workingDressSize;

        return $this;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): Operator
    {
        $this->type = $type;

        return $this;
    }

    public function getOperatorDigitalTachographs(): Collection
    {
        $lastId = $this->operatorDigitalTachographs->last() ? $this->operatorDigitalTachographs->last()->getId() : null;

        return $this->operatorDigitalTachographs->filter(function (OperatorDigitalTachograph $operatorDigitalTachograph) use ($lastId) {
            return $operatorDigitalTachograph->getId() === $lastId;
        });
    }

    /**
     * @param $digitalTachographs
     *
     * @return $this
     */
    public function setOperatorDigitalTachographs($digitalTachographs): static
    {
        $this->operatorDigitalTachographs = $digitalTachographs;

        return $this;
    }

    /**
     * @return $this
     */
    public function addOperatorDigitalTachograph(OperatorDigitalTachograph $digitalTachograph): static
    {
        if (!$this->operatorDigitalTachographs->contains($digitalTachograph)) {
            $this->operatorDigitalTachographs->add($digitalTachograph);
            $digitalTachograph->setOperator($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeOperatorDigitalTachograph(OperatorDigitalTachograph $digitalTachograph): static
    {
        if ($this->operatorDigitalTachographs->contains($digitalTachograph)) {
            $this->operatorDigitalTachographs->removeElement($digitalTachograph);
        }

        return $this;
    }

    public function getOperatorCheckings(): Collection
    {
        return $this->operatorCheckings
            ->filter($this->filterOperatorCheckings(OperatorCheckingTypeCategoryEnum::CHECKING))
        ;
    }

    public function getOperatorCheckingPpes(): Collection
    {
        return $this->operatorCheckingPpes
            ->filter($this->filterOperatorCheckings(OperatorCheckingTypeCategoryEnum::PPE))
        ;
    }

    public function getOperatorCheckingTrainings(): Collection
    {
        return $this->operatorCheckingTrainings
            ->filter($this->filterOperatorCheckings(OperatorCheckingTypeCategoryEnum::TRAINING))
        ;
    }

    public function setOperatorCheckings($operatorCheckings): Operator
    {
        $this->operatorCheckings = $operatorCheckings;

        return $this;
    }

    public function setOperatorCheckingPpes($operatorCheckings): Operator
    {
        $this->operatorCheckingPpes = $operatorCheckings;

        return $this;
    }

    public function setOperatorCheckingTrainings($operatorCheckings): Operator
    {
        $this->operatorCheckingTrainings = $operatorCheckings;

        return $this;
    }

    public function addOperatorChecking(OperatorChecking $operatorChecking): Operator
    {
        if (!$this->operatorCheckings->contains($operatorChecking)) {
            $this->operatorCheckings->add($operatorChecking);
            $operatorChecking->setOperator($this);
        }

        return $this;
    }

    public function removeOperatorChecking(OperatorChecking $operatorChecking): Operator
    {
        if ($this->operatorCheckings->contains($operatorChecking)) {
            $this->operatorCheckings->removeElement($operatorChecking);
        }

        return $this;
    }

    public function addOperatorCheckingPpe(OperatorCheckingPpe $operatorChecking): Operator
    {
        if (!$this->operatorCheckingPpes->contains($operatorChecking)) {
            $this->operatorCheckingPpes->add($operatorChecking);
            $operatorChecking->setOperator($this);
        }

        return $this;
    }

    public function removeOperatorCheckingPpe(OperatorCheckingPpe $operatorChecking): Operator
    {
        if ($this->operatorCheckingPpes->contains($operatorChecking)) {
            $this->operatorCheckingPpes->removeElement($operatorChecking);
        }

        return $this;
    }

    public function addOperatorCheckingTraining(OperatorCheckingTraining $operatorChecking): Operator
    {
        if (!$this->operatorCheckingTrainings->contains($operatorChecking)) {
            $this->operatorCheckingTrainings->add($operatorChecking);
            $operatorChecking->setOperator($this);
        }

        return $this;
    }

    public function removeOperatorCheckingTraining(OperatorCheckingTraining $operatorChecking): Operator
    {
        if ($this->operatorCheckingTrainings->contains($operatorChecking)) {
            $this->operatorCheckingTrainings->removeElement($operatorChecking);
        }

        return $this;
    }

    public function getOperatorAbsences(): Collection
    {
        return $this->operatorAbsences;
    }

    /**
     * @param $operatorAbsences
     *
     * @return $this
     */
    public function setOperatorAbsences($operatorAbsences): static
    {
        $this->operatorAbsences = $operatorAbsences;

        return $this;
    }

    /**
     * @return $this
     */
    public function addOperatorAbsence(OperatorAbsence $operatorAbsence): static
    {
        if (!$this->operatorAbsences->contains($operatorAbsence)) {
            $this->operatorAbsences->add($operatorAbsence);
            $operatorAbsence->setOperator($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeOperatorAbsence(OperatorAbsence $operatorAbsence): static
    {
        if ($this->operatorAbsences->contains($operatorAbsence)) {
            $this->operatorAbsences->removeElement($operatorAbsence);
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
            $document->setOperator($this);
        }

        return $this;
    }

    public function removeDocument(Document $document)
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
        }

        return $this;
    }

    /**
     * @return EnterpriseGroupBounty
     */
    public function getEnterpriseGroupBounty(): ?EnterpriseGroupBounty
    {
        return $this->enterpriseGroupBounty;
    }

    /**
     * @param EnterpriseGroupBounty $enterpriseGroupBounty
     *
     * @return $this
     */
    public function setEnterpriseGroupBounty($enterpriseGroupBounty): static
    {
        $this->enterpriseGroupBounty = $enterpriseGroupBounty;

        return $this;
    }

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
            $saleRequest->setOperator($this);
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

    public function getWorkRegisterHeaders(): Collection
    {
        return $this->workRegisterHeaders;
    }

    /**
     * @return $this
     */
    public function setWorkRegisterHeaders(ArrayCollection $workRegisterHeaders): static
    {
        $this->workRegisterHeaders = $workRegisterHeaders;

        return $this;
    }

    /**
     * @return $this
     */
    public function addWorkRegisterHeader(OperatorWorkRegisterHeader $workRegisterHeader): static
    {
        if (!$this->workRegisterHeaders->contains($workRegisterHeader)) {
            $this->workRegisterHeaders->add($workRegisterHeader);
            $workRegisterHeader->setOperator($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeWorkRegisterHeader(OperatorWorkRegisterHeader $workRegisterHeader): static
    {
        if ($this->workRegisterHeaders->contains($workRegisterHeader)) {
            $this->workRegisterHeaders->removeElement($workRegisterHeader);
        }

        return $this;
    }

    public function getPayslipOperatorDefaultLines(): ?Collection
    {
        return $this->payslipOperatorDefaultLines;
    }

    /**
     * @return $this
     */
    public function setPayslipOperatorDefaultLines(ArrayCollection $payslipOperatorDefaultLines): static
    {
        $this->payslipOperatorDefaultLines = $payslipOperatorDefaultLines;

        return $this;
    }

    /**
     * @return $this
     */
    public function addPayslipOperatorDefaultLine(PayslipOperatorDefaultLine $payslipOperatorDefaultLine): static
    {
        if (!$this->payslipOperatorDefaultLines->contains($payslipOperatorDefaultLine)) {
            $this->payslipOperatorDefaultLines->add($payslipOperatorDefaultLine);
            $payslipOperatorDefaultLine->setOperator($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removePayslipOperatorDefaultLine(PayslipOperatorDefaultLine $payslipOperatorDefaultLine): static
    {
        if ($this->payslipOperatorDefaultLines->contains($payslipOperatorDefaultLine)) {
            $this->payslipOperatorDefaultLines->removeElement($payslipOperatorDefaultLine);
        }

        return $this;
    }

    public function getOperatorVariousAmount(): Collection
    {
        return $this->operatorVariousAmount;
    }

    /**
     * @param ArrayCollection $operatorVariousAmount
     *
     * @return $this
     */
    public function setOperatorVariousAmount($operatorVariousAmount): static
    {
        $this->operatorVariousAmount = $operatorVariousAmount;

        return $this;
    }

    /**
     * @param OperatorVariousAmount $operatorVariousAmount
     *
     * @return $this
     */
    public function addOperatorVariousAmount($operatorVariousAmount): static
    {
        if (!$this->operatorVariousAmount->contains($operatorVariousAmount)) {
            $this->operatorVariousAmount->add($operatorVariousAmount);
            $operatorVariousAmount->setOperator($this);
        }

        return $this;
    }

    /**
     * @param OperatorVariousAmount $operatorVariousAmount
     *
     * @return $this
     */
    public function removeOperatorVariousAmount($operatorVariousAmount): static
    {
        if ($this->operatorVariousAmount->contains($operatorVariousAmount)) {
            $this->operatorVariousAmount->removeElement($operatorVariousAmount);
        }

        return $this;
    }

    public function getPayslips(): Collection
    {
        return $this->payslips;
    }

    /**
     * @return $this
     */
    public function setPayslips(ArrayCollection $payslips): static
    {
        $this->payslips = $payslips;

        return $this;
    }

    /**
     * @return $this
     */
    public function addPayslip(Payslip $payslip): static
    {
        if (!$this->payslips->contains($payslip)) {
            $this->payslips->add($payslip);
            $payslip->setOperator($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removePayslip(Payslip $payslip): static
    {
        if ($this->payslips->contains($payslip)) {
            $this->payslips->removeElement($payslip);
        }

        return $this;
    }

    public function getPurchaseInvoiceLines(): Collection
    {
        return $this->purchaseInvoiceLines;
    }

    public function setPurchaseInvoiceLines(Collection $purchaseInvoiceLines): Operator
    {
        $this->purchaseInvoiceLines = $purchaseInvoiceLines;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? (!$this->getEnabled() ? '¡Inactivo! - ' : '').$this->getFullName() : '---';
    }

    protected function filterOperatorCheckings(int $operatorCheckingCategory): \Closure
    {
        return function (OperatorChecking|OperatorCheckingPpe|OperatorCheckingTraining $operatorChecking) use ($operatorCheckingCategory) {
            if (!$operatorChecking->getId()) {
                return true;
            }

            return $operatorChecking->getType()?->getCategory() === $operatorCheckingCategory;
        };
    }
}
