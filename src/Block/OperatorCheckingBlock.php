<?php

namespace App\Block;

use App\Repository\Operator\OperatorCheckingRepository;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class OperatorCheckingBlock.
 *
 * @category Block
 */
class OperatorCheckingBlock extends AbstractBlockService
{
    /**
     * @var OperatorCheckingRepository
     */
    private OperatorCheckingRepository $ocr;

    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tss;

    /**
     * Methods.
     */

    /**
     * @param null|string                $name
     * @param EngineInterface            $templating
     * @param OperatorCheckingRepository $ocr
     * @param TokenStorageInterface      $tss
     */
    public function __construct($name, EngineInterface $templating, OperatorCheckingRepository $ocr, TokenStorageInterface $tss)
    {
        parent::__construct($name, $templating);
        $this->ocr = $ocr;
        $this->tss = $tss;
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
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

    /**
     * @return string
     */
    public function getName()
    {
        return 'operator_checking';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'title' => 'Resum',
            'content' => 'Default content',
            'template' => 'admin/block/operator_checking.html.twig',
        ));
    }
}
