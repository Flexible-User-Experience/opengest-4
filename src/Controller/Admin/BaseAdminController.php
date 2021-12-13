<?php

namespace App\Controller\Admin;

use App\Manager\InvoiceManager;
use App\Manager\Pdf\SaleDeliveryNotePdfManager;
use App\Manager\Pdf\SaleInvoicePdfManager;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Exception\NoFileFoundException;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * Class BaseAdminController.
 *
 * @category Controller
 *
 * @author   David Romaní <david@flux.cat>
 */
abstract class BaseAdminController extends Controller
{
    protected InvoiceManager $im;

    protected SaleDeliveryNotePdfManager $sdnpm;

    protected SaleInvoicePdfManager $sipm;

    public function __construct(InvoiceManager $invoiceManager, SaleDeliveryNotePdfManager $sdnpm, SaleInvoicePdfManager $sipm)
    {
        $this->im = $invoiceManager;
        $this->sdnpm = $sdnpm;
        $this->sipm = $sipm;
    }

    /**
     * @return Request
     */
    protected function resolveRequest(Request $request = null)
    {
        return $request;
    }

    protected function downloadDocument(Request $request, $id, DownloadHandler $downloadHandler, $object, $documentFile, $documentName): Response
    {
        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        try {
            $return = $downloadHandler->downloadObject(
                $object,
                $fileField = $documentFile,
                $objectClass = get_class($object),
                $fileName = $documentName,
                $forceDownload = false
            );
        } catch (\ErrorException | NoFileFoundException $e) {
            $this->addFlash(
                'warning',
                'No se pudo recuperar el documento  '.$documentName.'.'
            );
            $referer = $request->headers->get('referer');
            $return = new RedirectResponse($referer);
        }

        return $return;
    }
}
