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
     * Show action.
     *
     * @param int|string|null $id
     * @param Request         $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     */
    public function showAction($id = null, Request $request = null)
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

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->renderWithExtraParams(
            $this->admin->getTemplate('show'),
            array(
                'action' => 'show',
                'object' => $object,
                'elements' => $this->admin->getShow(),
            )
        );
    }

    /**
     * Answer message action.
     *
     * @param int|string|null $id
     * @param Request         $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     */
    public function answerAction($id = null, Request $request = null)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var ContactMessage $object */
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }
        $em = $this->getDoctrine()->getManager();
        $object->setChecked(true);
        $em->flush();

        $form = $this->createForm(ContactMessageAnswerFormType::class, $object);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // persist new contact message form record
            $object->setChecked(true);
            $em->flush();
            // send notifications
            $messenger = $this->get('app.notification');
            $messenger->sendUserBackendAnswerNotification($object);
            // build flash message
            $this->addFlash('success', 'La teva resposta ha estat enviada correctament.');

            return $this->redirectToRoute('admin_app_contactmessage_list');
        }

        return $this->renderWithExtraParams(
            'admin/contact-message/answer_form.html.twig',
            array(
                'action' => 'answer',
                'object' => $object,
                'form' => $form->createView(),
                'elements' => $this->admin->getShow(),
            )
        );
    }
}
