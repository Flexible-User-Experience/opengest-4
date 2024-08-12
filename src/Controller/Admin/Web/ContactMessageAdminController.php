<?php

namespace App\Controller\Admin\Web;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Web\ContactMessage;
use App\Form\Type\ContactMessageAnswerFormType;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ContactMessageAdminController.
 *
 * @category Controller
 *
 * @author   David Romaní <david@flux.cat>
 */
class ContactMessageAdminController extends BaseAdminController
{
    /**
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     */
    public function showAction(Request $request = null): Response
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var ContactMessage $object */
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        $object->setChecked(true);
        $this->admin->checkAccess('show', $object);

        $preResponse = $this->preShow($request, $object);
        if (null !== $preResponse) {
            return $preResponse;
        }
        $this->admin->setSubject($object);
        $this->admin->update($object);

        return $this->renderWithExtraParams(
            $this->admin->getTemplateRegistry()->getTemplate('show'),
            [
                'action' => 'show',
                'object' => $object,
                'elements' => $this->admin->getShow(),
            ]
        );
    }

    /**
     * @return Response
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     */
    public function answerAction(Request $request = null)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var ContactMessage $object */
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }
        $object->setChecked(true);
        $this->admin->update($object);

        $form = $this->createForm(ContactMessageAnswerFormType::class, $object);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // persist new contact message form record
            $object->setChecked(true);
            $this->admin->update($object);
            // send notifications
            $messenger = $this->get('app.notification');
            $messenger->sendUserBackendAnswerNotification($object);
            // build flash message
            $this->addFlash('success', 'La teva resposta ha estat enviada correctament.');

            return $this->redirectToRoute('admin_app_contactmessage_list');
        }

        return $this->renderWithExtraParams(
            'admin/contact-message/answer_form.html.twig',
            [
                'action' => 'answer',
                'object' => $object,
                'form' => $form->createView(),
                'elements' => $this->admin->getShow(),
            ]
        );
    }
}
