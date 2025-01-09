<?php

namespace App\Controller;
use App\Entity\Medecin;
use App\Entity\RDV;
use App\Form\RDVType;

use App\Entity\Patient;
use App\Form\PatientType;
use Doctrine\ORM\EntityManagerInterface;  // Ajoutez cet import
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RDVRepository; // Ajoutez cet import
use App\Form\PatientProfileType;  // N'oubliez pas d'ajouter cet import
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;  // Voici le bon namespace




#[Route('/patient')]
class PatientController extends AbstractController
{
    // Affiche la liste des patients
    #[Route('/', name: 'patient_list', methods: ['GET'])]
    public function list(): Response
    {
        $patients = $this->getDoctrine()
            ->getRepository(Patient::class)
            ->findAll();

        return $this->render('patient/list.html.twig', [
            'patients' => $patients,
        ]);
    }


    // #[Route('/dashboard', name: 'patient_dashboard')]
    // public function dashboard(RDVRepository $rdvRepository): Response
    // {
    //     // Récupérer l'utilisateur connecté (le patient)
    //     $patient = $this->getUser();
        
    //     // Récupérer uniquement les rendez-vous du patient connecté
    //     $rendezVous = $rdvRepository->findBy(['patient' => $patient]);
    
    //     return $this->render('patient/dashboard.html.twig', [
    //         'message' => 'Bienvenue au tableau de bord du patient',
    //         'rendezVous' => $rendezVous,
    //     ]);
    // }


    #[Route('/compte', name: 'patient_compte', methods: ['GET', 'POST'])]
    public function monCompte(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager  // Ajoutez cette dépendance
    ): Response {
        $patient = $this->getUser();
        $form = $this->createForm(PatientProfileType::class, $patient);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Si un nouveau mot de passe est fourni, le hasher
                if ($password = $form->get('plainPassword')->getData()) {
                    $hashedPassword = $passwordHasher->hashPassword($patient, $password);
                    $patient->setPassword($hashedPassword);
                }

                // Persister les modifications
                $entityManager->flush();

                // Ajouter un message de succès
                $this->addFlash('success', 'Vos informations ont été mises à jour avec succès.');

                // Rediriger vers la même page pour rafraîchir les données
                return $this->redirectToRoute('patient_compte');
                
            } catch (\Exception $e) {
                // En cas d'erreur, ajouter un message d'erreur
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour de vos informations.');
            }
        }

        return $this->render('patient/compte.html.twig', [
            'patient' => $patient,
            'form' => $form->createView(),
        ]);
    }

    // Affiche le formulaire pour ajouter un patient
    #[Route('/new', name: 'patient_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $patient = new Patient();
        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer le format de la date de naissance si nécessaire
            $dateNaissance = $patient->getDateNaissance();
            if ($dateNaissance instanceof \DateTime) {
                $patient->setDateNaissance($dateNaissance->format('Y-m-d'));
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($patient);
            $entityManager->flush();

            return $this->redirectToRoute('patient_list');
        }

        return $this->render('patient/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/dashboard', name: 'patient_dashboard')]
public function dashboard(EntityManagerInterface $em): Response
{
    $medecins = $em->getRepository(Medecin::class)->findAll();
    $rdvs = $em->getRepository(RDV::class)->findBy(['patient' => $this->getUser()]);
    
    return $this->render('patient/dashboard.html.twig', [
        'medecins' => $medecins,
        'rdvs' => $rdvs
    ]);
}

#[Route('/medecin/{id}', name: 'patient_voir_medecin')]
public function voirMedecin(Medecin $medecin): Response
{
    return $this->render('patient/voir_medecin.html.twig', [
        'medecin' => $medecin
    ]);
}

// eya eya eya 

#[Route('/rdv', name: 'patient_mes_rdv')]
public function mesRDV(EntityManagerInterface $em): Response
{
  //  $medecins = $em->getRepository(Medecin::class)->findAll();
    $rdvs = $em->getRepository(RDV::class)->findBy(['patient' => $this->getUser()]);
 
    return $this->render('patient/mes_rdv.html.twig', [
     //   'medecins' => $medecins,
       'rdvs' => $rdvs
    ]);
}




#[Route('/rdv/new/{id}', name: 'patient_rdv_new')]
public function nouveauRdv(Request $request, Medecin $medecin, EntityManagerInterface $em): Response
{
    $rdv = new RDV();
    $rdv->setMedecin($medecin);
    $rdv->setPatient($this->getUser());
    $rdv->setStatut('En attente');

    $form = $this->createForm(RDVType::class, $rdv);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($rdv);
        $em->flush();
        
        $this->addFlash('success', 'Rendez-vous pris avec succès');
        return $this->redirectToRoute('patient_dashboard');
    }

    return $this->render('patient/prendre_rdv.html.twig', [
        'form' => $form->createView(),
        'medecin' => $medecin
    ]);
}

#[Route('/rdv/{id}/modifier', name: 'patient_rdv_modifier')]
public function modifierRdv(Request $request, RDV $rdv, EntityManagerInterface $em): Response
{
    if ($rdv->getPatient() !== $this->getUser()) {
        throw $this->createAccessDeniedException();
    }

    $form = $this->createForm(RDVType::class, $rdv);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();
        $this->addFlash('success', 'Rendez-vous modifié');
        return $this->redirectToRoute('patient_mes_rdv');
    }

    return $this->render('patient/modifier_rdv.html.twig', [
        'form' => $form->createView(),
        'rdv' => $rdv
    ]);
}

#[Route('/rdv/{id}/annuler', name: 'patient_rdv_annuler')]
public function annulerRdv(RDV $rdv, EntityManagerInterface $em): Response
{
    if ($rdv->getPatient() !== $this->getUser()) {
        throw $this->createAccessDeniedException();
    }

    $rdv->setStatut('Annulé');
    $em->flush();

    $this->addFlash('success', 'Rendez-vous annulé');
    return $this->redirectToRoute('patient_dashboard');
}



}
