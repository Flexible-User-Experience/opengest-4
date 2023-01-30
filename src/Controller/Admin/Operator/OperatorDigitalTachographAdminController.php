<?php

namespace App\Controller\Admin\Operator;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Operator\OperatorDigitalTachograph;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * Class OperatorDigitalTachographAdminController.
 */
class OperatorDigitalTachographAdminController extends BaseAdminController
{
    public function downloadAction(Request $request, DownloadHandler $downloadHandler): StreamedResponse
    {
        $id = $request->get($this->admin->getIdParameter());

        /** @var OperatorDigitalTachograph $operatorDigitalTachograph */
        $operatorDigitalTachograph = $this->admin->getObject($id);
        if (!$operatorDigitalTachograph) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return $downloadHandler->downloadObject($operatorDigitalTachograph, 'uploadedFile');
    }
}
