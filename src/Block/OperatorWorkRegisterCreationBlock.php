<?php

namespace App\Block;

use App\Repository\Operator\OperatorRepository;
use App\Repository\Sale\SaleRequestRepository;
use Exception;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

class OperatorWorkRegisterCreationBlock extends AbstractBlockService
{
    private OperatorRepository $operatorRepository;
    private TokenStorageInterface $tss;

    public function __construct(Environment $twig, OperatorRepository $or, TokenStorageInterface $tss)
    {
        parent::__construct($twig);
        $this->operatorRepository = $or;
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
        $operators = $this->operatorRepository->getFilteredByEnterpriseEnabledSortedByName($this->tss->getToken()->getUser()->getDefaultEnterprise());
        // merge settings
        $settings = $blockContext->getSettings();
        $backgroundColor = 'bg-light-blue';
        $content = array(
            'operators' => $operators
        );

        return $this->renderResponse(
            $blockContext->getTemplate(), [
                'block' => $blockContext->getBlock(),
                'settings' => $settings,
                'title' => 'admin.dashboard.pending',
                'background' => $backgroundColor,
                'content' => $content,
                'show_date' => true,
            ],
            $response
        );
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'title' => 'admin.operator.operator_work_register_create',
            'content' => 'Default content',
            'template' => 'admin/block/operator_work_register_create.html.twig',
            'info' => 'Default info'
        ]);
    }
}
