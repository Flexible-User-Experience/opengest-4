<?php

namespace App\Controller\Admin\Operator;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorWorkRegisterHeader;
use App\Form\Type\GenerateTimeSummaryFormType;
use App\Repository\Operator\OperatorWorkRegisterHeaderRepository;
use DateTime;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class OperatorWorkRegisterHeaderAdminController.
 */
class OperatorWorkRegisterHeaderAdminController extends BaseAdminController
{
    /**
     * @return Response|RedirectResponse
     */
    public function batchActionGenerateWorkRegisterReportPdf(ProxyQueryInterface $selectedModelQuery): Response
    {
        $this->admin->checkAccess('edit');
        $operatorWorkRegisterHeaders = $selectedModelQuery->execute()->getQuery()->getResult();
        $owrhForDates = $operatorWorkRegisterHeaders;

        //TODO get from, to from the filter selection
        $from = array_shift($owrhForDates)->getDateFormatted();

        if (!$owrhForDates) {
            $to = $from;
        } else {
            $to = array_pop($owrhForDates)->getDateFormatted();
        }

        if (!$operatorWorkRegisterHeaders) {
            $this->addFlash('warning', 'No existen registros para esta selección');
        }

        return new Response($this->wrhpm->outputCollection($operatorWorkRegisterHeaders, $from, $to), 200, ['Content-type' => 'application/pdf']);
    }

    public function getJsonOperatorWorkRegisterTotalsByHourTypeAction(Request $request): JsonResponse
    {
        $operatorId = $request->get('operatorId');
        $date = DateTime::createFromFormat('d-m-Y', $request->get('date'));
        /** @var Operator $operator */
        $operator = $this->admin->getModelManager()->find(Operator::class, $operatorId);
        if (!$operator) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $operatorId));
        }
        /** @var OperatorWorkRegisterHeader $operatorWorkRegisterHeader */
        $operatorWorkRegisterHeader = $this->admin->getModelManager()->findOneBy(OperatorWorkRegisterHeader::class, [
            'operator' => $operator,
            'date' => $date,
        ]);
        if (!$operatorWorkRegisterHeader) {
            $result = [];
        } else {
            /** @var OperatorWorkRegisterHeaderRepository $operatorWorkRegisterHeaderRepository */
            $operatorWorkRegisterHeaderRepository = $this->container->get('doctrine')->getRepository(OperatorWorkRegisterHeader::class);
            $resultFromRepository = $operatorWorkRegisterHeaderRepository->getHoursFromOperatorWorkRegistersWithHoursFromDeliveryNotesAndDateQB($operatorWorkRegisterHeader);
            $result = [];
            $result['workingHour'] = 0;
            $result['normalHour'] = 0;
            $result['extraHour'] = 0;
            foreach ($resultFromRepository as $singleResult) {
                if (str_contains($singleResult['description'], 'Hora laboral')) {
                    $result['workingHour'] += $singleResult['hours'];
                }
                if (str_contains($singleResult['description'], 'Hora normal')) {
                    $result['normalHour'] += $singleResult['hours'];
                }
                if (str_contains($singleResult['description'], 'Hora extra')) {
                    $result['extraHour'] += $singleResult['hours'];
                }
            }
        }

        return new JsonResponse($result);
    }

    public function batchActionGenerateTimeSummary(ProxyQueryInterface $selectedModelQuery, Request $request)
    {
        $this->admin->checkAccess('edit');
        $form = $this->createForm(GenerateTimeSummaryFormType::class);
        $form->handleRequest($request);
        /** @var Operator[] $operators */
        $operatorWorkRegisterHeaders = $selectedModelQuery->execute()->getQuery()->getResult();
        $form->get('operatorWorkRegisterHeaders')->setData($operatorWorkRegisterHeaders);

        return $this->renderWithExtraParams(
            'admin/operator-work-register-header/timeSummaryGeneration.html.twig',
            [
                'generateTimeSummaryForm' => $form->createView(),
//                'operators' => $operators
            ]
        );
    }

    public function createTimeSummaryAction(Request $request)
    {
        $formData = $request->request->get('app_generate_time_summary');
        try {
            $em = $this->getDoctrine()->getManager();

            /** @var Operator $operators */
            $operatorWorkRegisterHeaders = $formData['operatorWorkRegisterHeaders'];
            $fromDate = $formData['fromDate'];
            $toDate = $formData['toDate'];
            $percentage = $formData['percentage'];
            $newOperatorWorkRegisterHeaders = [];
            /* @var Operator $operator */
            foreach ($operatorWorkRegisterHeaders as $operatorWorkRegisterHeader) {
                $newOperatorWorkRegisterHeaders[] = $em->getRepository(OperatorWorkRegisterHeader::class)->find($operatorWorkRegisterHeader);
            }

            return new Response($this->wrhpm->outputSingleTimeSum($newOperatorWorkRegisterHeaders, $fromDate, $toDate, $percentage), 200, ['Content-type' => 'application/pdf']);
        } catch (\Exception $exception) {
            $this->addFlash(
                'warning',
                'No se han podido generar las la plantilla de horas.'.$exception->getMessage().$exception->getTraceAsString()
            );

            return new RedirectResponse($this->generateUrl('admin_app_operator_operatorworkregisterheader_list'));
        }
    }
}
