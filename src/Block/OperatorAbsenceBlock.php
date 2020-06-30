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
use Symfony\Component\Templating\EngineInterface;

/**
 * Class OperatorAbsenceBlock.
 *
 * @category Block
 */
class OperatorAbsenceBlock extends AbstractBlockService
{
    /**
     * @var OperatorAbsenceRepository
     */
    private OperatorAbsenceRepository $oar;

    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tss;

    /**
     * Methods.
     */

    /**
     * @param null|string               $name
     * @param EngineInterface           $templating
     * @param OperatorAbsenceRepository $oar
     * @param TokenStorageInterface     $tss
     */
    public function __construct($name, EngineInterface $templating, OperatorAbsenceRepository $oar, TokenStorageInterface $tss)
    {
        parent::__construct($name, $templating);
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
    public function execute(BlockContextInterface $blockContext, Response $response = null)
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
        } elseif ($operatorsAbsentAmount > 0 && 0 == $operatorsBeforeAbsent) {
            $backgroundColor = 'bg-red';
            $content = '<h3>'.$operatorsAbsentAmount.'</h3><p>Operaris avui no estan disponibles</p>';
        } elseif (0 == $operatorsAbsentAmount && $operatorsBeforeAbsent > 0) {
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

    /**
     * @return string
     */
    public function getName()
    {
        return 'operator_absence';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'title' => 'Resum',
            'content' => 'Default content',
            'template' => 'admin/block/operator_absence.html.twig',
        ));
    }
}
