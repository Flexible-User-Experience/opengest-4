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
use App\Repository\Operator\OperatorWorkRegisterHeaderRepository;
use App\Repository\Operator\OperatorWorkRegisterRepository;
use App\Repository\Partner\PartnerBuildingSiteRepository;
use App\Repository\Partner\PartnerClassRepository;
use App\Repository\Partner\PartnerContactRepository;
use App\Repository\Partner\PartnerDeliveryAddressRepository;
use App\Repository\Partner\PartnerOrderRepository;
use App\Repository\Partner\PartnerProjectRepository;
use App\Repository\Partner\PartnerRepository;
use App\Repository\Partner\PartnerTypeRepository;
use App\Repository\Partner\PartnerUnableDaysRepository;
use App\Repository\Payslip\PayslipLineConceptRepository;
use App\Repository\Payslip\PayslipRepository;
use App\Repository\Purchase\PurchaseInvoiceDueDateRepository;
use App\Repository\Purchase\PurchaseInvoiceLineRepository;
use App\Repository\Purchase\PurchaseInvoiceRepository;
use App\Repository\Purchase\PurchaseItemRepository;
use App\Repository\Sale\SaleDeliveryNoteRepository;
use App\Repository\Sale\SaleInvoiceRepository;
use App\Repository\Sale\SaleItemRepository;
use App\Repository\Sale\SaleRequestRepository;
use App\Repository\Sale\SaleServiceTariffRepository;
use App\Repository\Sale\SaleTariffRepository;
use App\Repository\Setting\CityRepository;
use App\Repository\Setting\CostCenterRepository;
use App\Repository\Setting\ProvinceRepository;
use App\Repository\Setting\SaleInvoiceSeriesRepository;
use App\Repository\Setting\TimeRangeRepository;
use App\Repository\Setting\UserRepository;
use App\Repository\Vehicle\VehicleCategoryRepository;
use App\Repository\Vehicle\VehicleCheckingRepository;
use App\Repository\Vehicle\VehicleCheckingTypeRepository;
use App\Repository\Vehicle\VehicleConsumptionRepository;
use App\Repository\Vehicle\VehicleDigitalTachographRepository;
use App\Repository\Vehicle\VehicleFuelRepository;
use App\Repository\Vehicle\VehicleMaintenanceRepository;
use App\Repository\Vehicle\VehicleMaintenanceTaskRepository;
use App\Repository\Vehicle\VehicleRepository;
use App\Repository\Vehicle\VehicleSpecialPermitRepository;
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

    private VehicleConsumptionRepository $vehicleConsumptionRepository;

    private VehicleMaintenanceTaskRepository $vehicleMaintenanceTaskRepository;

    private VehicleMaintenanceRepository $vehicleMaintenanceRepository;

    private VehicleFuelRepository $vehicleFuelRepository;

    private VehicleSpecialPermitRepository $vehicleSpecialPermitRepository;

    private VehicleDigitalTachographRepository $vehicleDigitalTachographRepository;

    private PartnerRepository $partnerRepository;

    private PartnerClassRepository $partnerClassRepository;

    private PartnerTypeRepository $partnerTypeRepository;

    private PartnerContactRepository $partnerContactRepository;

    private PartnerDeliveryAddressRepository $partnerDeliveryAddress;

    private PartnerUnableDaysRepository $partnerUnableDaysRepository;

    private CityRepository $cityRepository;

    private ProvinceRepository $provinceRepository;

    private SaleTariffRepository $saleTariffRepository;

    private PartnerBuildingSiteRepository $partnerBuildingSiteRepository;

    private PartnerOrderRepository $partnerOrderRepository;

    private PartnerProjectRepository $partnerProjectRepository;

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

    private OperatorWorkRegisterHeaderRepository $operatorWorkRegisterHeaderRepository;

    private PayslipRepository $payslipRepository;

    private PayslipLineConceptRepository $payslipLineConceptRepository;

    private PurchaseInvoiceRepository $purchaseInvoiceRepository;

    private PurchaseInvoiceLineRepository $purchaseInvoiceLineRepository;

    private PurchaseInvoiceDueDateRepository $purchaseInvoiceDueDateRepository;

    private PurchaseItemRepository $purchaseItemRepository;

    private CostCenterRepository $costCenterRepository;

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
        OperatorWorkRegisterHeaderRepository $operatorWorkRegisterHeaderRepository,
        VehicleRepository $vehicleRepository,
        VehicleCheckingTypeRepository $vehicleCheckingTypeRepository,
        VehicleCheckingRepository $vehicleCheckingRepository,
        VehicleConsumptionRepository $vehicleConsumptionRepository,
        VehicleMaintenanceTaskRepository $vehicleMaintenanceTaskRepository,
        VehicleMaintenanceRepository $vehicleMaintenanceRepository,
        VehicleFuelRepository $vehicleFuelRepository,
        VehicleSpecialPermitRepository $vehicleSpecialPermitRepository,
        VehicleDigitalTachographRepository $vehicleDigitalTachographRepository,
        PartnerRepository $partnerRepository,
        PartnerClassRepository $partnerClassRepository,
        PartnerTypeRepository $partnerTypeRepository,
        PartnerContactRepository $partnerContactRepository,
        PartnerDeliveryAddressRepository $partnerDeliveryAddress,
        PartnerUnableDaysRepository $partnerUnableDaysRepository,
        CityRepository $cityRepository,
        ProvinceRepository $provinceRepository,
        SaleTariffRepository $saleTariffRepository,
        PartnerBuildingSiteRepository $partnerBuildingSiteRepository,
        PartnerOrderRepository $partnerOrderRepository,
        PartnerProjectRepository $partnerProjectRepository,
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
        PayslipRepository $payslipRepository,
        PayslipLineConceptRepository $payslipLineConceptRepository,
        PurchaseInvoiceRepository $purchaseInvoiceRepository,
        PurchaseInvoiceLineRepository $purchaseInvoiceLineRepository,
        PurchaseInvoiceDueDateRepository $purchaseInvoiceDueDateRepository,
        PurchaseItemRepository $purchaseItemRepository,
        CostCenterRepository $costCenterRepository
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
        $this->operatorWorkRegisterHeaderRepository = $operatorWorkRegisterHeaderRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->vehicleCheckingTypeRepository = $vehicleCheckingTypeRepository;
        $this->vehicleCheckingRepository = $vehicleCheckingRepository;
        $this->vehicleConsumptionRepository = $vehicleConsumptionRepository;
        $this->vehicleMaintenanceTaskRepository = $vehicleMaintenanceTaskRepository;
        $this->vehicleMaintenanceRepository = $vehicleMaintenanceRepository;
        $this->vehicleFuelRepository = $vehicleFuelRepository;
        $this->vehicleSpecialPermitRepository = $vehicleSpecialPermitRepository;
        $this->vehicleDigitalTachographRepository = $vehicleDigitalTachographRepository;
        $this->partnerRepository = $partnerRepository;
        $this->partnerClassRepository = $partnerClassRepository;
        $this->partnerTypeRepository = $partnerTypeRepository;
        $this->partnerContactRepository = $partnerContactRepository;
        $this->partnerDeliveryAddress = $partnerDeliveryAddress;
        $this->partnerUnableDaysRepository = $partnerUnableDaysRepository;
        $this->cityRepository = $cityRepository;
        $this->provinceRepository = $provinceRepository;
        $this->saleTariffRepository = $saleTariffRepository;
        $this->partnerBuildingSiteRepository = $partnerBuildingSiteRepository;
        $this->partnerOrderRepository = $partnerOrderRepository;
        $this->partnerProjectRepository = $partnerProjectRepository;
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
        $this->payslipRepository = $payslipRepository;
        $this->payslipLineConceptRepository = $payslipLineConceptRepository;
        $this->purchaseInvoiceRepository = $purchaseInvoiceRepository;
        $this->purchaseInvoiceLineRepository = $purchaseInvoiceLineRepository;
        $this->purchaseInvoiceDueDateRepository = $purchaseInvoiceDueDateRepository;
        $this->purchaseItemRepository = $purchaseItemRepository;
        $this->costCenterRepository = $costCenterRepository;
    }

    /**
     * @return ServiceRepository
     */
    public function getServiceRepository(): ServiceRepository
    {
        return $this->serviceRepository;
    }

    /**
     * @return VehicleCategoryRepository
     */
    public function getVehicleCategoryRepository(): VehicleCategoryRepository
    {
        return $this->vehicleCategoryRepository;
    }

    /**
     * @return UserRepository
     */
    public function getUserRepository(): UserRepository
    {
        return $this->userRepository;
    }

    /**
     * @return OperatorRepository
     */
    public function getOperatorRepository(): OperatorRepository
    {
        return $this->operatorRepository;
    }

    /**
     * @return EnterpriseRepository
     */
    public function getEnterpriseRepository(): EnterpriseRepository
    {
        return $this->enterpriseRepository;
    }

    /**
     * @return EnterpriseGroupBountyRepository
     */
    public function getEnterpriseGroupBountyRepository(): EnterpriseGroupBountyRepository
    {
        return $this->enterpriseGroupBountyRepository;
    }

    /**
     * @return EnterpriseTransferAccountRepository
     */
    public function getEnterpriseTransferAccountRepository(): EnterpriseTransferAccountRepository
    {
        return $this->enterpriseTransferAccountRepository;
    }

    /**
     * @return EnterpriseHolidaysRepository
     */
    public function getEnterpriseHolidaysRepository(): EnterpriseHolidaysRepository
    {
        return $this->enterpriseHolidaysRepository;
    }

    /**
     * @return OperatorCheckingRepository
     */
    public function getOperatorCheckingRepository(): OperatorCheckingRepository
    {
        return $this->operatorCheckingRepository;
    }

    /**
     * @return OperatorCheckingTypeRepository
     */
    public function getOperatorCheckingTypeRepository(): OperatorCheckingTypeRepository
    {
        return $this->operatorCheckingTypeRepository;
    }

    /**
     * @return OperatorAbsenceRepository
     */
    public function getOperatorAbsenceRepository(): OperatorAbsenceRepository
    {
        return $this->operatorAbsenceRepository;
    }

    /**
     * @return OperatorAbsenceTypeRepository
     */
    public function getOperatorAbsenceTypeRepository(): OperatorAbsenceTypeRepository
    {
        return $this->operatorAbsenceTypeRepository;
    }

    /**
     * @return OperatorDigitalTachographRepository
     */
    public function getOperatorDigitalTachographRepository(): OperatorDigitalTachographRepository
    {
        return $this->operatorDigitalTachographRepository;
    }

    /**
     * @return OperatorVariousAmountRepository
     */
    public function getOperatorVariousAmountRepository(): OperatorVariousAmountRepository
    {
        return $this->operatorVariousAmountRepository;
    }

    /**
     * @return VehicleRepository
     */
    public function getVehicleRepository(): VehicleRepository
    {
        return $this->vehicleRepository;
    }

    /**
     * @return VehicleCheckingTypeRepository
     */
    public function getVehicleCheckingTypeRepository(): VehicleCheckingTypeRepository
    {
        return $this->vehicleCheckingTypeRepository;
    }

    /**
     * @return VehicleCheckingRepository
     */
    public function getVehicleCheckingRepository(): VehicleCheckingRepository
    {
        return $this->vehicleCheckingRepository;
    }

    public function getVehicleConsumptionRepository(): VehicleConsumptionRepository
    {
        return $this->vehicleConsumptionRepository;
    }

    /**
     * @return VehicleMaintenanceTaskRepository
     */
    public function getVehicleMaintenanceTaskRepository(): VehicleMaintenanceTaskRepository
    {
        return $this->vehicleMaintenanceTaskRepository;
    }

    /**
     * @return VehicleMaintenanceRepository
     */
    public function getVehicleMaintenanceRepository(): VehicleMaintenanceRepository
    {
        return $this->vehicleMaintenanceRepository;
    }

    /**
     * @return VehicleFuelRepository
     */
    public function getVehicleFuelRepository(): VehicleFuelRepository
    {
        return $this->vehicleFuelRepository;
    }

    public function getVehicleSpecialPermitRepository(): VehicleSpecialPermitRepository
    {
        return $this->vehicleSpecialPermitRepository;
    }

    public function getVehicleDigitalTachographRepository(): VehicleDigitalTachographRepository
    {
        return $this->vehicleDigitalTachographRepository;
    }

    /**
     * @return PartnerRepository
     */
    public function getPartnerRepository(): PartnerRepository
    {
        return $this->partnerRepository;
    }

    /**
     * @return PartnerClassRepository
     */
    public function getPartnerClassRepository(): PartnerClassRepository
    {
        return $this->partnerClassRepository;
    }

    /**
     * @return PartnerContactRepository
     */
    public function getPartnerContactRepository(): PartnerContactRepository
    {
        return $this->partnerContactRepository;
    }

    /**
     * @return PartnerDeliveryAddressRepository
     */
    public function getPartnerDeliveryAddressRepository(): PartnerDeliveryAddressRepository
    {
        return $this->partnerDeliveryAddress;
    }

    /**
     * @return CityRepository
     */
    public function getCityRepository(): CityRepository
    {
        return $this->cityRepository;
    }

    /**
     * @return ProvinceRepository
     */
    public function getProvinceRepository(): ProvinceRepository
    {
        return $this->provinceRepository;
    }

    /**
     * @return PartnerTypeRepository
     */
    public function getPartnerTypeRepository(): PartnerTypeRepository
    {
        return $this->partnerTypeRepository;
    }

    /**
     * @return SaleTariffRepository
     */
    public function getSaleTariffRepository(): SaleTariffRepository
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
    public function getPartnerBuildingSiteRepository(): PartnerBuildingSiteRepository
    {
        return $this->partnerBuildingSiteRepository;
    }

    /**
     * @return PartnerOrderRepository
     */
    public function getPartnerOrderRepository(): PartnerOrderRepository
    {
        return $this->partnerOrderRepository;
    }

    /**
     * @return PartnerProjectRepository
     */
    public function getPartnerProjectRepository(): PartnerProjectRepository
    {
        return $this->partnerProjectRepository;
    }

    /**
     * @return PartnerUnableDaysRepository
     */
    public function getPartnerUnableDaysRepository(): PartnerUnableDaysRepository
    {
        return $this->partnerUnableDaysRepository;
    }

    /**
     * @return CollectionDocumentTypeRepository
     */
    public function getCollectionDocumentTypeRepository(): CollectionDocumentTypeRepository
    {
        return $this->collectionDocumentTypeRepository;
    }

    /**
     * @return ActivityLineRepository
     */
    public function getActivityLineRepository(): ActivityLineRepository
    {
        return $this->activityLineRepository;
    }

    /**
     * @return SaleInvoiceSeriesRepository
     */
    public function getSaleInvoiceSeriesRepository(): SaleInvoiceSeriesRepository
    {
        return $this->saleInvoiceSeriesRepository;
    }

    /**
     * @return SaleRequestRepository
     */
    public function getSaleRequestRepository(): SaleRequestRepository
    {
        return $this->saleRequestRepository;
    }

    /**
     * @return SaleDeliveryNoteRepository
     */
    public function getSaleDeliveryNoteRepository(): SaleDeliveryNoteRepository
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

    public function getSaleItemRepository(): SaleItemRepository
    {
        return $this->saleItemRepository;
    }

    /**
     * @return WorkRepository
     */
    public function getWorkRepository(): WorkRepository
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

    public function getOperatorWorkRegisterHeaderRepository(): OperatorWorkRegisterHeaderRepository
    {
        return $this->operatorWorkRegisterHeaderRepository;
    }

    public function getPayslipLineConceptRepository(): PayslipLineConceptRepository
    {
        return $this->payslipLineConceptRepository;
    }

    public function getPayslipRepository(): PayslipRepository
    {
        return $this->payslipRepository;
    }

    public function getPurchaseInvoiceRepository(): PurchaseInvoiceRepository
    {
        return $this->purchaseInvoiceRepository;
    }

    public function getPurchaseInvoiceLineRepository(): PurchaseInvoiceLineRepository
    {
        return $this->purchaseInvoiceLineRepository;
    }

    public function getPurchaseInvoiceDueDateRepository(): PurchaseInvoiceDueDateRepository
    {
        return $this->purchaseInvoiceDueDateRepository;
    }

    public function getPurchaseItemRepository(): PurchaseItemRepository
    {
        return $this->purchaseItemRepository;
    }

    public function getCostCenterRepository(): CostCenterRepository
    {
        return $this->costCenterRepository;
    }
}
