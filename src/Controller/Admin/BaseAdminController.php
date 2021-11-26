<?php

namespace App\Controller\Admin;

use App\Manager\InvoiceManager;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    protected function downloadDocument($id, DownloadHandler $downloadHandler, $object, $documentFile, $documentName): Response
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
        } catch (\ErrorException $e) {
            $this->addFlash(
                'warning',
                'No se pudo recuperar el documento  '.$documentName.'.'
            );
            $referer = $this->getRequest()->headers->get('referer');
            if (null == $referer) {
                $url = $this->router->generate('fallback_url');
            } else {
                $url = $referer;
            }
            $return = new RedirectResponse($url);
        }

        return $return;
    }
}
