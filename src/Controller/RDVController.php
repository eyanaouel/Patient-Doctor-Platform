<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RDVController extends AbstractController
{
    #[Route('/r/d/v', name: 'app_r_d_v')]
    public function index(): Response
    {
        return $this->render('rdv/index.html.twig', [
            'controller_name' => 'RDVController',
        ]);
    }


    //lorsqu un patient annule un rdv , il s annule automatiquemnt du dashboard medecin
    #[Route('/patient/{id}/annuler-rdv/{rdvId}', name: 'patient_annuler_rdv')]
public function annulerRendezVous(
    int $rdvId, 
    RDVRepository $rdvRepository, 
    EntityManagerInterface $em
): Response {
    $rendezVous = $rdvRepository->find($rdvId);

    if ($rendezVous && $rendezVous->getPatient()->getId() === $this->getUser()->getId()) {
        $em->remove($rendezVous);
        $em->flush();
        $this->addFlash('success', 'Votre rendez-vous a été annulé.');
    } else {
        $this->addFlash('error', 'Rendez-vous introuvable ou non autorisé.');
    }

    return $this->redirectToRoute('patient_dashboard');
}

}
