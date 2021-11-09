<?php

namespace App\Manager;

use App\Repository\Enterprise\ActivityLineRepository;
use App\Repository\Enterprise\CollectionDocumentTypeRepository;
use App\Repository\Enterprise\EnterpriseGroupBountyRepository;
use App\Repository\Enterprise\EnterpriseHolidaysRepository;
use App\Repository\Enterprise\EnterpriseRepository;
use App\Repository\Enterprise\EnterpriseTransferAccountRepository;
use App\Repository\Operator\OperatorAbsenceRepository;
use App\Repository\Operator\OperatorAbsenceTypeRepository;
use App\Repository\Operator\OperatorCheckingRepository;
use App\Repository\Operator\OperatorCheckingTypeRepository;
use App\Repository\Operator\OperatorDigitalTachographRepository;
use App\Repository\Operator\OperatorRepository;
use App\Repository\Operator\OperatorVariousAmountRepository;
use App\Repository\Operator\OperatorWorkRegisterRepository;
use App\Repository\Partner\PartnerBuildingSiteRepository;
use App\Repository\Partner\PartnerClassRepository;
use App\Repository\Partner\PartnerContactRepository;
use App\Repository\Partner\PartnerOrderRepository;
use App\Repository\Partner\PartnerRepository;
use App\Repository\Partner\PartnerTypeRepository;
use App\Repository\Partner\PartnerUnableDaysRepository;
use App\Repository\Payslip\PayslipLineConceptRepository;
use App\Repository\Sale\SaleDeliveryNoteRepository;
use App\Repository\Sale\SaleInvoiceRepository;
use App\Repository\Sale\SaleItemRepository;
use App\Repository\Sale\SaleRequestRepository;
use App\Repository\Sale\SaleServiceTariffRepository;
use App\Repository\Sale\SaleTariffRepository;
use App\Repository\Setting\CityRepository;
use App\Repository\Setting\ProvinceRepository;
use App\Repository\Setting\SaleInvoiceSeriesRepository;
use App\Repository\Setting\TimeRangeRepository;
use App\Repository\Setting\UserRepository;
use App\Repository\Vehicle\VehicleCategoryRepository;
use App\Repository\Vehicle\VehicleCheckingRepository;
use App\Repository\Vehicle\VehicleCheckingTypeRepository;
use App\Repository\Vehicle\VehicleMaintenanceRepository;
use App\Repository\Vehicle\VehicleMaintenanceTaskRepository;
use App\Repository\Vehicle\VehicleRepository;
use App\Repository\Web\ServiceRepository;
use App\Repository\Web\WorkRepository;

/**
 * Class RepositoriesManager.
 *
 * @category Manager
 *
 * @author   David Romaní <david@flux.cat>
 */
class RepositoriesManager
{
    private ServiceRepository $serviceRepository;

    private VehicleCategoryRepository $vehicleCategoryRepository;

    private UserRepository $userRepository;

    private OperatorRepository $operatorRepository;

    private EnterpriseRepository $enterpriseRepository;

    private EnterpriseGroupBountyRepository $enterpriseGroupBountyRepository;

    private EnterpriseTransferAccountRepository $enterpriseTransferAccountRepository;

    private EnterpriseHolidaysRepository $enterpriseHolidaysRepository;

    private OperatorCheckingRepository $operatorCheckingRepository;

    private OperatorCheckingTypeRepository $operatorCheckingTypeRepository;

    private OperatorAbsenceTypeRepository $operatorAbsenceTypeRepository;

    private OperatorAbsenceRepository $operatorAbsenceRepository;

    private OperatorDigitalTachographRepository $operatorDigitalTachographRepository;

    private OperatorVariousAmountRepository $operatorVariousAmountRepository;

    private VehicleRepository $vehicleRepository;

    private VehicleCheckingTypeRepository $vehicleCheckingTypeRepository;

    private VehicleCheckingRepository $vehicleCheckingRepository;

    private VehicleMaintenanceTaskRepository $vehicleMaintenanceTaskRepository;

    private VehicleMaintenanceRepository $vehicleMaintenanceRepository;

    private PartnerRepository $partnerRepository;

    private PartnerClassRepository $partnerClassRepository;

    private PartnerTypeRepository $partnerTypeRepository;

    private PartnerContactRepository $partnerContactRepository;

    private PartnerUnableDaysRepository $partnerUnableDaysRepository;

    private CityRepository $cityRepository;

    private ProvinceRepository $provinceRepository;

    private SaleTariffRepository $saleTariffRepository;

    private PartnerBuildingSiteRepository $partnerBuildingSiteRepository;

    private PartnerOrderRepository $partnerOrderRepository;

    private CollectionDocumentTypeRepository $collectionDocumentTypeRepository;

    private ActivityLineRepository $activityLineRepository;

    private SaleInvoiceSeriesRepository $saleInvoiceSeriesRepository;

    private SaleRequestRepository $saleRequestRepository;

    private SaleDeliveryNoteRepository $saleDeliveryNoteRepository;

    private SaleInvoiceRepository $saleInvoiceRepository;

    private SaleServiceTariffRepository $saleServiceTariffRepository;

    private SaleItemRepository $saleItemRepository;

    private WorkRepository $workRepository;

    private TimeRangeRepository $timeRangeRepository;

    private OperatorWorkRegisterRepository $operatorWorkRegisterRepository;

    private PayslipLineConceptRepository $payslipLineConceptRepository;

    /**
     * Methods.
     */

    /**
     * RepositoriesManager constructor.
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
        OperatorWorkRegisterRepository $operatorWorkRegisterRepository,
        VehicleRepository $vehicleRepository,
        VehicleCheckingTypeRepository $vehicleCheckingTypeRepository,
        VehicleCheckingRepository $vehicleCheckingRepository,
        VehicleMaintenanceTaskRepository $vehicleMaintenanceTaskRepository,
        VehicleMaintenanceRepository $vehicleMaintenanceRepository,
        PartnerRepository $partnerRepository,
        PartnerClassRepository $partnerClassRepository,
        PartnerTypeRepository $partnerTypeRepository,
        PartnerContactRepository $partnerContactRepository,
        PartnerUnableDaysRepository $partnerUnableDaysRepository,
        CityRepository $cityRepository,
        ProvinceRepository $provinceRepository,
        SaleTariffRepository $saleTariffRepository,
        PartnerBuildingSiteRepository $partnerBuildingSiteRepository,
        PartnerOrderRepository $partnerOrderRepository,
        CollectionDocumentTypeRepository $collectionDocumentTypeRepository,
        ActivityLineRepository $activityLineRepository,
        SaleInvoiceSeriesRepository $saleInvoiceSeriesRepository,
        SaleRequestRepository $saleRequestRepository,
        SaleDeliveryNoteRepository $saleDeliveryNoteRepository,
        SaleInvoiceRepository $saleInvoiceRepository,
        SaleServiceTariffRepository $saleServiceTariffRepository,
        SaleItemRepository $saleItemRepository,
        WorkRepository $workRepository,
        TimeRangeRepository $timeRangeRepository,
        PayslipLineConceptRepository $payslipLineConceptRepository
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
        $this->operatorWorkRegisterRepository = $operatorWorkRegisterRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->vehicleCheckingTypeRepository = $vehicleCheckingTypeRepository;
        $this->vehicleCheckingRepository = $vehicleCheckingRepository;
        $this->vehicleMaintenanceTaskRepository = $vehicleMaintenanceTaskRepository;
        $this->vehicleMaintenanceRepository = $vehicleMaintenanceRepository;
        $this->partnerRepository = $partnerRepository;
        $this->partnerClassRepository = $partnerClassRepository;
        $this->partnerTypeRepository = $partnerTypeRepository;
        $this->partnerContactRepository = $partnerContactRepository;
        $this->partnerUnableDaysRepository = $partnerUnableDaysRepository;
        $this->cityRepository = $cityRepository;
        $this->provinceRepository = $provinceRepository;
        $this->saleTariffRepository = $saleTariffRepository;
        $this->partnerBuildingSiteRepository = $partnerBuildingSiteRepository;
        $this->partnerOrderRepository = $partnerOrderRepository;
        $this->collectionDocumentTypeRepository = $collectionDocumentTypeRepository;
        $this->activityLineRepository = $activityLineRepository;
        $this->saleInvoiceSeriesRepository = $saleInvoiceSeriesRepository;
        $this->saleRequestRepository = $saleRequestRepository;
        $this->saleDeliveryNoteRepository = $saleDeliveryNoteRepository;
        $this->saleInvoiceRepository = $saleInvoiceRepository;
        $this->saleServiceTariffRepository = $saleServiceTariffRepository;
        $this->saleItemRepository = $saleItemRepository;
        $this->workRepository = $workRepository;
        $this->timeRangeRepository = $timeRangeRepository;
        $this->payslipLineConceptRepository = $payslipLineConceptRepository;
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
        return $this->operatorAbsenceRepository;
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
     * @return VehicleMaintenanceTaskRepository
     */
    public function getVehicleMaintenanceTaskRepository()
    {
        return $this->vehicleMaintenanceTaskRepository;
    }

    /**
     * @return VehicleMaintenanceRepository
     */
    public function getVehicleMaintenanceRepository()
    {
        return $this->vehicleMaintenanceRepository;
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
     * @return PartnerContactRepository
     */
    public function getPartnerContactRepository()
    {
        return $this->partnerContactRepository;
    }

    /**
     * @return CityRepository
     */
    public function getCityRepository()
    {
        return $this->cityRepository;
    }

    /**
     * @return ProvinceRepository
     */
    public function getProvinceRepository()
    {
        return $this->provinceRepository;
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

    public function getSaleServiceTariffRepository(): SaleServiceTariffRepository
    {
        return $this->saleServiceTariffRepository;
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
     * @return PartnerUnableDaysRepository
     */
    public function getPartnerUnableDaysRepository()
    {
        return $this->partnerUnableDaysRepository;
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
    public function getSaleInvoiceRepository()
    {
        return $this->saleInvoiceRepository;
    }

    public function getSaleItemRepository(): SaleItemRepository
    {
        return $this->saleItemRepository;
    }

    /**
     * @return WorkRepository
     */
    public function getWorkRepository()
    {
        return $this->workRepository;
    }

    public function getTimeRangeRepository(): TimeRangeRepository
    {
        return $this->timeRangeRepository;
    }

    public function getOperatorWorkRegisterRepository(): OperatorWorkRegisterRepository
    {
        return $this->operatorWorkRegisterRepository;
    }

    public function getPayslipLineConceptRepository(): PayslipLineConceptRepository
    {
        return $this->payslipLineConceptRepository;
    }
}
