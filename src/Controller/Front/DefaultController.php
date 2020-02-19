<?php

namespace App\Controller\Front;

use App\Entity\Web\ContactMessage;
use App\Form\Type\ContactMessageFormType;
use App\Repository\Vehicle\VehicleCheckingRepository;
use App\Repository\Web\ServiceRepository;
use App\Service\NotificationService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @category Controller
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="front_homepage")
     *
     * @param ServiceRepository $sr
     *
     * @return Response
     */
    public function indexAction(ServiceRepository $sr)
    {
        $serviceGC = $sr->findOneBy(['slug' => 'gruas-de-celosia']);
        $serviceGH = $sr->findOneBy(['slug' => 'gruas-hidraulicas']);
        $servicePA = $sr->findOneBy(['slug' => 'plataformas-aereas-sobre-camion']);

        return $this->render(':Frontend:homepage.html.twig', array(
            'serviceGC' => $serviceGC,
            'serviceGH' => $serviceGH,
            'servicePA' => $servicePA,
        ));
    }

    /**
     * @Route("/empresa", name="front_company")
     *
     * @param Request             $request
     * @param NotificationService $ns
     *
     * @return Response
     *
     * @throws TransportExceptionInterface
     */
    public function companyAction(Request $request, NotificationService $ns)
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

        return $this->render(':Frontend:company.html.twig', array(
            'contactForm' => $form->createView(),
        ));
    }

    /**
     * @Route("/sobre-este-sitio", name="front_about")
     */
    public function aboutAction()
    {
        return $this->render(':Frontend:about.html.twig');
    }

    /**
     * @Route("/privacidad", name="front_privacy")
     */
    public function privacyAction()
    {
        return $this->render(':Frontend:privacy.html.twig');
    }

    /**
     * @Route("/mapa-del-web", name="front_sitemap")
     */
    public function sitemapAction()
    {
        return $this->render(':Frontend:sitemap.html.twig');
    }

    /**
     * @Route("/test-email", name="front_test_email")
     *
     * @param VehicleCheckingRepository $vcr
     *
     * @return Response
     *
     * @throws HttpException
     * @throws Exception
     */
    public function testEmailAction(VehicleCheckingRepository $vcr)
    {
        if ('prod' == $this->get('kernel')->getEnvironment()) {
            throw new HttpException(403);
        }
        $entities = $vcr->getItemsInvalidByEnabledVehicle();
//        $contact = $this->getDoctrine()->getRepository('App:ContactMessage')->find(223);

        return $this->render(':Mails:vehicles_checking_invalid_admin_notification.html.twig', array(
            'entities' => $entities,
            'show_devel_top_bar' => true,
        ));
    }
}
