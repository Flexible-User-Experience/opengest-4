<?php

namespace App\Controller\Front;

use App\Entity\Web\Work;
use App\Enum\ConstantsEnum;
use App\Repository\Web\WorkImageRepository;
use App\Repository\Web\WorkRepository;
use Doctrine\ORM\EntityNotFoundException;
use Knp\Component\Pager\PaginatorInterface;
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
    #[Route('/trabajos/{page}', name: 'front_works')]
    public function listAction(PaginatorInterface $paginator, WorkRepository $wr, $page = 1): Response
    {
        $works = $wr->findEnabledSortedByDate();
        $pagination = $paginator->paginate($works, $page, ConstantsEnum::FRONTEND_ITEMS_PER_PAGE_LIMIT);

        return $this->render('frontend/works.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route('/trabajo/{slug}', name: 'front_work_detail')]
    public function detailAction(WorkRepository $wr, WorkImageRepository $wir, $slug): Response
    {
        /** @var Work|null $work */
        $work = $wr->findOneBy(['slug' => $slug]);
        if (!$work) {
            throw new EntityNotFoundException();
        }
        $images = $wir->findEnabledSortedByPosition($work);

        return $this->render('frontend/work_detail.html.twig', [
            'work' => $work,
            'images' => $images,
        ]);
    }
}
