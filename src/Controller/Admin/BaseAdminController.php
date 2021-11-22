<?php

namespace App\Controller\Admin;

use App\Entity\Vehicle\Vehicle;
use App\Manager\InvoiceManager;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    public function __construct(InvoiceManager $invoiceManager)
    {
        $this->im = $invoiceManager;
    }

    /**
     * @return Request
     */
    protected function resolveRequest(Request $request = null)
    {
        if (null === $request) {
            return $this->getRequest();
        }

        return $request;
    }

    protected function downloadDocument($id, DownloadHandler $downloadHandler, $documentFile, $documentName): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);
        if (!$vehicle) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return $downloadHandler->downloadObject(
            $vehicle,
            $fileField = $documentFile,
            $objectClass = Vehicle::class,
            $fileName = $documentName,
            $forceDownload = false
        );
    }
}
