<?php

namespace App\Controller\Admin\Sale;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Sale\SaleRequestDocument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * Class SaleRequestHasDeliveryNoteAdminController.
 */
class SaleRequestDocumentAdminController extends BaseAdminController
{
    public function downloadDocumentAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var SaleRequestDocument $saleRequestDocument */
        $saleRequestDocument = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $saleRequestDocument, 'documentFile', $saleRequestDocument->getDocument());
    }
}
