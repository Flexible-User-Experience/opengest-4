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
    #[Route('/accesorios', name: 'front_complement')]
    public function complementAction(ComplementRepository $cr): Response
    {
        $complements = $cr->findEnabledSortedByName();

        return $this->render('frontend/complements.html.twig', [
            'complements' => $complements,
        ]);
    }

    #[Route('/accesorio/{slug}', name: 'front_complement_detail')]
    public function complementDetailAction(ComplementRepository $cr, $slug): Response
    {
        /** @var Complement|null $complement */
        $complement = $cr->findOneBy(['slug' => $slug]);
        if (!$complement) {
            throw new EntityNotFoundException();
        }

        return $this->render('frontend/complement_detail.html.twig', [
            'complement' => $complement,
        ]);
    }
}
