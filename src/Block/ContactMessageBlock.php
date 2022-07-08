<?php

namespace App\Block;

use App\Entity\Web\ContactMessage;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class ContactMessageBlock extends AbstractBlockService
{
    private EntityManagerInterface $em;

    public function __construct(Environment $twig, EntityManagerInterface $em)
    {
        parent::__construct($twig);
        $this->em = $em;
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null): Response
    {
        // merge settings
        $settings = $blockContext->getSettings();

        $pendingMessagesAmount = $this->em->getRepository(ContactMessage::class)->getPendingMessagesAmount();

        $backgroundColor = 'bg-green';
        $content = '<h3><i class="fa fa-check-circle-o" aria-hidden="true"></i></h3><p>Tots els missatges de contacte estan contestats</p>';

        if ($pendingMessagesAmount > 0) {
            $backgroundColor = 'bg-red';
            if (1 === $pendingMessagesAmount) {
                $content = '<h3>'.$pendingMessagesAmount.'</h3><p>Missatge de contacte pendent de contestar</p>';
            } else {
                $content = '<h3>'.$pendingMessagesAmount.'</h3><p>Missatges de contacte pendents de contestar</p>';
            }
        }

        return $this->renderResponse(
            $blockContext->getTemplate(),
            [
                'block' => $blockContext->getBlock(),
                'settings' => $settings,
                'title' => 'Notificacions',
                'background' => $backgroundColor,
                'content' => $content,
            ],
            $response
        );
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'title' => 'Resum',
            'content' => 'Default content',
            'template' => 'admin/block/contact_message.html.twig',
        ]);
    }
}
