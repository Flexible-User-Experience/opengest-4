<?php

namespace App\Controller\Front;

use App\Enum\ConstantsEnum;
use Doctrine\ORM\EntityNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class WorkController.
 *
 * @category Controller
 */
class WorkController extends Controller
{
    /**
     * @Route("/trabajos/{page}", name="front_works")
     *
     * @param int $page
     *
     * @return Response
     */
    public function listAction($page = 1)
    {
        $works = $this->getDoctrine()->getRepository('App:Web\Work')->findEnabledSortedByDate();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($works, $page, ConstantsEnum::FRONTEND_ITEMS_PER_PAGE_LIMIT);

        return $this->render(':Frontend:works.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/trabajo/{slug}", name="front_work_detail")
     *
     * @param $slug
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function detailAction($slug)
    {
        $work = $this->getDoctrine()->getRepository('App:Web\Work')->findOneBy(['slug' => $slug]);
        if (!$work) {
            throw new EntityNotFoundException();
        }
        $images = $this->getDoctrine()->getRepository('App:Web\WorkImage')->findEnabledSortedByPosition($work);

        return $this->render(':Frontend:work_detail.html.twig', [
            'work' => $work,
            'images' => $images,
        ]);
    }
}
