<?php

namespace App\Block;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Templating\TwigEngine;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ContactMessageBlock.
 *
 * @category Block
 */
class ContactMessageBlock extends AbstractBlockService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * Methods.
     */

    /**
     * @param string                 $name
     * @param TwigEngine             $templating
     * @param EntityManagerInterface $em
     */
    public function __construct($name, TwigEngine $templating, EntityManagerInterface $em)
    {
        parent::__construct($name, $templating);
        $this->em = $em;
    }

    /**
     * Execute.
     *
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        // merge settings
        $settings = $blockContext->getSettings();

        $pendingMessagesAmount = $this->em->getRepository('App:Web\ContactMessage')->getPendingMessagesAmount();

        $backgroundColor = 'bg-green';
        $content = '<h3><i class="fa fa-check-circle-o" aria-hidden="true"></i></h3><p>Tots els missatges de contacte estan contestats</p>';

        if ($pendingMessagesAmount > 0) {
            $backgroundColor = 'bg-red';
            if (1 == $pendingMessagesAmount) {
                $content = '<h3>'.$pendingMessagesAmount.'</h3><p>Missatge de contacte pendent de contestar</p>';
            } else {
                $content = '<h3>'.$pendingMessagesAmount.'</h3><p>Missatges de contacte pendents de contestar</p>';
            }
        }

        return $this->renderResponse(
            $blockContext->getTemplate(),
            array(
                'block' => $blockContext->getBlock(),
                'settings' => $settings,
                'title' => 'Notificacions',
                'background' => $backgroundColor,
                'content' => $content,
            ),
            $response
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pending_messages';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'title' => 'Resum',
            'content' => 'Default content',
            'template' => ':Admin/Block:contact_message.html.twig',
        ));
    }
}
