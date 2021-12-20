<?php

namespace App\Entity\Operator;

use App\Entity\AbstractBase;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Enterprise\EnterpriseGroupBounty;
use App\Entity\Payslip\Payslip;
use App\Entity\Payslip\PayslipOperatorDefaultLine;
use App\Entity\Sale\SaleRequest;
use App\Entity\Setting\City;
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Operator\OperatorDigitalTachograph", mappedBy="operator", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $operatorDigitalTachographs;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Operator\OperatorChecking", mappedBy="operator", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $operatorCheckings;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Operator\OperatorAbsence", mappedBy="operator", cascade={"persist", "remove"}, orphanRemoval=true)
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
     * Methods.
     */

    /**
     * Operator constructor.
     */
    public function __construct()
    {
        $this->operatorDigitalTachographs = new ArrayCollection();
        $this->operatorCheckings = new ArrayCollection();
        $this->saleRequests = new ArrayCollection();
        $this->operatorVariousAmount = new ArrayCollection();
        $this->payslipOperatorDefaultLines = new ArrayCollection();
        $this->payslips = new ArrayCollection();
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
     * @return Operator
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
     * @return Operator
     */
    public function setTaxIdentificationNumber($taxIdentificationNumber)
    {
        $this->taxIdentificationNumber = $taxIdentificationNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getBancAccountNumber()
    {
        return $this->bancAccountNumber;
    }

    /**
     * @param string $bancAccountNumber
     *
     * @return Operator
     */
    public function setBancAccountNumber($bancAccountNumber)
    {
        $this->bancAccountNumber = $bancAccountNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getSocialSecurityNumber()
    {
        return $this->socialSecurityNumber;
    }

    /**
     * @param string $socialSecurityNumber
     *
     * @return Operator
     */
    public function setSocialSecurityNumber($socialSecurityNumber)
    {
        $this->socialSecurityNumber = $socialSecurityNumber;

        return $this;
    }

    /**
     * @return float
     */
    public function getHourCost()
    {
        return $this->hourCost;
    }

    /**
     * @param float $hourCost
     *
     * @return Operator
     */
    public function setHourCost($hourCost)
    {
        $this->hourCost = $hourCost;

        return $this;
    }

    /**
     * @return string
     */
    public function getSurname1()
    {
        return $this->surname1;
    }

    /**
     * @param string $surname1
     *
     * @return Operator
     */
    public function setSurname1($surname1)
    {
        $this->surname1 = $surname1;

        return $this;
    }

    /**
     * @return string
     */
    public function getSurname2()
    {
        return $this->surname2;
    }

    /**
     * @param string $surname2
     *
     * @return Operator
     */
    public function setSurname2($surname2)
    {
        $this->surname2 = $surname2;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->surname1.' '.$this->surname2.', '.$this->name;
    }

    /**
     * @return string
     */
    public function getShortFullName()
    {
        return $this->surname1.', '.$this->name;
    }

    /**
     * @return string
     */
    public function getUppercaseNameInitials()
    {
        return strtoupper(substr($this->name, 0, 1).substr($this->surname1, 0, 1));
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
     * @return Operator
     */
    public function setAddress($address)
    {
        $this->address = $address;

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
     * @return Operator
     */
    public function setEnterprise($enterprise)
    {
        $this->enterprise = $enterprise;

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
     * @return Operator
     */
    public function setCity($city)
    {
        $this->city = $city;

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
     * @return Operator
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnterpriseMobile()
    {
        return $this->enterpriseMobile;
    }

    /**
     * @param string $enterpriseMobile
     *
     * @return Operator
     */
    public function setEnterpriseMobile($enterpriseMobile)
    {
        $this->enterpriseMobile = $enterpriseMobile;

        return $this;
    }

    /**
     * @return string
     */
    public function getOwnPhone()
    {
        return $this->ownPhone;
    }

    /**
     * @param string $ownPhone
     *
     * @return Operator
     */
    public function setOwnPhone($ownPhone)
    {
        $this->ownPhone = $ownPhone;

        return $this;
    }

    /**
     * @return string
     */
    public function getOwnMobile()
    {
        return $this->ownMobile;
    }

    /**
     * @param string $ownMobile
     *
     * @return Operator
     */
    public function setOwnMobile($ownMobile)
    {
        $this->ownMobile = $ownMobile;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getBrithDate()
    {
        return $this->brithDate;
    }

    /**
     * @return Operator
     */
    public function setBrithDate(DateTime $brithDate)
    {
        $this->brithDate = $brithDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * @return Operator
     */
    public function setRegistrationDate(DateTime $registrationDate)
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    /**
     * @return File
     */
    public function getProfilePhotoImageFile()
    {
        return $this->profilePhotoImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setProfilePhotoImageFile(File $profilePhotoImageFile = null)
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
    public function getProfilePhotoImage()
    {
        return $this->profilePhotoImage;
    }

    /**
     * @param string $profilePhotoImage
     *
     * @return Operator
     */
    public function setProfilePhotoImage($profilePhotoImage)
    {
        $this->profilePhotoImage = $profilePhotoImage;

        return $this;
    }

    /**
     * @return File
     */
    public function getTaxIdentificationNumberImageFile()
    {
        return $this->taxIdentificationNumberImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setTaxIdentificationNumberImageFile(File $taxIdentificationNumberImageFile = null)
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
    public function getTaxIdentificationNumberImage()
    {
        return $this->taxIdentificationNumberImage;
    }

    /**
     * @param string $taxIdentificationNumberImage
     *
     * @return Operator
     */
    public function setTaxIdentificationNumberImage($taxIdentificationNumberImage)
    {
        $this->taxIdentificationNumberImage = $taxIdentificationNumberImage;

        return $this;
    }

    /**
     * @return File
     */
    public function getDrivingLicenseImageFile()
    {
        return $this->drivingLicenseImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setDrivingLicenseImageFile(File $drivingLicenseImageFile = null)
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
    public function getDrivingLicenseImage()
    {
        return $this->drivingLicenseImage;
    }

    /**
     * @param string $drivingLicenseImage
     *
     * @return Operator
     */
    public function setDrivingLicenseImage($drivingLicenseImage)
    {
        $this->drivingLicenseImage = $drivingLicenseImage;

        return $this;
    }

    /**
     * @return File
     */
    public function getCranesOperatorLicenseImageFile()
    {
        return $this->cranesOperatorLicenseImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setCranesOperatorLicenseImageFile(File $cranesOperatorLicenseImageFile = null)
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
    public function getCranesOperatorLicenseImage()
    {
        return $this->cranesOperatorLicenseImage;
    }

    /**
     * @param string $cranesOperatorLicenseImage
     *
     * @return Operator
     */
    public function setCranesOperatorLicenseImage($cranesOperatorLicenseImage)
    {
        $this->cranesOperatorLicenseImage = $cranesOperatorLicenseImage;

        return $this;
    }

    /**
     * @return File
     */
    public function getMedicalCheckImageFile()
    {
        return $this->medicalCheckImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setMedicalCheckImageFile(File $medicalCheckImageFile = null)
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
    public function getMedicalCheckImage()
    {
        return $this->medicalCheckImage;
    }

    /**
     * @param string $medicalCheckImage
     *
     * @return Operator
     */
    public function setMedicalCheckImage($medicalCheckImage)
    {
        $this->medicalCheckImage = $medicalCheckImage;

        return $this;
    }

    /**
     * @return File
     */
    public function getEpisImageFile()
    {
        return $this->episImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setEpisImageFile(File $episImageFile = null)
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
    public function getEpisImage()
    {
        return $this->episImage;
    }

    /**
     * @param string $episImage
     *
     * @return Operator
     */
    public function setEpisImage($episImage)
    {
        $this->episImage = $episImage;

        return $this;
    }

    /**
     * @return File
     */
    public function getTrainingDocumentImageFile()
    {
        return $this->trainingDocumentImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setTrainingDocumentImageFile(File $trainingDocumentImageFile = null)
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
    public function getTrainingDocumentImage()
    {
        return $this->trainingDocumentImage;
    }

    /**
     * @param string $trainingDocumentImage
     *
     * @return Operator
     */
    public function setTrainingDocumentImage($trainingDocumentImage)
    {
        $this->trainingDocumentImage = $trainingDocumentImage;

        return $this;
    }

    /**
     * @return File
     */
    public function getInformationImageFile()
    {
        return $this->informationImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setInformationImageFile(File $informationImageFile = null)
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
    public function getInformationImage()
    {
        return $this->informationImage;
    }

    /**
     * @param string $informationImage
     *
     * @return Operator
     */
    public function setInformationImage($informationImage)
    {
        $this->informationImage = $informationImage;

        return $this;
    }

    /**
     * @return File
     */
    public function getUseOfMachineryAuthorizationImageFile()
    {
        return $this->useOfMachineryAuthorizationImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setUseOfMachineryAuthorizationImageFile(File $useOfMachineryAuthorizationImageFile = null)
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
    public function getUseOfMachineryAuthorizationImage()
    {
        return $this->useOfMachineryAuthorizationImage;
    }

    /**
     * @param string $useOfMachineryAuthorizationImage
     *
     * @return Operator
     */
    public function setUseOfMachineryAuthorizationImage($useOfMachineryAuthorizationImage)
    {
        $this->useOfMachineryAuthorizationImage = $useOfMachineryAuthorizationImage;

        return $this;
    }

    /**
     * @return File
     */
    public function getDischargeSocialSecurityImageFile()
    {
        return $this->dischargeSocialSecurityImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setDischargeSocialSecurityImageFile(File $dischargeSocialSecurityImageFile = null)
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
    public function getDischargeSocialSecurityImage()
    {
        return $this->dischargeSocialSecurityImage;
    }

    /**
     * @param string $dischargeSocialSecurityImage
     *
     * @return Operator
     */
    public function setDischargeSocialSecurityImage($dischargeSocialSecurityImage)
    {
        $this->dischargeSocialSecurityImage = $dischargeSocialSecurityImage;

        return $this;
    }

    /**
     * @return File
     */
    public function getEmploymentContractImageFile()
    {
        return $this->employmentContractImageFile;
    }

    /**
     * @return Operator
     *
     * @throws \Exception
     */
    public function setEmploymentContractImageFile(File $employmentContractImageFile = null)
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
    public function getEmploymentContractImage()
    {
        return $this->employmentContractImage;
    }

    /**
     * @param string $employmentContractImage
     *
     * @return Operator
     */
    public function setEmploymentContractImage($employmentContractImage)
    {
        $this->employmentContractImage = $employmentContractImage;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHasCarDrivingLicense()
    {
        return $this->hasCarDrivingLicense;
    }

    /**
     * @param bool $hasCarDrivingLicense
     *
     * @return Operator
     */
    public function setHasCarDrivingLicense($hasCarDrivingLicense)
    {
        $this->hasCarDrivingLicense = $hasCarDrivingLicense;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHasLorryDrivingLicense()
    {
        return $this->hasLorryDrivingLicense;
    }

    /**
     * @param bool $hasLorryDrivingLicense
     *
     * @return Operator
     */
    public function setHasLorryDrivingLicense($hasLorryDrivingLicense)
    {
        $this->hasLorryDrivingLicense = $hasLorryDrivingLicense;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHasTowingDrivingLicense()
    {
        return $this->hasTowingDrivingLicense;
    }

    /**
     * @param bool $hasTowingDrivingLicense
     *
     * @return Operator
     */
    public function setHasTowingDrivingLicense($hasTowingDrivingLicense)
    {
        $this->hasTowingDrivingLicense = $hasTowingDrivingLicense;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHasCraneDrivingLicense()
    {
        return $this->hasCraneDrivingLicense;
    }

    /**
     * @param bool $hasCraneDrivingLicense
     *
     * @return Operator
     */
    public function setHasCraneDrivingLicense($hasCraneDrivingLicense)
    {
        $this->hasCraneDrivingLicense = $hasCraneDrivingLicense;

        return $this;
    }

    /**
     * @return string
     */
    public function getShoeSize()
    {
        return $this->shoeSize;
    }

    /**
     * @param string $shoeSize
     *
     * @return Operator
     */
    public function setShoeSize($shoeSize)
    {
        $this->shoeSize = $shoeSize;

        return $this;
    }

    /**
     * @return string
     */
    public function getJerseytSize()
    {
        return $this->jerseytSize;
    }

    /**
     * @param string $jerseytSize
     *
     * @return Operator
     */
    public function setJerseytSize($jerseytSize)
    {
        $this->jerseytSize = $jerseytSize;

        return $this;
    }

    /**
     * @return string
     */
    public function getJacketSize()
    {
        return $this->jacketSize;
    }

    /**
     * @param string $jacketSize
     *
     * @return Operator
     */
    public function setJacketSize($jacketSize)
    {
        $this->jacketSize = $jacketSize;

        return $this;
    }

    /**
     * @return string
     */
    public function getTShirtSize()
    {
        return $this->tShirtSize;
    }

    /**
     * @param string $tShirtSize
     *
     * @return Operator
     */
    public function setTShirtSize($tShirtSize)
    {
        $this->tShirtSize = $tShirtSize;

        return $this;
    }

    /**
     * @return string
     */
    public function getPantSize()
    {
        return $this->pantSize;
    }

    /**
     * @param string $pantSize
     *
     * @return Operator
     */
    public function setPantSize($pantSize)
    {
        $this->pantSize = $pantSize;

        return $this;
    }

    /**
     * @return string
     */
    public function getWorkingDressSize()
    {
        return $this->workingDressSize;
    }

    /**
     * @param string $workingDressSize
     *
     * @return Operator
     */
    public function setWorkingDressSize($workingDressSize)
    {
        $this->workingDressSize = $workingDressSize;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getOperatorDigitalTachographs()
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
    public function setOperatorDigitalTachographs($digitalTachographs)
    {
        $this->operatorDigitalTachographs = $digitalTachographs;

        return $this;
    }

    /**
     * @return $this
     */
    public function addOperatorDigitalTachograph(OperatorDigitalTachograph $digitalTachograph)
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
    public function removeOperatorDigitalTachograph(OperatorDigitalTachograph $digitalTachograph)
    {
        if ($this->operatorDigitalTachographs->contains($digitalTachograph)) {
            $this->operatorDigitalTachographs->removeElement($digitalTachograph);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getOperatorCheckings()
    {
        return $this->operatorCheckings->filter(function (OperatorChecking $operatorChecking) {
            return $operatorChecking->getEnd() > new Datetime();
        });
    }

    /**
     * @param $operatorCheckings
     *
     * @return $this
     */
    public function setOperatorCheckings($operatorCheckings)
    {
        $this->operatorCheckings = $operatorCheckings;

        return $this;
    }

    /**
     * @return $this
     */
    public function addOperatorChecking(OperatorChecking $operatorChecking)
    {
        if (!$this->operatorCheckings->contains($operatorChecking)) {
            $this->operatorCheckings->add($operatorChecking);
            $operatorChecking->setOperator($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeOperatorChecking(OperatorChecking $operatorChecking)
    {
        if ($this->operatorCheckings->contains($operatorChecking)) {
            $this->operatorCheckings->removeElement($operatorChecking);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getOperatorAbsences()
    {
        $date = new DateTime();
        $date->setDate($date->format('Y') * 1 - 1, 1, 1);
        $operatorAbsences = $this->operatorAbsences->filter(function (OperatorAbsence $operatorAbsence) use ($date) {
            return $operatorAbsence->getEnd()->getTimestamp() > $date->getTimestamp();
        });
        $iterator = $operatorAbsences->getIterator();
        $iterator->uasort(function (OperatorAbsence $a, OperatorAbsence $b) {
            return ($a->getEnd() > $b->getEnd()) ? -1 : 1;
        });

        return new ArrayCollection(iterator_to_array($iterator));
    }

    /**
     * @param $operatorAbsences
     *
     * @return $this
     */
    public function setOperatorAbsences($operatorAbsences)
    {
        $this->operatorAbsences = $operatorAbsences;

        return $this;
    }

    /**
     * @return $this
     */
    public function addOperatorAbsence(OperatorAbsence $operatorAbsence)
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
    public function removeOperatorAbsence(OperatorAbsence $operatorAbsence)
    {
        if ($this->operatorAbsences->contains($operatorAbsence)) {
            $this->operatorAbsences->removeElement($operatorAbsence);
        }

        return $this;
    }

    /**
     * @return EnterpriseGroupBounty
     */
    public function getEnterpriseGroupBounty()
    {
        return $this->enterpriseGroupBounty;
    }

    /**
     * @param EnterpriseGroupBounty $enterpriseGroupBounty
     *
     * @return $this
     */
    public function setEnterpriseGroupBounty($enterpriseGroupBounty)
    {
        $this->enterpriseGroupBounty = $enterpriseGroupBounty;

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
            $saleRequest->setOperator($this);
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

    public function getWorkRegisterHeaders(): Collection
    {
        return $this->workRegisterHeaders;
    }

    /**
     * @return $this
     */
    public function setWorkRegisterHeaders(ArrayCollection $workRegisterHeaders): Operator
    {
        $this->workRegisterHeaders = $workRegisterHeaders;

        return $this;
    }

    /**
     * @return $this
     */
    public function addWorkRegisterHeader(OperatorWorkRegisterHeader $workRegisterHeader): Operator
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
    public function removeWorkRegisterHeader(OperatorWorkRegisterHeader $workRegisterHeader): Operator
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
    public function setPayslipOperatorDefaultLines(ArrayCollection $payslipOperatorDefaultLines): Operator
    {
        $this->payslipOperatorDefaultLines = $payslipOperatorDefaultLines;

        return $this;
    }

    /**
     * @return $this
     */
    public function addPayslipOperatorDefaultLine(PayslipOperatorDefaultLine $payslipOperatorDefaultLine): Operator
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
    public function removePayslipOperatorDefaultLine(PayslipOperatorDefaultLine $payslipOperatorDefaultLine): Operator
    {
        if ($this->payslipOperatorDefaultLines->contains($payslipOperatorDefaultLine)) {
            $this->payslipOperatorDefaultLines->removeElement($payslipOperatorDefaultLine);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getOperatorVariousAmount()
    {
        return $this->operatorVariousAmount;
    }

    /**
     * @param ArrayCollection $operatorVariousAmount
     *
     * @return $this
     */
    public function setOperatorVariousAmount($operatorVariousAmount)
    {
        $this->operatorVariousAmount = $operatorVariousAmount;

        return $this;
    }

    /**
     * @param OperatorVariousAmount $operatorVariousAmount
     *
     * @return $this
     */
    public function addOperatorVariousAmount($operatorVariousAmount)
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
    public function removeOperatorVariousAmount($operatorVariousAmount)
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
    public function setPayslips(ArrayCollection $payslips): Operator
    {
        $this->payslips = $payslips;

        return $this;
    }

    /**
     * @return $this
     */
    public function addPayslip(Payslip $payslip): Operator
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
    public function removePayslip(Payslip $payslip): Operator
    {
        if ($this->payslips->contains($payslip)) {
            $this->payslips->removeElement($payslip);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getFullName() : '---';
    }
}
