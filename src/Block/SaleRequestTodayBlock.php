<?php

namespace App\Block;

use App\Repository\Sale\SaleRequestRepository;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Templating\TwigEngine;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class SaleRequestTodayBlock.
 *
 * @category Block
 */
class SaleRequestTodayBlock extends AbstractBlockService
{
    /**
     * @var SaleRequestRepository
     */
    private SaleRequestRepository $srr;

    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tss;

    /**
     * Methods.
     */

    /**
     * @param null|string           $name
     * @param TwigEngine            $templating
     * @param SaleRequestRepository $srr
     * @param TokenStorageInterface $tss
     */
    public function __construct($name, TwigEngine $templating, SaleRequestRepository $srr, TokenStorageInterface $tss)
    {
        parent::__construct($name, $templating);
        $this->srr = $srr;
        $this->tss = $tss;
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        // merge settings
        $settings = $blockContext->getSettings();
        $backgroundColor = 'bg-light-blue';
        $todaySaleRequests = $this->srr->getTodayFilteredByEnterpriseEnabledSortedByServiceDate($this->tss->getToken()->getUser()->getDefaultEnterprise());

        return $this->renderResponse(
            $blockContext->getTemplate(), [
                'block' => $blockContext->getBlock(),
                'settings' => $settings,
                'title' => 'admin.dashboard.today',
                'background' => $backgroundColor,
                'content' => $todaySaleRequests,
                'show_date' => false,
            ],
            $response
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sale_request_today';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'title' => 'admin.dashboard.today',
            'content' => 'Default content',
            'template' => ':Admin/Block:sale_requests.html.twig',
        ]);
    }
}
