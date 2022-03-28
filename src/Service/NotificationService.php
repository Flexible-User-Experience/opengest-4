<?php

namespace App\Service;

use App\Entity\Operator\OperatorChecking;
use App\Entity\Vehicle\VehicleChecking;
use App\Entity\Web\ContactMessage;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twig\Environment;

/**
 * Class NotificationService.
 *
 * @category Service
 *
 * @author   David Romaní <david@flux.cat>
 */
class NotificationService
{
    private CourierService $messenger;

    private Environment $twig;

    /**
     * @var string Mailer Destination
     */
    private string $amd;

    /**
     * @var string Mailer URL base
     */
    private string $urlBase;

    /**
     * Methods.
     */
    public function __construct(CourierService $messenger, Environment $twig, string $amd, string $urlBase)
    {
        $this->messenger = $messenger;
        $this->twig = $twig;
        $this->amd = $amd;
        $this->urlBase = $urlBase;
    }

    /**
     * Send a common notification mail to frontend user.
     *
     * @throws TransportExceptionInterface
     */
    public function sendCommonUserNotification(ContactMessage $contactMessage)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $contactMessage->getEmail(),
            'Notificació pàgina web '.$this->urlBase,
            $this->twig->render('mails/common_user_notification.html.twig', [
                'contact' => $contactMessage,
            ])
        );
    }

    /**
     * Send a common notification mail to admin user.
     *
     * @param string $text
     *
     * @throws TransportExceptionInterface
     */
    public function sendCommonAdminNotification($text)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $this->amd,
            'Notificació pàgina web '.$this->urlBase,
            $this->twig->render('mails/common_user_notification.html.twig', [
                'text' => $text,
            ])
        );
    }

    /**
     * Send a contact form notification to admin user.
     *
     * @throws TransportExceptionInterface
     */
    public function sendContactAdminNotification(ContactMessage $contactMessage)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $this->amd,
            'Missatge de contacte pàgina web '.$this->urlBase,
            $this->twig->render('mails/contact_form_admin_notification.html.twig', [
                'contact' => $contactMessage,
            ])
        );
    }

    /**
     * Send a contact form notification to admin user.
     *
     * @throws TransportExceptionInterface
     */
    public function sendUserBackendAnswerNotification(ContactMessage $contactMessage)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $contactMessage->getEmail(),
            'Resposta pàgina web '.$this->urlBase,
            $this->twig->render('/mails/user_backend_answer_notification.html.twig', [
                'contact' => $contactMessage,
            ])
        );
    }

    /**
     * @param array|OperatorChecking[] $entities
     *
     * @throws TransportExceptionInterface
     */
    public function sendOperatorCheckingInvalidNotification($entities)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $this->amd,
            'Avís de revisions d\'operaris caducades avui pàgina web '.$this->urlBase,
            $this->twig->render('mails/operators_checking_invalid_admin_notification.html.twig', [
                'entities' => $entities,
            ])
        );
    }

    /**
     * @param array|OperatorChecking[] $entities
     *
     * @throws TransportExceptionInterface
     */
    public function sendOperatorCheckingBeforeToBeInvalidNotification($entities)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $this->amd,
            'Avís de revisions d\'operaris pedent de caducar pàgina web '.$this->urlBase,
            $this->twig->render('mails/operators_checking_before_to_be_invalid_notification.html.twig', [
                'entities' => $entities,
            ])
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendToOperatorInvalidCheckingNotification(OperatorChecking $entity)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $entity->getOperator()->getEmail(),
            'Avís de revisió caducada',
            $this->twig->render('mails/operator_invalid_notification.html.twig', [
                'operatorChecking' => $entity,
            ])
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendToOperatorBeforeToBeInvalidCheckingNotification(OperatorChecking $entity)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $entity->getOperator()->getEmail(),
            'Avís de revisió a punt de caducar',
            $this->twig->render('mails/operator_before_to_be_invalid_notification.html.twig', [
                'operatorChecking' => $entity,
            ])
        );
    }

    /**
     * @param array|VehicleChecking[] $entities
     *
     * @throws TransportExceptionInterface
     */
    public function sendVehicleCheckingInvalidNotification($entities)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $this->amd,
            'Avís de revisions de vehicles caducades avui pàgina web '.$this->urlBase,
            $this->twig->render('mails/vehicles_checking_invalid_admin_notification.html.twig', [
                'entities' => $entities,
            ])
        );
    }

    /**
     * @param array|VehicleChecking[] $entities
     *
     * @throws TransportExceptionInterface
     */
    public function sendVehicleCheckingBeforeToBeInvalidNotification($entities)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $this->amd,
            'Avís de revisions de vehicles pedent de caducar pàgina web '.$this->urlBase,
            $this->twig->render('mails/vehicles_checking_before_to_be_invalid_notification.html.twig', [
                'entities' => $entities,
            ])
        );
    }
}
