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
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Templating\EngineInterface;
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

    private EngineInterface $tws;

    protected TokenStorageInterface $ts;

    protected AuthorizationCheckerInterface $acs;

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
    public function __construct($code, $class, $baseControllerName, CacheManager $lis, YearChoicesManager $ycm, InvoiceManager $im, RepositoriesManager $rm, DeliveryNoteManager $dnm, VehicleMaintenanceManager $vmm, EntityManagerInterface $em, FileService $fs, EngineInterface $tws, TokenStorageInterface $ts, AuthorizationCheckerInterface $acs)
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
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('show')
            ->remove('batch')
        ;
    }

    /**
     * @return array
     */
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();
        unset($actions['delete']);

        return $actions;
    }

    /**
     * @return array
     */
    public function getExportFormats()
    {
        return [
            'csv',
            'xls',
        ];
    }

    /**
     * @param string $bootstrapGrid
     * @param string $bootstrapSize
     * @param string $boxClass
     *
     * @return array
     */
    protected function getDefaultFormBoxArray($bootstrapGrid = 'md', $bootstrapSize = '6', $boxClass = 'primary')
    {
        return [
            'class' => 'col-'.$bootstrapGrid.'-'.$bootstrapSize,
            'box_class' => 'box box-'.$boxClass,
        ];
    }

    /**
     * @param string $bootstrapColSize
     *
     * @return array
     */
    protected function getFormMdSuccessBoxArray($bootstrapColSize = '6')
    {
        return $this->getDefaultFormBoxArray('md', $bootstrapColSize, 'success');
    }

    /**
     * Get image helper form mapper with thumbnail.
     *
     * @return string
     */
    protected function getMainImageHelperFormMapperWithThumbnail()
    {
        return ($this->getSubject() ? $this->getSubject()->getMainImage() ? '<img src="'.$this->lis->getBrowserPath(
                $this->vus->asset($this->getSubject(), 'mainImageFile'),
                '480xY'
            ).'" class="admin-preview img-responsive" alt="thumbnail"/>' : '' : '').'<p style="width:100%;display:block;margin-top:10px">* imatge amplada mínima 1.200 píxels<br>* arxiu pes màxim 10MB<br>* format JPG o PNG</p>';
    }

    /**
     * Get image helper form mapper with thumbnail.
     *
     * @return string
     */
    protected function getImageHelperFormMapperWithThumbnail()
    {
        return ($this->getSubject() ? $this->getSubject()->getImage() ? '<img src="'.$this->lis->getBrowserPath(
                $this->vus->asset($this->getSubject(), 'imageFile'),
                '480xY'
            ).'" class="admin-preview img-responsive" alt="thumbnail"/>' : '' : '').'<span style="width:100%;display:block;">amplada mínima 1200px (màx. 10MB amb JPG o PNG)</span>';
    }

    /**
     * Get image helper form mapper with thumbnail.
     *
     * @return string
     */
    protected function getLogoHelperFormMapperWithThumbnail()
    {
        return ($this->getSubject() ? $this->getSubject()->getLogo() ? '<img src="'.$this->lis->getBrowserPath(
                $this->vus->asset($this->getSubject(), 'logoFile'),
                '480xY'
            ).'" class="admin-preview img-responsive" alt="thumbnail"/>' : '' : '').'<span style="width:100%;display:block;">amplada mínima 300px (màx. 10MB amb JPG o PNG)</span>';
    }

    /**
     * Get image helper form mapper with thumbnail.
     *
     * @return string
     */
    protected function getProfileHelperFormMapperWithThumbnail()
    {
        return ($this->getSubject() ? $this->getSubject()->getProfilePhotoImage() ? '<img src="'.
                $this->routeGenerator->generate(
                    'admin_app_operator_operator_downloadProfilePhotoImage',
                    ['id' => $this->getSubject()->getId()]
                )
                .'" class="admin-preview img-responsive" alt="thumbnail"/>' : '' : '').'<span style="width:100%;display:block;">amplada mínima 300px (màx. 10MB amb JPG o PNG)</span>';
    }

    /**
     * Get image helper form mapper with thumbnail.
     *
     * @param string $attribute
     * @param string $uploaderMapping
     *
     * @return string
     */
    protected function getSmartHelper($attribute, $uploaderMapping)
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

    /**
     * Get image helper form mapper with thumbnail for black&white.
     *
     * @return string
     */
    protected function getImageHelperFormMapperWithThumbnailBW()
    {
        return ($this->getSubject() ? $this->getSubject()->getImageNameBW() ? '<img src="'.$this->lis->getBrowserPath(
                $this->vus->asset($this->getSubject(), 'imageFileBW'),
                '480xY'
            ).'" class="admin-preview img-responsive" alt="thumbnail"/>' : '' : '').'<span style="width:100%;display:block;">amplada mínima 1200px (màx. 10MB amb JPG o PNG)</span>';
    }

    /**
     * @return string
     */
    protected function getImageHelperFormMapperWithThumbnailGif()
    {
        return ($this->getSubject() ? $this->getSubject()->getGifName() ? '<img src="'.$this->lis->getBrowserPath(
                $this->vus->asset($this->getSubject(), 'gifFile'),
                '480xY'
            ).'" class="admin-preview img-responsive" alt="thumbnail"/>' : '' : '').'<span style="width:100%;display:block;">mida 780x1168px (màx. 10MB amb GIF)</span>';
    }

    /**
     * @return string
     */
    protected function getDownloadPdfButton()
    {
        $result = '';
        if ($this->getSubject() && !is_null($this->getSubject()->getAttatchmentPDF())) {
            $result = '<a class="btn btn-warning btn-xs" href="'.$this->vus->asset($this->getSubject(), 'attatchmentPDFFile').'" download="'.$this->getSubject()->getName().'.pdf"><i class="fa fa-file-pdf-o"></i> Document PDF</a>';
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getDownloadDigitalTachographButton()
    {
        $result = '';
        if ($this->getSubject() && !is_null($this->getSubject()->getUploadedFileName())) {
            $url = $this->routeGenerator->generateUrl($this, 'download', ['id' => $this->getSubject()->getId()]);
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
