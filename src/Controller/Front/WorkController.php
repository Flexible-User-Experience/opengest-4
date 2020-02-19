<?php

namespace App\Controller\Front;

use App\Entity\Web\Work;
use App\Enum\ConstantsEnum;
use App\Repository\Web\WorkImageRepository;
use App\Repository\Web\WorkRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class WorkController.
 *
 * @category Controller
 */
class WorkController extends AbstractController
{
    /**
     * @Route("/trabajos/{page}", name="front_works")
     *
     * @param WorkRepository $wr
     * @param int            $page
     *
     * @return Response
     */
    public function listAction(WorkRepository $wr, $page = 1)
    {
        $works = $wr->findEnabledSortedByDate();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($works, $page, ConstantsEnum::FRONTEND_ITEMS_PER_PAGE_LIMIT);

        return $this->render(':Frontend:works.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/trabajo/{slug}", name="front_work_detail")
     *
     * @param WorkRepository      $wr
     * @param WorkImageRepository $wir
     * @param string              $slug
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function detailAction(WorkRepository $wr, WorkImageRepository $wir, $slug)
    {
        /** @var Work|null $work */
        $work = $wr->findOneBy(['slug' => $slug]);
        if (!$work) {
            throw new EntityNotFoundException();
        }
        $images = $wir->findEnabledSortedByPosition($work);

        return $this->render(':Frontend:work_detail.html.twig', [
            'work' => $work,
            'images' => $images,
        ]);
    }
}
