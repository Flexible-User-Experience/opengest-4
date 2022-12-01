<?php

namespace App\Controller\Admin\Setting;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Setting\Document;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * Class DocumentAdminController.
 *
 * @category Controller
 *
 * @author Jordi Sort <jordi.sort@mirmit.com>
 */
class DocumentAdminController extends BaseAdminController
{
    public function downloadDocumentAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Document $document */
        $document = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $document, 'fileFile', $document->getFile());
    }
}
