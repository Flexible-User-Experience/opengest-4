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
 * Class SaleRequestNextBlock.
 *
 * @category Block
 */
class SaleRequestNextBlock extends AbstractBlockService
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
        $nextSaleRequests = $this->srr->getNextFilteredByEnterpriseEnabledSortedByServiceDate($this->tss->getToken()->getUser()->getDefaultEnterprise());

        return $this->renderResponse(
            $blockContext->getTemplate(), [
            'block' => $blockContext->getBlock(),
            'settings' => $settings,
            'title' => 'admin.dashboard.next',
            'background' => $backgroundColor,
            'content' => $nextSaleRequests,
            'show_date' => true,
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
            'title' => 'admin.dashboard.next',
            'content' => 'Default content',
            'template' => 'admin/block/sale_requests.html.twig',
        ]);
    }
}
