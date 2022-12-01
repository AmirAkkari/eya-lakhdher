<?php

namespace App\Controller;

use DateTime;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/reservation')]
class ReservationController extends AbstractController
{
    #[Route('/', name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ReservationRepository $reservationRepository, ValidatorInterface $validator ,\Swift_Mailer $mailer): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() ) {
            $client = $request->request->get('prenom')." ". $request->request->get('name');
            $reservation->setClient($client);
            $errors = $validator->validate($reservation);
            // dd($errors);
            if (count($errors) > 0) {
                // dd(1);
                $errorsString = (string) $errors;
                // dd($errors);
                return $this->renderForm('reservation/new.html.twig', [
                    'reservation' => $reservation,
                    'form' => $form,
                    "errors" => $errors
                ]);
            }
            
            $reservation->setEtat("active");
            $reservation->setDate(new DateTime("now"));
            $reservationRepository->save($reservation, true);

            $message = (new \Swift_Message('Hello Email'))
            ->setFrom('benabdallah.eya@esprit.tn')
            ->setTo('akkari.amir23@gmail.com')
            ->setBody(
                
                    "<p>Merci pour votre réservation Mr/Mme " .$reservation->getClient()." , le service ".$reservation->getService()." est maintenant réservé !</p>"
                ,
                'text/html'
            )
        ;
    
        $mailer->send($message);
            $this->addFlash("success_message","Reservation faite avec succés");
            return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
            "errors" => null
        ]);
    }

    // #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    // public function show(Reservation $reservation): Response
    // {
    //     return $this->render('reservation/show.html.twig', [
    //         'reservation' => $reservation,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, ReservationRepository $reservationRepository, ): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $reservationRepository->save($reservation, true);

            
            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
            "errors" => null
        ]);
    }

    #[Route('/delete/{id}', name: 'app_reservation_delete')]
    public function delete(Request $request, Reservation $reservation, ReservationRepository $reservationRepository): Response
    {
        
            $reservationRepository->remove($reservation, true);
        

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
