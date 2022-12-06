<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
        ]);
    }

    #[Route('/search', name: 'app_reservation_search', methods: ['GET'])]
    public function search(Request $request, ReservationRepository $reservationRepository, SerializerInterface $serializer , EntityManagerInterface $em)
    {
        $value = $request->query->get('query');
        $reservations = $em
        ->createQuery(
            'SELECT e
            FROM App\Entity\Reservation e
            WHERE e.service LIKE :str'
        )
        ->setParameter('str', '%'.$value.'%')
        ->getResult();

        $reservationsJson = $serializer->serialize($reservations, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
             }
         ]);

        //  dd($eventsJson);
        return new JsonResponse($reservationsJson);
    }
}
