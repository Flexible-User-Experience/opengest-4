<?php

namespace App\Controller\Front;

use App\Entity\Web\Complement;
use App\Repository\Web\ComplementRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ComplementController.
 *
 * @category Controller
 */
class ComplementController extends AbstractController
{
    /**
     * @Route("/accesorios", name="front_complement")
     *
     * @param ComplementRepository $cr
     *
     * @return Response
     */
    public function complementAction(ComplementRepository $cr)
    {
        $complements = $cr->findEnabledSortedByName();

        return $this->render(':Frontend:complements.html.twig', [
            'complements' => $complements,
        ]);
    }

    /**
     * @Route("/accesorio/{slug}", name="front_complement_detail")
     *
     * @param ComplementRepository $cr
     * @param string               $slug
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function complementDetailAction(ComplementRepository $cr, $slug)
    {
        /** @var Complement|null $complement */
        $complement = $cr->findOneBy(['slug' => $slug]);
        if (!$complement) {
            throw new EntityNotFoundException();
        }

        return $this->render(':Frontend:complement_detail.html.twig', [
            'complement' => $complement,
        ]);
    }
}
