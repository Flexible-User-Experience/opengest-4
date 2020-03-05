<?php

namespace App\Service;

use App\Entity\Web\ContactMessage;
use App\Entity\Operator\OperatorChecking;
use App\Entity\Vehicle\VehicleChecking;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Class NotificationService.
 *
 * @category Service
 *
 * @author   David Romaní <david@flux.cat>
 */
class NotificationService
{
    /**
     * @var CourierService
     */
    private CourierService $messenger;

    /**
     * @var EngineInterface
     */
    private EngineInterface $twig;

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

    /**
     * @param CourierService  $messenger
     * @param EngineInterface $twig
     * @param string          $amd
     * @param string          $urlBase
     */
    public function __construct(CourierService $messenger, EngineInterface $twig, string $amd, string $urlBase)
    {
        $this->messenger = $messenger;
        $this->twig = $twig;
        $this->amd = $amd;
        $this->urlBase = $urlBase;
    }

    /**
     * Send a common notification mail to frontend user.
     *
     * @param ContactMessage $contactMessage
     *
     * @throws TransportExceptionInterface
     */
    public function sendCommonUserNotification(ContactMessage $contactMessage)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $contactMessage->getEmail(),
            'Notificació pàgina web '.$this->urlBase,
            $this->twig->render(':Mails:common_user_notification.html.twig', array(
                'contact' => $contactMessage,
            ))
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
            $this->twig->render(':Mails:common_admin_notification.html.twig', array(
                'text' => $text,
            ))
        );
    }

    /**
     * Send a contact form notification to admin user.
     *
     * @param ContactMessage $contactMessage
     *
     * @throws TransportExceptionInterface
     */
    public function sendContactAdminNotification(ContactMessage $contactMessage)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $this->amd,
            'Missatge de contacte pàgina web '.$this->urlBase,
            $this->twig->render(':Mails:contact_form_admin_notification.html.twig', array(
                'contact' => $contactMessage,
            ))
        );
    }

    /**
     * Send a contact form notification to admin user.
     *
     * @param ContactMessage $contactMessage
     *
     * @throws TransportExceptionInterface
     */
    public function sendUserBackendAnswerNotification(ContactMessage $contactMessage)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $contactMessage->getEmail(),
            'Resposta pàgina web '.$this->urlBase,
            $this->twig->render(':Mails:user_backend_answer_notification.html.twig', array(
                'contact' => $contactMessage,
            ))
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
            $this->twig->render(':Mails:operators_checking_invalid_admin_notification.html.twig', array('entities' => $entities))
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
            $this->twig->render(':Mails:operators_checking_before_to_be_invalid_notification.html.twig', array('entities' => $entities))
        );
    }

    /**
     * @param OperatorChecking $entity
     *
     * @throws TransportExceptionInterface
     */
    public function sendToOperatorInvalidCheckingNotification(OperatorChecking $entity)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $entity->getOperator()->getEmail(),
            'Avís de revisió caducada',
            $this->twig->render(':Mails:operator_invalid_notification.html.twig', array('operatorChecking' => $entity))
        );
    }

    /**
     * @param OperatorChecking $entity
     *
     * @throws TransportExceptionInterface
     */
    public function sendToOperatorBeforeToBeInvalidCheckingNotification(OperatorChecking $entity)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $entity->getOperator()->getEmail(),
            'Avís de revisió a punt de caducar',
            $this->twig->render(':Mails:operator_before_to_be_invalid_notification.html.twig', array('operatorChecking' => $entity))
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
            $this->twig->render(':Mails:vehicles_checking_invalid_admin_notification.html.twig', array('entities' => $entities))
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
            $this->twig->render(':Mails:vehicles_checking_before_to_be_invalid_notification.html.twig', array('entities' => $entities))
        );
    }
}
