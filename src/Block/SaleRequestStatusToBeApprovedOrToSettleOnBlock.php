<?php

namespace App\Block;

use App\Repository\Sale\SaleRequestRepository;
use Exception;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

class SaleRequestStatusToBeApprovedOrToSettleOnBlock extends AbstractBlockService
{
    private SaleRequestRepository $srr;
    private TokenStorageInterface $tss;

    public function __construct(Environment $twig, SaleRequestRepository $srr, TokenStorageInterface $tss)
    {
        parent::__construct($twig);
        $this->srr = $srr;
        $this->tss = $tss;
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     *
     * @throws Exception
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null): Response
    {
        // merge settings
        $settings = $blockContext->getSettings();
        $backgroundColor = 'bg-light-blue';
        $toBeApprovedSaleRequests = $this->srr->getFilteredByStatusFilteredByEnterpriseEnabledSortedByServiceDate($this->tss->getToken()->getUser()->getDefaultEnterprise(), 4);
        $toSettleOnSaleRequests = $this->srr->getFilteredByStatusFilteredByEnterpriseEnabledSortedByServiceDate($this->tss->getToken()->getUser()->getDefaultEnterprise(), 5);
        $saleRequests = array_merge($toBeApprovedSaleRequests, $toSettleOnSaleRequests);

        return $this->renderResponse(
            $blockContext->getTemplate(), [
                'block' => $blockContext->getBlock(),
                'settings' => $settings,
                'title' => 'admin.dashboard.to_be_approved_or_to_settle_on',
                'background' => $backgroundColor,
                'content' => $saleRequests,
                'show_date' => false,
            ],
            $response
        );
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'title' => 'admin.dashboard.to_be_approved_or_to_settle_on',
            'content' => 'Default content',
            'template' => 'admin/block/sale_requests.html.twig',
        ]);
    }
}
