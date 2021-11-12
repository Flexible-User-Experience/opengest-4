<?php

namespace App\Block;

use App\Repository\Vehicle\VehicleMaintenanceRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class VehicleMaintenanceBlock extends AbstractBlockService
{
    private VehicleMaintenanceRepository $vmr;

    public function __construct(Environment $twig, VehicleMaintenanceRepository $vmr)
    {
        parent::__construct($twig);
        $this->vmr = $vmr;
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null): Response
    {
        // merge settings
        $settings = $blockContext->getSettings();
        $vehicleNeedsRevisions = count($this->vmr->findBy(['needsCheck' => true]));
        $backgroundColor = 'bg-green';
        $content = '<h3><i class="fa fa-check-circle-o" aria-hidden="true"></i></h3><p>Tots els vehicles tenen les revisions al dia</p>';

        if ($vehicleNeedsRevisions > 0) {
            $backgroundColor = 'bg-yellow';
            $content = '<h3>'.$vehicleNeedsRevisions.'</h3><p>Vehicle/s requereixen manteniment</p>';
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
            'template' => 'admin/block/vehicle_maintenance.html.twig',
        ]);
    }
}
