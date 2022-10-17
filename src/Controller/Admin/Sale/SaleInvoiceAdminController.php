<?php

namespace App\Controller\Admin\Sale;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleInvoice;
use App\Entity\Sale\SaleInvoiceDueDate;
use App\Entity\Setting\SaleInvoiceSeries;
use App\Repository\Setting\SaleInvoiceSeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SaleInvoiceAdminController.
 */
class SaleInvoiceAdminController extends BaseAdminController
{
    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id = null): Response
    {
        $id = $request->get($this->admin->getIdParameter());

        /** @var SaleInvoice $saleInvoice */
        $saleInvoice = $this->admin->getObject($id);
        if (!$saleInvoice) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return parent::editAction($request);
    }

    public function batchActionInvoiceList(ProxyQueryInterface $selectedModelQuery): Response
    {
        $saleInvoices = $selectedModelQuery->execute()->getQuery()->getResult();
        usort($saleInvoices, function (SaleInvoice $a, SaleInvoice $b) {
            return $a->getDateFormatted() > $b->getDateFormatted();
        });
        $siforDates = $saleInvoices;
        $filterInfo = $this->admin->getFilterParameters();

        if (array_key_exists('date', $filterInfo)) {
            // get from to filter dates
            $from = $filterInfo['date']['value']['start'];
            $to = $filterInfo['date']['value']['end'];
        } else {
            $from = array_shift($siforDates)->getDateFormatted();
            if (!$siforDates) {
                $to = $from;
            } else {
                $to = array_pop($siforDates)->getDateFormatted();
            }
        }

        return new Response($this->sipm->outputSingle($saleInvoices, $from, $to), 200, ['Content-type' => 'application/pdf']);
    }

    /**
     * @return RedirectResponse|Response
     */
    public function pdfAction(Request $request)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var SaleInvoice $saleInvoice */
        $saleInvoice = $this->admin->getObject($id);
        if (!$saleInvoice) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /* TODO @var SaleRequestPdfManager $rps /
        $rps = $this->container->get('app.sale_request_pdf_manager');
        return new Response($rps->outputSingle($saleRequest), 200, array('Content-type' => 'application/pdf'));*/

        $this->addFlash('warning', 'Aquesta acció encara NO funciona!');

        return $this->redirectToRoute('admin_app_sale_saleinvoice_list');
    }

    /**
     * @return RedirectResponse|Response
     */
    public function pdfWithBackgroundAction(Request $request)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var SaleInvoice $saleInvoice */
        $saleInvoice = $this->admin->getObject($id);
        if (!$saleInvoice) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        // TODO
        $this->addFlash('warning', 'Aquesta acció encara NO funciona!');

        return $this->redirectToRoute('admin_app_sale_saleinvoice_list');
    }

    /**
     * @return RedirectResponse|Response
     */
    public function countAction(Request $request)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var SaleInvoice $saleInvoice */
        $saleInvoice = $this->admin->getObject($id);
        if (!$saleInvoice) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        // TODO
        $this->addFlash('warning', 'Aquesta acció encara NO funciona!');

        return $this->redirectToRoute('admin_app_sale_saleinvoice_list');
    }

    /**
     * @return RedirectResponse|Response
     *
     * @throws NonUniqueResultException
     */
    public function cloneAction(Request $request, EntityManagerInterface $em)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var SaleInvoice $saleInvoice */
        $saleInvoice = $this->admin->getObject($id);
        if (!$saleInvoice) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var SaleInvoiceSeriesRepository $collectionTypeRepository */
        $saleInvoiceSeriesRepository = $em->getRepository(SaleInvoiceSeries::class);
        /** @var SaleInvoiceSeries $collectionType */
        $saleInvoiceSeries = $saleInvoiceSeriesRepository->findOneBy(['isDefault' => true]);
        $clonedSaleInvoice = clone $saleInvoice;
        $clonedSaleInvoice->setSeries($saleInvoiceSeries);
        $deliveryNotes = $saleInvoice->getDeliveryNotes();
        /** @var SaleDeliveryNote $deliveryNote */
        foreach ($deliveryNotes as $deliveryNote) {
            $deliveryNote->setSaleInvoice($clonedSaleInvoice);
//            $deliveryNote->setIsInvoiced(true);
            $em->persist($deliveryNote);
        }
        $dueDates = $saleInvoice->getSaleInvoiceDueDates();
        /** @var SaleInvoiceDueDate $dueDate */
        foreach ($dueDates as $dueDate) {
            $dueDate->setSaleInvoice($clonedSaleInvoice);
            $em->persist($dueDate);
        }
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getModelManager()->find(Enterprise::class, 1);
        $nextInvoiceNumber = $this->im->getLastInvoiceNumberBySerieAndEnterprise($saleInvoiceSeries, $enterprise);
        $clonedSaleInvoice->setInvoiceNumber($nextInvoiceNumber);
//        $em->clear(SaleInvoice::class);
        $em->persist($clonedSaleInvoice);
        $em->flush();

        return $this->redirectToRoute('admin_app_sale_saleinvoice_edit', ['id' => $clonedSaleInvoice->getId()]);
    }

    /**
     * @return RedirectResponse|Response
     *
     * @throws ModelManagerException
     */
    public function setHasNotBeenCountedAction(Request $request)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var SaleInvoice $saleInvoice */
        $saleInvoice = $this->admin->getObject($id);
        if (!$saleInvoice) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        $saleInvoice->setHasBeenCounted(false);
        try {
            $this->admin->getModelManager()->update($saleInvoice);
            $this->addFlash('success', 'La factura se ha descontabilizado');
        } catch (\Exception $ex) {
            $this->addFlash('warning', 'No se pudo realizar la acción');
        }

        return $this->redirectToRoute('admin_app_sale_saleinvoice_edit', ['id' => $id]);
    }

    public function batchActionGeneratePdfsToPrint(ProxyQueryInterface $selectedModelQuery): Response
    {
        $this->admin->checkAccess('edit');
        /** @var SaleInvoice[] $saleDeliveryNotes */
        $saleInvoices = $selectedModelQuery->execute()->getQuery()->getResult();

        return new Response($this->sipm->outputCollectionPrint($saleInvoices), 200, ['Content-type' => 'application/pdf']);
    }

    public function batchActionGeneratePdfsForEmail(ProxyQueryInterface $selectedModelQuery): Response
    {
        $this->admin->checkAccess('edit');
        /** @var SaleInvoice[] $saleDeliveryNotes */
        $saleInvoices = $selectedModelQuery->execute()->getQuery()->getResult();

        return new Response($this->sipm->outputCollectionEmail($saleInvoices), 200, ['Content-type' => 'application/pdf']);
    }

    /**
     * @param SaleInvoice $object
     *
     * @throws ModelManagerException
     */
    public function preDelete(Request $request, $object): ?Response
    {
        return $this->generalPreDelete($object);
    }

    public function batchActionDelete(ProxyQueryInterface $query): Response
    {
        $saleInvoices = $query->execute();
        /** @var SaleInvoice $saleInvoice */
        foreach ($saleInvoices as $saleInvoice) {
            $this->generalPreDelete($saleInvoice);
        }

        return parent::batchActionDelete($query);
    }

    /**
     * @throws ModelManagerException
     * @throws \Sonata\AdminBundle\Exception\ModelManagerThrowable
     */
    private function generalPreDelete(SaleInvoice $object): ?RedirectResponse
    {
        if ($object->isHasBeenCounted()) {
            $this->addFlash('warning', 'No se puede borrar una factura contablilizada');

            return $this->redirectToRoute('admin_app_sale_saleinvoice_list');
        } else {
            try {
                /** @var SaleDeliveryNote $deliveryNote */
                foreach ($object->getDeliveryNotes() as $deliveryNote) {
                    $deliveryNote->setSaleInvoice(null);
                    $deliveryNote->setIsInvoiced(false);
                    $this->admin->getModelManager()->update($deliveryNote);
                }
            } catch (ModelManagerException $exception) {
                $this->addFlash('error', 'Error al actualizar albaranes relacionados: '.$exception->getMessage());
                throw $exception;
            }
        }

        return null;
    }

    /**
     * @return JsonResponse
     *
     * @throws NonUniqueResultException
     */
    public function getJsonNextInvoiceNumberForSeriesIdAndInvoiceAction(Request $request, int $id)
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getModelManager()->find(Enterprise::class, 1);
        /** @var SaleInvoiceSeries $series */
        $series = $this->admin->getModelManager()->find(SaleInvoiceSeries::class, $id);
        if (!$series) {
            throw $this->createNotFoundException(sprintf('unable to find the sale invoice series with id: %s', $id));
        }
        $nextInvoiceNumber = $this->im->getLastInvoiceNumberBySerieAndEnterprise($series, $enterprise);

        return new JsonResponse(['nextInvoiceNumber' => $nextInvoiceNumber]);
    }

    /**
     * @return JsonResponse
     *
     * @throws NonUniqueResultException
     */
    public function getJsonAvailableInvoiceNumbersForSeriesAction(Request $request, int $id)
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getModelManager()->find(Enterprise::class, 1);
        /** @var SaleInvoiceSeries $series */
        $series = $this->admin->getModelManager()->find(SaleInvoiceSeries::class, $id);
        if (!$series) {
            throw $this->createNotFoundException(sprintf('unable to find the sale invoice series with id: %s', $id));
        }
        $availableInvociceNumbers = $this->im->getAvailableNumbersBySerieAndEnterprise($series, $enterprise);

        return new JsonResponse($availableInvociceNumbers);
    }

    public function createEInvoiceAction(Request $request)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());
        /** @var SaleInvoice $saleInvoice */
        $saleInvoice = $this->admin->getObject($id);
        if (!$saleInvoice) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $xml = $this->EFacturaService->createEFactura($saleInvoice);
        $response = new Response($xml);
        $response->headers->set('Content-type', 'text/xml');
        $response->headers->set('Content-Disposition', 'attachment; filename="factura-e-'.$saleInvoice->getInvoiceNumber().'.xml"');
        // 'Content-Disposition' => 'attachment; filename="margenes.xlsx"',

        return $response;
    }
}
