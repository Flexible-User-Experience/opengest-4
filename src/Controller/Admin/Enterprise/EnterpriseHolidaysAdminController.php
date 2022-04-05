<?php

namespace App\Controller\Admin\Enterprise;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Enterprise\EnterpriseHolidays;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EnterpriseHolidaysAdminController.
 */
class EnterpriseHolidaysAdminController extends BaseAdminController
{
    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id = null): Response
    {
        $id = $request->get($this->admin->getIdParameter());

        /** @var EnterpriseHolidays $enterpriseHoliday */
        $enterpriseHoliday = $this->admin->getObject($id);
        if (!$enterpriseHoliday) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return parent::editAction($request);
    }

    public function checkIfDayIsEnterpriseHolidayAction(Request $request)
    {
        $date = DateTime::createFromFormat('d-m-Y H:i:s', $request->get('date').' 00:00:00');
        $holiday = false;
        if ($date->format('N') >= 6) {
            $holiday = true;
        } elseif ($this->em->getRepository(EnterpriseHolidays::class)->findOneBy(['day' => $date])) {
            $holiday = true;
        }

        return new JsonResponse($holiday);
    }
}
