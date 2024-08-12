<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;

/**
 * Class CourierService.
 *
 * @category Service
 *
 * @author   David Romaní <david@flux.cat>
 */
class CourierService
{
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * Methods.
     */

    /**
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param string      $from
     * @param string      $to
     * @param string      $subject
     * @param string      $body
     * @param string|null $replyAddress
     *
     * @return void
     * @throws TransportExceptionInterface
     */
    public function sendEmail($from, $to, $subject, $body, $replyAddress = null): void
    {
        $email = new Email();
        $email
            ->subject($subject)
            ->from($from)
            ->to($to)
            ->html($body)
        ;
        if (!is_null($replyAddress)) {
            $email->replyTo($replyAddress);
        }

        $this->mailer->send($email);
    }
}
