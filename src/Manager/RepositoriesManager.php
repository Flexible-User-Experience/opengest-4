<?php

namespace App\Manager;

use App\Repository\Enterprise\ActivityLineRepository;
use App\Repository\Operator\OperatorDigitalTachographRepository;
use App\Repository\Operator\OperatorVariousAmountRepository;
use App\Repository\Sale\SaleInvoiceRepository;
use App\Repository\Setting\CityRepository;
use App\Repository\Enterprise\CollectionDocumentTypeRepository;
use App\Repository\Enterprise\EnterpriseGroupBountyRepository;
use App\Repository\Enterprise\EnterpriseRepository;
use App\Repository\Enterprise\EnterpriseTransferAccountRepository;
use App\Repository\Enterprise\EnterpriseHolidaysRepository;
use App\Repository\Operator\OperatorAbsenceRepository;
use App\Repository\Operator\OperatorAbsenceTypeRepository;
use App\Repository\Operator\OperatorCheckingRepository;
use App\Repository\Operator\OperatorCheckingTypeRepository;
use App\Repository\Operator\OperatorRepository;
use App\Repository\Partner\PartnerBuildingSiteRepository;
use App\Repository\Partner\PartnerClassRepository;
use App\Repository\Partner\PartnerOrderRepository;
use App\Repository\Partner\PartnerRepository;
use App\Repository\Partner\PartnerTypeRepository;
use App\Repository\Sale\SaleDeliveryNoteRepository;
use App\Repository\Setting\SaleInvoiceSeriesRepository;
use App\Repository\Sale\SaleRequestRepository;
use App\Repository\Sale\SaleTariffRepository;
use App\Repository\Web\ServiceRepository;
use App\Repository\Setting\UserRepository;
use App\Repository\Vehicle\VehicleCategoryRepository;
use App\Repository\Vehicle\VehicleCheckingRepository;
use App\Repository\Vehicle\VehicleCheckingTypeRepository;
use App\Repository\Vehicle\VehicleRepository;

/**
 * Class RepositoriesManager.
 *
 * @category Manager
 *
 * @author   David Romaní <david@flux.cat>
 */
class RepositoriesManager
{
    /**
     * @var ServiceRepository
     */
    private ServiceRepository $serviceRepository;

    /**
     * @var VehicleCategoryRepository
     */
    private VehicleCategoryRepository $vehicleCategoryRepository;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var OperatorRepository
     */
    private OperatorRepository $operatorRepository;

    /**
     * @var EnterpriseRepository
     */
    private EnterpriseRepository $enterpriseRepository;

    /**
     * @var EnterpriseGroupBountyRepository
     */
    private EnterpriseGroupBountyRepository $enterpriseGroupBountyRepository;

    /**
     * @var EnterpriseTransferAccountRepository
     */
    private EnterpriseTransferAccountRepository $enterpriseTransferAccountRepository;

    /**
     * @var EnterpriseHolidaysRepository
     */
    private EnterpriseHolidaysRepository $enterpriseHolidaysRepository;

    /**
     * @var OperatorCheckingRepository
     */
    private OperatorCheckingRepository $operatorCheckingRepository;

    /**
     * @var OperatorCheckingTypeRepository
     */
    private OperatorCheckingTypeRepository $operatorCheckingTypeRepository;

    /**
     * @var OperatorAbsenceTypeRepository
     */
    private OperatorAbsenceTypeRepository $operatorAbsenceTypeRepository;

    /**
     * @var OperatorAbsenceRepository
     */
    private OperatorAbsenceRepository $operatorAbsenceRepository;

    /**
     * @var OperatorDigitalTachographRepository
     */
    private OperatorDigitalTachographRepository $operatorDigitalTachographRepository;

    /**
     * @var OperatorVariousAmountRepository
     */
    private OperatorVariousAmountRepository $operatorVariousAmountRepository;

    /**
     * @var VehicleRepository
     */
    private VehicleRepository $vehicleRepository;

    /**
     * @var VehicleCheckingTypeRepository
     */
    private VehicleCheckingTypeRepository $vehicleCheckingTypeRepository;

    /**
     * @var VehicleCheckingRepository
     */
    private VehicleCheckingRepository $vehicleCheckingRepository;

    /**
     * @var PartnerRepository
     */
    private PartnerRepository $partnerRepository;

    /**
     * @var PartnerClassRepository
     */
    private PartnerClassRepository $partnerClassRepository;

    /**
     * @var PartnerTypeRepository
     */
    private PartnerTypeRepository $partnerTypeRepository;

    /**
     * @var CityRepository
     */
    private CityRepository $cityRepository;

    /**
     * @var SaleTariffRepository
     */
    private SaleTariffRepository $saleTariffRepository;

    /**
     * @var PartnerBuildingSiteRepository
     */
    private PartnerBuildingSiteRepository $partnerBuildingSiteRepository;

    /**
     * @var PartnerOrderRepository
     */
    private PartnerOrderRepository $partnerOrderRepository;

    /**
     * @var CollectionDocumentTypeRepository
     */
    private CollectionDocumentTypeRepository $collectionDocumentTypeRepository;

    /**
     * @var ActivityLineRepository
     */
    private ActivityLineRepository $activityLineRepository;

    /**
     * @var SaleInvoiceSeriesRepository
     */
    private SaleInvoiceSeriesRepository $saleInvoiceSeriesRepository;

    /**
     * @var SaleRequestRepository
     */
    private SaleRequestRepository $saleRequestRepository;

    /**
     * @var SaleDeliveryNoteRepository
     */
    private SaleDeliveryNoteRepository $saleDeliveryNoteRepository;

    /**
     * @var SaleInvoiceRepository
     */
    private SaleInvoiceRepository $saleInvoiceRepository;

    /**
     * Methods.
     */

    /**
     * RepositoriesManager constructor.
     *
     * @param ServiceRepository                   $serviceRepository
     * @param VehicleCategoryRepository           $vehicleCategoryRepository
     * @param UserRepository                      $userRepository
     * @param OperatorRepository                  $operatorRepository
     * @param EnterpriseRepository                $enterpriseRepository
     * @param EnterpriseGroupBountyRepository     $enterpriseGroupBountyRepository
     * @param EnterpriseTransferAccountRepository $enterpriseTransferAccountRepository
     * @param EnterpriseHolidaysRepository        $enterpriseHolidaysRepository
     * @param OperatorCheckingRepository          $operatorCheckingRepository
     * @param OperatorCheckingTypeRepository      $operatorCheckingTypeRepository
     * @param OperatorAbsenceTypeRepository       $operatorAbsenceTypeRepository
     * @param OperatorAbsenceRepository           $operatorAbsenceRepository
     * @param OperatorDigitalTachographRepository $operatorDigitalTachographRepository
     * @param OperatorVariousAmountRepository     $operatorVariousAmountRepository
     * @param VehicleRepository                   $vehicleRepository
     * @param VehicleCheckingTypeRepository       $vehicleCheckingTypeRepository
     * @param VehicleCheckingRepository           $vehicleCheckingRepository
     * @param PartnerRepository                   $partnerRepository
     * @param PartnerClassRepository              $partnerClassRepository
     * @param PartnerTypeRepository               $partnerTypeRepository
     * @param CityRepository                      $cityRepository
     * @param SaleTariffRepository                $saleTariffRepository
     * @param PartnerBuildingSiteRepository       $partnerBuildingSiteRepository
     * @param PartnerOrderRepository              $partnerOrderRepository
     * @param CollectionDocumentTypeRepository    $collectionDocumentTypeRepository
     * @param ActivityLineRepository              $activityLineRepository
     * @param SaleInvoiceSeriesRepository         $saleInvoiceSeriesRepository
     * @param SaleRequestRepository               $saleRequestRepository
     * @param SaleDeliveryNoteRepository          $saleDeliveryNoteRepository
     * @param SaleInvoiceRepository               $saleInvoiceRepository
     */
    public function __construct(
        ServiceRepository $serviceRepository,
        VehicleCategoryRepository $vehicleCategoryRepository,
        UserRepository $userRepository,
        OperatorRepository $operatorRepository,
        EnterpriseRepository $enterpriseRepository,
        EnterpriseGroupBountyRepository $enterpriseGroupBountyRepository,
        EnterpriseTransferAccountRepository $enterpriseTransferAccountRepository,
        EnterpriseHolidaysRepository $enterpriseHolidaysRepository,
        OperatorCheckingRepository $operatorCheckingRepository,
        OperatorCheckingTypeRepository $operatorCheckingTypeRepository,
        OperatorAbsenceTypeRepository $operatorAbsenceTypeRepository,
        OperatorAbsenceRepository $operatorAbsenceRepository,
        OperatorDigitalTachographRepository $operatorDigitalTachographRepository,
        OperatorVariousAmountRepository $operatorVariousAmountRepository,
        VehicleRepository $vehicleRepository,
        VehicleCheckingTypeRepository $vehicleCheckingTypeRepository,
        VehicleCheckingRepository $vehicleCheckingRepository,
        PartnerRepository $partnerRepository,
        PartnerClassRepository $partnerClassRepository,
        PartnerTypeRepository $partnerTypeRepository,
        CityRepository $cityRepository,
        SaleTariffRepository $saleTariffRepository,
        PartnerBuildingSiteRepository $partnerBuildingSiteRepository,
        PartnerOrderRepository $partnerOrderRepository,
        CollectionDocumentTypeRepository $collectionDocumentTypeRepository,
        ActivityLineRepository $activityLineRepository,
        SaleInvoiceSeriesRepository $saleInvoiceSeriesRepository,
        SaleRequestRepository $saleRequestRepository,
        SaleDeliveryNoteRepository $saleDeliveryNoteRepository,
        SaleInvoiceRepository $saleInvoiceRepository
    ) {
        $this->serviceRepository = $serviceRepository;
        $this->vehicleCategoryRepository = $vehicleCategoryRepository;
        $this->userRepository = $userRepository;
        $this->operatorRepository = $operatorRepository;
        $this->enterpriseRepository = $enterpriseRepository;
        $this->enterpriseGroupBountyRepository = $enterpriseGroupBountyRepository;
        $this->enterpriseTransferAccountRepository = $enterpriseTransferAccountRepository;
        $this->enterpriseHolidaysRepository = $enterpriseHolidaysRepository;
        $this->operatorCheckingRepository = $operatorCheckingRepository;
        $this->operatorCheckingTypeRepository = $operatorCheckingTypeRepository;
        $this->operatorAbsenceTypeRepository = $operatorAbsenceTypeRepository;
        $this->operatorAbsenceRepository = $operatorAbsenceRepository;
        $this->operatorDigitalTachographRepository = $operatorDigitalTachographRepository;
        $this->operatorVariousAmountRepository = $operatorVariousAmountRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->vehicleCheckingTypeRepository = $vehicleCheckingTypeRepository;
        $this->vehicleCheckingRepository = $vehicleCheckingRepository;
        $this->partnerRepository = $partnerRepository;
        $this->partnerClassRepository = $partnerClassRepository;
        $this->partnerTypeRepository = $partnerTypeRepository;
        $this->cityRepository = $cityRepository;
        $this->saleTariffRepository = $saleTariffRepository;
        $this->partnerBuildingSiteRepository = $partnerBuildingSiteRepository;
        $this->partnerOrderRepository = $partnerOrderRepository;
        $this->collectionDocumentTypeRepository = $collectionDocumentTypeRepository;
        $this->activityLineRepository = $activityLineRepository;
        $this->saleInvoiceSeriesRepository = $saleInvoiceSeriesRepository;
        $this->saleRequestRepository = $saleRequestRepository;
        $this->saleDeliveryNoteRepository = $saleDeliveryNoteRepository;
        $this->saleInvoiceRepository = $saleInvoiceRepository;
    }

    /**
     * @return ServiceRepository
     */
    public function getServiceRepository()
    {
        return $this->serviceRepository;
    }

    /**
     * @return VehicleCategoryRepository
     */
    public function getVehicleCategoryRepository()
    {
        return $this->vehicleCategoryRepository;
    }

    /**
     * @return UserRepository
     */
    public function getUserRepository()
    {
        return $this->userRepository;
    }

    /**
     * @return OperatorRepository
     */
    public function getOperatorRepository()
    {
        return $this->operatorRepository;
    }

    /**
     * @return EnterpriseRepository
     */
    public function getEnterpriseRepository()
    {
        return $this->enterpriseRepository;
    }

    /**
     * @return EnterpriseGroupBountyRepository
     */
    public function getEnterpriseGroupBountyRepository()
    {
        return $this->enterpriseGroupBountyRepository;
    }

    /**
     * @return EnterpriseTransferAccountRepository
     */
    public function getEnterpriseTransferAccountRepository()
    {
        return $this->enterpriseTransferAccountRepository;
    }

    /**
     * @return EnterpriseHolidaysRepository
     */
    public function getEnterpriseHolidaysRepository()
    {
        return $this->enterpriseHolidaysRepository;
    }

    /**
     * @return OperatorCheckingRepository
     */
    public function getOperatorCheckingRepository()
    {
        return $this->operatorCheckingRepository;
    }

    /**
     * @return OperatorCheckingTypeRepository
     */
    public function getOperatorCheckingTypeRepository()
    {
        return $this->operatorCheckingTypeRepository;
    }

    /**
     * @return OperatorAbsenceRepository
     */
    public function getOperatorAbsenceRepository()
    {
        return $this->getOperatorAbsenceRepository();
    }

    /**
     * @return OperatorAbsenceTypeRepository
     */
    public function getOperatorAbsenceTypeRepository()
    {
        return $this->operatorAbsenceTypeRepository;
    }

    /**
     * @return OperatorDigitalTachographRepository
     */
    public function getOperatorDigitalTachographRepository()
    {
        return $this->operatorDigitalTachographRepository;
    }

    /**
     * @return OperatorVariousAmountRepository
     */
    public function getOperatorVariousAmountRepository()
    {
        return $this->operatorVariousAmountRepository;
    }

    /**
     * @return VehicleRepository
     */
    public function getVehicleRepository()
    {
        return $this->vehicleRepository;
    }

    /**
     * @return VehicleCheckingTypeRepository
     */
    public function getVehicleCheckingTypeRepository()
    {
        return $this->vehicleCheckingTypeRepository;
    }

    /**
     * @return VehicleCheckingRepository
     */
    public function getVehicleCheckingRepository()
    {
        return $this->vehicleCheckingRepository;
    }

    /**
     * @return PartnerRepository
     */
    public function getPartnerRepository()
    {
        return $this->partnerRepository;
    }

    /**
     * @return PartnerClassRepository
     */
    public function getPartnerClassRepository()
    {
        return $this->partnerClassRepository;
    }

    /**
     * @return CityRepository
     */
    public function getCityRepository()
    {
        return $this->cityRepository;
    }

    /**
     * @return PartnerTypeRepository
     */
    public function getPartnerTypeRepository()
    {
        return $this->partnerTypeRepository;
    }

    /**
     * @return SaleTariffRepository
     */
    public function getSaleTariffRepository()
    {
        return $this->saleTariffRepository;
    }

    /**
     * @return PartnerBuildingSiteRepository
     */
    public function getPartnerBuildingSiteRepository()
    {
        return $this->partnerBuildingSiteRepository;
    }

    /**
     * @return PartnerOrderRepository
     */
    public function getPartnerOrderRepository()
    {
        return $this->partnerOrderRepository;
    }

    /**
     * @return CollectionDocumentTypeRepository
     */
    public function getCollectionDocumentTypeRepository()
    {
        return $this->collectionDocumentTypeRepository;
    }

    /**
     * @return ActivityLineRepository
     */
    public function getActivityLineRepository()
    {
        return $this->activityLineRepository;
    }

    /**
     * @return SaleInvoiceSeriesRepository
     */
    public function getSaleInvoiceSeriesRepository()
    {
        return $this->saleInvoiceSeriesRepository;
    }

    /**
     * @return SaleRequestRepository
     */
    public function getSaleRequestRepository()
    {
        return $this->saleRequestRepository;
    }

    /**
     * @return SaleDeliveryNoteRepository
     */
    public function getSaleDeliveryNoteRepository()
    {
        return $this->saleDeliveryNoteRepository;
    }

    /**
     * @return SaleInvoiceRepository
     */
    public function getSaleInvoiceRepository(): SaleInvoiceRepository
    {
        return $this->saleInvoiceRepository;
    }
}
