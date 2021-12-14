<?php

namespace App\Admin;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Setting\User;
use App\Entity\Vehicle\VehicleMaintenance;
use App\Manager\DeliveryNoteManager;
use App\Manager\InvoiceManager;
use App\Manager\RepositoriesManager;
use App\Manager\VehicleMaintenanceManager;
use App\Manager\YearChoicesManager;
use App\Service\FileService;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\String\UnicodeString;
use Twig\Environment;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Class BaseAdmin.
 *
 * @category Admin
 */
abstract class AbstractBaseAdmin extends AbstractAdmin
{
    private UploaderHelper $vus;

    private CacheManager $lis;

    protected YearChoicesManager $ycm;

    protected InvoiceManager $im;

    protected RepositoriesManager $rm;

    protected DeliveryNoteManager $dnm;

    protected VehicleMaintenanceManager $vmm;

    protected EntityManagerInterface $em;

    protected FileService $fs;

    private Environment $tws;

    protected TokenStorageInterface $ts;

    protected AuthorizationCheckerInterface $acs;

    protected UserPasswordEncoder $passwordEncoder;

    /**
     * @var array
     */
    protected $perPageOptions = [25, 50, 100, 200];

    /**
     * @var int
     */
    protected $maxPerPage = 25;

    /**
     * Methods.
     */

    /**
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     */
    public function __construct($code, $class, $baseControllerName, CacheManager $lis, YearChoicesManager $ycm, InvoiceManager $im, RepositoriesManager $rm, DeliveryNoteManager $dnm, VehicleMaintenanceManager $vmm, EntityManagerInterface $em, FileService $fs, Environment $tws, TokenStorageInterface $ts, AuthorizationCheckerInterface $acs, UserPasswordEncoder $passwordEncoder)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->vus = $fs->getUhs();
        $this->lis = $lis;
        $this->ycm = $ycm;
        $this->im = $im;
        $this->rm = $rm;
        $this->dnm = $dnm;
        $this->vmm = $vmm;
        $this->em = $em;
        $this->fs = $fs;
        $this->tws = $tws;
        $this->ts = $ts;
        $this->acs = $acs;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('show')
            ->remove('batch')
        ;
    }

    public function configureBatchActions(array $actions): array
    {
        $actions = parent::getBatchActions();
        unset($actions['delete']);

        return $actions;
    }

    public function getExportFormats(): array
    {
        return [
            'csv',
            'xls',
        ];
    }

    protected function getDefaultFormBoxArray(string $bootstrapGrid = 'md', string $bootstrapSize = '6', string $boxClass = 'primary'): array
    {
        return [
            'class' => 'col-'.$bootstrapGrid.'-'.$bootstrapSize,
            'box_class' => 'box box-'.$boxClass,
        ];
    }

    protected function getFormMdSuccessBoxArray(string $bootstrapColSize = '6'): array
    {
        return $this->getDefaultFormBoxArray('md', $bootstrapColSize, 'success');
    }

    /**
     * Get image helper form mapper with thumbnail.
     */
    protected function getMainImageHelperFormMapperWithThumbnail(): string
    {
        return ($this->getSubject() ? $this->getSubject()->getMainImage() ? '<img src="'.
                $this->getRouteGenerator()->generate(
                    'admin_app_vehicle_vehicle_downloadMainImage',
                    ['id' => $this->getSubject()->getId()]
                )
                .'" class="admin-preview img-responsive" alt="thumbnail"/>' : '' : '').
            '<p style="width:100%;display:block;margin-top:10px">* imatge amplada mínima 1.200 píxels<br>* arxiu pes màxim 10MB<br>* format JPG o PNG</p>';
    }

    /**
     * Get image helper form mapper with thumbnail.
     */
    protected function getImageHelperFormMapperWithThumbnail(): string
    {
        return ($this->getSubject() ? $this->getSubject()->getImage() ? '<img src="'.$this->lis->getBrowserPath(
                $this->vus->asset($this->getSubject(), 'imageFile'),
                '480xY'
            ).'" class="admin-preview img-responsive" alt="thumbnail"/>' : '' : '').'<span style="width:100%;display:block;">amplada mínima 1200px (màx. 10MB amb JPG o PNG)</span>';
    }

    /**
     * Get image helper form mapper with thumbnail.
     */
    protected function getLogoHelperFormMapperWithThumbnail(): string
    {
        return ($this->getSubject() ? $this->getSubject()->getLogo() ? '<img src="'.$this->lis->getBrowserPath(
                $this->vus->asset($this->getSubject(), 'logoFile'),
                '480xY'
            ).'" class="admin-preview img-responsive" alt="thumbnail"/>' : '' : '').'<span style="width:100%;display:block;">amplada mínima 300px (màx. 10MB amb JPG o PNG)</span>';
    }

    /**
     * Get image helper form mapper with thumbnail.
     */
    protected function getProfileHelperFormMapperWithThumbnail(): string
    {
        return ($this->getSubject() ? $this->getSubject()->getProfilePhotoImage() ? '<img src="'.
                $this->getRouteGenerator()->generate(
                    'admin_app_operator_operator_downloadProfilePhotoImage',
                    ['id' => $this->getSubject()->getId()]
                )
                .'" class="admin-preview img-responsive" alt="thumbnail"/>' : '' : '').'<span style="width:100%;display:block;">amplada mínima 300px (màx. 10MB amb JPG o PNG)</span>';
    }

    /**
     * Get image helper form mapper with thumbnail.
     */
    protected function getSmartHelper(string $attribute, string $uploaderMapping): string
    {
        if ($this->getSubject() && $this->getSubject()->$attribute()) {
            if ($this->fs->isPdf($this->getSubject(), $uploaderMapping)) {
                // PDF case
                return $this->tws->render('admin/helpers/pdf.html.twig', [
                    'attribute' => $this->getSubject()->$attribute(),
                    'subject' => $this->getSubject(),
                    'uploaderMapping' => $uploaderMapping,
                ]);
            } else {
                // Image case
                return $this->tws->render('admin/helpers/image.html.twig', [
                    'attribute' => $this->getSubject()->$attribute(),
                    'subject' => $this->getSubject(),
                    'uploaderMapping' => $uploaderMapping,
                ]);
            }
        } else {
            // Undefined case
            return '<span style="width:100%;display:block;">Pots adjuntar un PDF o una imatge d\'una amplada mínima de 1200px. Pes màxim 10MB.</span>';
        }
    }

    protected function getDocumentHelper($route, $document): string
    {
        $docFunction = new UnicodeString($document);
        $isPdf = false;
        $fileAvailable = false;
        $object = $this->getSubject();
        $docFunction = 'get'.$docFunction->camel()->title();
        $fileName = $object->$docFunction();
        if ($fileName) {
            $fileAvailable = true;
            if (strpos($fileName, '.pdf')) {
                $isPdf = true;
            }
        }

        return $this->tws->render(
            'admin/helpers/document.html.twig', [
                'documentSrc' => $this->getRouteGenerator()->generate(
                    $route,
                    ['id' => $object->getId()]
                ),
                'object' => $object,
                'fileAvailable' => $fileAvailable,
                'isPdf' => $isPdf,
                'fileName' => $fileName,
            ]
        );
    }

    /**
     * Get image helper form mapper with thumbnail for black&white.
     */
    protected function getImageHelperFormMapperWithThumbnailBW(): string
    {
        return ($this->getSubject() ? $this->getSubject()->getImageNameBW() ? '<img src="'.$this->lis->getBrowserPath(
                $this->vus->asset($this->getSubject(), 'imageFileBW'),
                '480xY'
            ).'" class="admin-preview img-responsive" alt="thumbnail"/>' : '' : '').'<span style="width:100%;display:block;">amplada mínima 1200px (màx. 10MB amb JPG o PNG)</span>';
    }

    protected function getImageHelperFormMapperWithThumbnailGif(): string
    {
        return ($this->getSubject() ? $this->getSubject()->getGifName() ? '<img src="'.$this->lis->getBrowserPath(
                $this->vus->asset($this->getSubject(), 'gifFile'),
                '480xY'
            ).'" class="admin-preview img-responsive" alt="thumbnail"/>' : '' : '').'<span style="width:100%;display:block;">mida 780x1168px (màx. 10MB amb GIF)</span>';
    }

    protected function getDownloadPdfButton(): string
    {
        $result = '';
        if ($this->getSubject() && !is_null($this->getSubject()->getAttatchmentPDF())) {
            $result = '<a class="btn btn-warning btn-xs" href="'.$this->vus->asset($this->getSubject(), 'attatchmentPDFFile').'" download="'.$this->getSubject()->getName().'.pdf"><i class="fa fa-file-pdf-o"></i> Document PDF</a>';
        }

        return $result;
    }

    protected function getDownloadDigitalTachographButton(): string
    {
        $result = '';
        if ($this->getSubject() && !is_null($this->getSubject()->getUploadedFileName())) {
            $url = $this->getRouteGenerator()->generateUrl($this, 'download', ['id' => $this->getSubject()->getId()]);
            $result = '<a class="btn btn-warning" role="button" href="'.$url.'"><i class="fa fa-download"></i> Descarregar arxiu</a>';
        }

        return $result;
    }

    /**
     * @return Enterprise
     */
    protected function getUserLogedEnterprise()
    {
        return $this->getUser()->getLoggedEnterprise();
    }

    /**
     * @return int
     */
    protected function getUserLogedId()
    {
        return $this->getUser()->getId();
    }

    /**
     * @return User|object
     */
    protected function getUser()
    {
        return $this->ts->getToken()->getUser();
    }

    protected function disablePreviousMaintenance(VehicleMaintenance $vehicleMaintenance)
    {
        $otherVehicleMaintenances = $this->rm->getVehicleMaintenanceRepository()->findBy(
            [
                'vehicle' => $vehicleMaintenance->getVehicle(),
                'vehicleMaintenanceTask' => $vehicleMaintenance->getVehicleMaintenanceTask(),
                'enabled' => true,
            ]
        );
        /** @var VehicleMaintenance $otherVehicleMaintenance */
        foreach ($otherVehicleMaintenances as $otherVehicleMaintenance) {
            if ($otherVehicleMaintenance->getDate() <= $vehicleMaintenance->getDate()) {
                $otherVehicleMaintenance->setEnabled(false);
                $vehicleMaintenance->setEnabled(true);
            } else {
                $otherVehicleMaintenance->setEnabled(true);
                $vehicleMaintenance->setEnabled(false);
            }
            $this->em->persist($otherVehicleMaintenance);
            $this->em->flush();
        }
    }
}
