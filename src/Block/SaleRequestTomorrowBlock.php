<?php

namespace App\Block;

use App\Repository\Sale\SaleRequestRepository;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class SaleRequestTomorrowBlock.
 *
 * @category Block
 */
class SaleRequestTomorrowBlock extends AbstractBlockService
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
     * @param EngineInterface       $templating
     * @param SaleRequestRepository $srr
     * @param TokenStorageInterface $tss
     */
    public function __construct($name, EngineInterface $templating, SaleRequestRepository $srr, TokenStorageInterface $tss)
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
        $tomorroySaleRequests = $this->srr->getTomorrowFilteredByEnterpriseEnabledSortedByServiceDate($this->tss->getToken()->getUser()->getDefaultEnterprise());

        return $this->renderResponse(
            $blockContext->getTemplate(), [
            'block' => $blockContext->getBlock(),
            'settings' => $settings,
            'title' => 'admin.dashboard.tomorrow',
            'background' => $backgroundColor,
            'content' => $tomorroySaleRequests,
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
            'title' => 'admin.dashboard.tomorrow',
            'content' => 'Default content',
            'template' => 'admin/block/sale_requests.html.twig',
        ]);
    }
}
