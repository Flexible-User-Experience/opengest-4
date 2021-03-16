<?php

namespace App\Block;

use App\Repository\Operator\OperatorAbsenceRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

class OperatorAbsenceBlock extends AbstractBlockService
{
    private OperatorAbsenceRepository $oar;
    private TokenStorageInterface $tss;

    public function __construct(Environment $twig, OperatorAbsenceRepository $oar, TokenStorageInterface $tss)
    {
        parent::__construct($twig);
        $this->oar = $oar;
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

        $operatorsAbsentAmount = $this->oar->getItemsAbsenceTodayByEnterpriseAmount($this->tss->getToken()->getUser()->getDefaultEnterprise());
        $operatorsBeforeAbsent = $this->oar->getItemsToBeAbsenceTomorrowByEnterpriseAmount($this->tss->getToken()->getUser()->getDefaultEnterprise());

        $backgroundColor = 'bg-green';
        $content = '<h3><i class="fa fa-check-circle-o" aria-hidden="true"></i></h3><p>Avui tots els operaris estan disponibles</p>';

        if ($operatorsBeforeAbsent > 0 && $operatorsAbsentAmount > 0) {
            $backgroundColor = 'bg-red';
            $content = '<h3>'.$operatorsAbsentAmount.'</h3><p>Operaris avui no estan disponibles</p><p>'.$operatorsBeforeAbsent.' operaris demà no estaran disponibles</p>';
        } elseif ($operatorsAbsentAmount > 0 && 0 === $operatorsBeforeAbsent) {
            $backgroundColor = 'bg-red';
            $content = '<h3>'.$operatorsAbsentAmount.'</h3><p>Operaris avui no estan disponibles</p>';
        } elseif (0 === $operatorsAbsentAmount && $operatorsBeforeAbsent > 0) {
            $backgroundColor = 'bg-yellow';
            $content = '<h3>'.$operatorsBeforeAbsent.'</h3><p>Operaris demà no estaran disponibles</p>';
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
            'template' => 'admin/block/operator_absence.html.twig',
        ));
    }
}
