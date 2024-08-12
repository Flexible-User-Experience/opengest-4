<?php

namespace App\Controller\Front;

use App\Entity\Web\ContactMessage;
use App\Entity\Web\Service;
use App\Form\Type\ContactMessageFormType;
use App\Repository\Vehicle\VehicleCheckingRepository;
use App\Repository\Web\ServiceRepository;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @category Controller
 */
class DefaultController extends AbstractController
{
    #[Route('/', name: 'front_homepage')]
    public function indexAction(ServiceRepository $sr): Response
    {
        /** @var Service|null $serviceGC */
        $serviceGC = $sr->findOneBy(['slug' => 'gruas-de-celosia']);
        /** @var Service|null $serviceGH */
        $serviceGH = $sr->findOneBy(['slug' => 'gruas-hidraulicas']);
        /** @var Service|null $servicePA */
        $servicePA = $sr->findOneBy(['slug' => 'plataformas-aereas-sobre-camion']);

        return $this->render('frontend/homepage.html.twig', [
            'serviceGC' => $serviceGC,
            'serviceGH' => $serviceGH,
            'servicePA' => $servicePA,
        ]);
    }

    #[Route('/empresa', name: 'front_company')]
    public function companyAction(Request $request, NotificationService $ns): Response
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
            // Clean up new form in production envioronment
            if ('prod' == $this->get('kernel')->getEnvironment()) {
                $contactMessage = new ContactMessage();
                $form = $this->createForm(ContactMessageFormType::class, $contactMessage);
            }
        }

        return $this->render('frontend/company.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }

    #[Route('/sobre-este-sitio', name: 'front_about')]
    public function aboutAction(): Response
    {
        return $this->render('frontend/about.html.twig');
    }

    #[Route('/privacidad', name: 'front_privacy')]
    public function privacyAction(): Response
    {
        return $this->render('frontend/privacy.html.twig');
    }

    #[Route('/mapa-del-web', name: 'front_sitemap')]
    public function sitemapAction(): Response
    {
        return $this->render('frontend/sitemap.html.twig');
    }

    #[Route('/test-email', name: 'front_test_email')]
    public function testEmailAction(VehicleCheckingRepository $vcr): Response
    {
        if ('prod' == $this->get('kernel')->getEnvironment()) {
            throw new HttpException(403);
        }
        $entities = $vcr->getItemsInvalidByEnabledVehicle();

        return $this->render(':Mails:vehicles_checking_invalid_admin_notification.html.twig', [
            'entities' => $entities,
            'show_devel_top_bar' => true,
        ]);
    }
}
