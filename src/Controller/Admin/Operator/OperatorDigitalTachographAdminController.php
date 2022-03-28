<?php

namespace App\Controller\Admin\Operator;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Operator\OperatorDigitalTachograph;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class OperatorDigitalTachographAdminController.
 */
class OperatorDigitalTachographAdminController extends BaseAdminController
{
    /**
     * @param int|null $id
     *
     * @return StreamedResponse
     */
    public function downloadAction($id = null)
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());

        /** @var OperatorDigitalTachograph $operatorDigitalTachograph */
        $operatorDigitalTachograph = $this->admin->getObject($id);
        if (!$operatorDigitalTachograph) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        $downloadHandler = $this->container->get('vich_uploader.download_handler');

        return $downloadHandler->downloadObject($operatorDigitalTachograph, 'uploadedFile');
    }
}
