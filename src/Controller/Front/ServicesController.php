<?php

namespace App\Controller\Front;

use App\Entity\Web\ContactMessage;
use App\Entity\Web\Service;
use App\Form\Type\ContactMessageFormType;
use App\Repository\Web\ServiceRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class ServicesController.
 *
 * @category Controller
 */
class ServicesController extends AbstractController
{
    #[Route('/servicios', name: 'front_services')]
    public function servicesAction(ServiceRepository $sr): RedirectResponse|Response
    {
        $services = $sr->findEnabledSortedByPositionAndName();
        if (0 == count($services)) {
            return $this->render('frontend/services_empty.html.twig');
        }
        /** @var Service $service */
        $service = $services[0];

        return $this->redirectToRoute('front_service_detail', [
            'slug' => $service->getSlug(),
        ]);
    }

    /**
     * @throws EntityNotFoundException
     * @throws TransportExceptionInterface
     */
    #[Route('/servicio/{slug}', name: 'front_service_detail')]
    public function detailServiceAction(Request $request, NotificationService $ns, ServiceRepository $sr, $slug): Response
    {
        $contactMessage = new ContactMessage();
        $form = $this->createForm(ContactMessageFormType::class, $contactMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set frontend flash message
            $this->addFlash(
                'notice',
                'Tu mensaje se ha enviado correctamente'
            );
            // Persist new contact message into DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($contactMessage);
            $em->flush();
            // Send notification
            $ns->sendCommonUserNotification($contactMessage);
            $ns->sendContactAdminNotification($contactMessage);
            // Clean up new form in production eviorament
            if ('prod' == $this->get('kernel')->getEnvironment()) {
                $contactMessage = new ContactMessage();
                $form = $this->createForm(ContactMessageFormType::class, $contactMessage);
            }
        }

        $service = $sr->findOneBy(['slug' => $slug]);
        if (!$service) {
            throw new EntityNotFoundException();
        }

        return $this->render('frontend/service_detail.html.twig', [
            'service' => $service,
            'contactForm' => $form->createView(),
        ]);
    }
}
