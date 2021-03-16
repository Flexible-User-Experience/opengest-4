<?php

namespace App\Block;

use App\Repository\Operator\OperatorCheckingRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

class OperatorCheckingBlock extends AbstractBlockService
{
    private OperatorCheckingRepository $ocr;
    private TokenStorageInterface $tss;

    public function __construct(Environment $twig, OperatorCheckingRepository $ocr, TokenStorageInterface $tss)
    {
        parent::__construct($twig);
        $this->ocr = $ocr;
        $this->tss = $tss;
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null): Response
    {
        // merge settings
        $settings = $blockContext->getSettings();

        $operatorsInvalidAmount = $this->ocr->getItemsInvalidSinceTodayByEnterpriseAmount($this->tss->getToken()->getUser()->getDefaultEnterprise());
        $operatorsBeforeInvalidAmount = $this->ocr->getItemsBeforeToBeInvalidSinceTodayByEnterpriseAmount($this->tss->getToken()->getUser()->getDefaultEnterprise());

        $backgroundColor = 'bg-green';
        $content = '<h3><i class="fa fa-check-circle-o" aria-hidden="true"></i></h3><p>Tots els operaris tenen les revisions al dia</p>';

        if ($operatorsBeforeInvalidAmount > 0 && $operatorsInvalidAmount > 0) {
            $backgroundColor = 'bg-red';
            $content = '<h3>'.$operatorsInvalidAmount.'</h3><p>Operaris tenen revisions caducades</p><p>'.$operatorsBeforeInvalidAmount.' operaris tenen revisions a punt de caducar</p>';
        } elseif ($operatorsInvalidAmount > 0) {
            $backgroundColor = 'bg-red';
            $content = '<h3>'.$operatorsInvalidAmount.'</h3><p>Operaris tenen revisions caducades</p>';
        } elseif ($operatorsBeforeInvalidAmount > 0) {
            $backgroundColor = 'bg-yellow';
            $content = '<h3>'.$operatorsBeforeInvalidAmount.'</h3><p>Operaris tenen revisions a punt de caducar</p>';
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

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'title' => 'Resum',
            'content' => 'Default content',
            'template' => 'admin/block/operator_checking.html.twig',
        ));
    }
}
