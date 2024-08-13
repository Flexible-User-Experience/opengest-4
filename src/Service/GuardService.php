<?php

namespace App\Service;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorChecking;
use App\Entity\Partner\Partner;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleChecking;
use App\Enum\UserRolesEnum;
use App\Security\AbstractVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class GuardService.
 *
 * @category Service
 */
class GuardService
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private AuthorizationCheckerInterface $acs;

    /**
     * Methods.
     */

    /**
     * @param AuthorizationCheckerInterface $acs
     */
    public function __construct(AuthorizationCheckerInterface $acs)
    {
        $this->acs = $acs;
    }

    /**
     * @param Operator $operator
     *
     * @return bool
     */
    public function isOwnOperator(Operator $operator): bool
    {
        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            return true;
        }

        return $this->acs->isGranted(AbstractVoter::ATTRIBUTES, $operator);
    }

    /**
     * @param OperatorChecking $oc
     *
     * @return bool
     */
    public function isOwnOperatorCheking(OperatorChecking $oc): bool
    {
        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            return true;
        }

        return $this->acs->isGranted(AbstractVoter::ATTRIBUTES, $oc);
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return bool
     */
    public function isOwnEnterprise(Enterprise $enterprise): bool
    {
        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            return true;
        }

        return $this->acs->isGranted(AbstractVoter::ATTRIBUTES, $enterprise);
    }

    /**
     * @param Vehicle $vehicle
     *
     * @return bool
     */
    public function isOwnVehicle(Vehicle $vehicle): bool
    {
        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            return true;
        }

        return $this->acs->isGranted(AbstractVoter::ATTRIBUTES, $vehicle);
    }

    /**
     * @param VehicleChecking $vc
     *
     * @return bool
     */
    public function isOwnVehicleChecking(VehicleChecking $vc): bool
    {
        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            return true;
        }

        return $this->acs->isGranted(AbstractVoter::ATTRIBUTES, $vc);
    }

    /**
     * @param Partner $partner
     *
     * @return bool
     */
    public function isOwnPartner(Partner $partner): bool
    {
        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            return true;
        }

        return $this->acs->isGranted(AbstractVoter::ATTRIBUTES, $partner);
    }
}
