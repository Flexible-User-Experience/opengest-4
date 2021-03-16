<?php

namespace App\Block;

use App\Repository\Vehicle\VehicleCheckingRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

class VehicleCheckingBlock extends AbstractBlockService
{
    private VehicleCheckingRepository $vcr;
    private TokenStorageInterface $tss;

    public function __construct(Environment $twig, VehicleCheckingRepository $vcr, TokenStorageInterface $tss)
    {
        parent::__construct($twig);
        $this->vcr = $vcr;
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

        $vehiclesInvalidAmount = $this->vcr->getItemsInvalidSinceTodayByEnterpriseAmount($this->tss->getToken()->getUser()->getDefaultEnterprise());
        $vehiclesBeforeInvalidAmount = $this->vcr->getItemsBeforeToBeInvalidSinceTodayByEnterpriseAmount($this->tss->getToken()->getUser()->getDefaultEnterprise());

        $backgroundColor = 'bg-green';
        $content = '<h3><i class="fa fa-check-circle-o" aria-hidden="true"></i></h3><p>Tots els vehicles tenen les revisions al dia</p>';

        if ($vehiclesBeforeInvalidAmount > 0 && $vehiclesInvalidAmount > 0) {
            $backgroundColor = 'bg-red';
            $content = '<h3>'.$vehiclesInvalidAmount.'</h3><p>Vehicles tenen revisions caducades</p><p>'.$vehiclesBeforeInvalidAmount.' vehicles tenen revisions a punt de caducar</p>';
        } elseif ($vehiclesInvalidAmount > 0) {
            $backgroundColor = 'bg-red';
            $content = '<h3>'.$vehiclesInvalidAmount.'</h3><p>Vehicles tenen revisions caducades</p>';
        } elseif ($vehiclesBeforeInvalidAmount > 0) {
            $backgroundColor = 'bg-yellow';
            $content = '<h3>'.$vehiclesBeforeInvalidAmount.'</h3><p>Vehicles tenen revisions a punt de caducar</p>';
        }

        return $this->renderResponse(
            $blockContext->getTemplate(), [
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
            'template' => 'admin/block/vehicle_checking.html.twig',
        ]);
    }
}
