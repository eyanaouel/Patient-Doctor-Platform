<?php

namespace App\Controller;

use App\Entity\Medecin;
use Doctrine\ORM\EntityManagerInterface;  
use App\Form\MedecinType;
use App\Repository\MedecinRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RDVRepository; 
use App\Form\MedecinProfileType;  
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; 
use App\Entity\HistoriqueMedical;
use App\Entity\Patient;


#[Route('/medecin')]
class MedecinController extends AbstractController
{
    // Affiche la liste des médecins
    #[Route('/', name: 'medecin_index', methods: ['GET'])]
    public function index(MedecinRepository $medecinRepository): Response
    {
        $medecins = $medecinRepository->findAll();

        return $this->render('medecin/index.html.twig', [
            'medecins' => $medecins,
        ]);
    }

    
    #[Route('/dashboard', name: 'medecin_dashboard')]
public function dashboard(RDVRepository $rdvRepository): Response
{
    
    $medecin = $this->getUser();
    
    $rendezVous = $rdvRepository->findBy(['medecin' => $medecin]);

    return $this->render('medecin/dashboard.html.twig', [
        'rendezVous' => $rendezVous
    ]);
}


    #[Route('/patient/{id}', name: 'patient_details')]
public function patientDetails($id, EntityManagerInterface $em): Response
{
    $patient = $em->getRepository(Patient::class)->find($id);
    $historique = $em->getRepository(HistoriqueMedical::class)->findBy(
        ['patient' => $patient],
        ['date' => 'DESC']
    );
    
    return $this->render('medecin/patient_details.html.twig', [
        'patient' => $patient,
        'historique' => $historique
    ]);
}

    //route pour voir la page mon compte

    #[Route('/compte', name: 'medecin_compte', methods: ['GET', 'POST'])]
    public function monCompte(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager  // Ajoutez cette dépendance
    ): Response {
        $medecin = $this->getUser();
        $form = $this->createForm(MedecinProfileType::class, $medecin);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Si un nouveau mot de passe est fourni, le hasher
                if ($password = $form->get('plainPassword')->getData()) {
                    $hashedPassword = $passwordHasher->hashPassword($medecin, $password);
                    $medecin->setPassword($hashedPassword);
                }

                // Persister les modifications
                $entityManager->flush();

                // Ajouter un message de succès
                $this->addFlash('success', 'Vos informations ont été mises à jour avec succès.');

                // Rediriger vers la même page pour rafraîchir les données
                return $this->redirectToRoute('medecin_compte');
                
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour de vos informations.');
            }
            try {
                // Debug des disponibilités
                dump($medecin->getDisponibilite());
                
                $entityManager->flush();
                $this->addFlash('success', 'Vos informations ont été mises à jour avec succès.');
                
                return $this->redirectToRoute('medecin_compte');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
            }

        }

        return $this->render('medecin/compte.html.twig', [
            'medecin' => $medecin,
            'form' => $form->createView(),
        ]);
    }
        // Affiche le formulaire pour ajouter un médecin
        #[Route('/new', name: 'medecin_new', methods: ['GET', 'POST'])]
        public function new(Request $request): Response
        {
            $medecin = new Medecin();
            $form = $this->createForm(MedecinType::class, $medecin);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($medecin);
                $entityManager->flush();
    
                return $this->redirectToRoute('medecin_index');
            }
    
            return $this->render('medecin/new.html.twig', [
                'form' => $form->createView(),
            ]);
    }

    // Affiche le formulaire pour modifier un médecin existant
    #[Route('/{id}/edit', name: 'medecin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Medecin $medecin): Response
    {
        $form = $this->createForm(MedecinType::class, $medecin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('medecin_index');
        }

        return $this->render('medecin/edit.html.twig', [
            'form' => $form->createView(),
            'medecin' => $medecin,
        ]);
    }

    // Supprime un médecin
    #[Route('/{id}/delete', name: 'medecin_delete', methods: ['POST'])]
    public function delete(Request $request, Medecin $medecin): Response
    {
        if ($this->isCsrfTokenValid('delete' . $medecin->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($medecin);
            $entityManager->flush();
        }

        return $this->redirectToRoute('medecin_index');
    }


    //voir dossier medical du patient !
#[Route('/dossier-patient/{id}', name: 'medecin_dossier_patient')]
public function dossierPatientWithMedecin(
    Patient $patient, 
    EntityManagerInterface $em
): Response {
    $historiques = $em->getRepository(HistoriqueMedical::class)->findBy([
        'patient' => $patient,
        'medecin' => $this->getUser()
    ]);

    return $this->render('medecin/dossier_patient.html.twig', [
        'patient' => $patient,
        'historiques' => $historiques
    ]);
}


// //////
#[Route('/patient/{id}/dossier', name: 'medecin_dossier_patient')]
    public function DossierPatient(Patient $patient): Response
    {
        $historiques = $patient->getHistoriquesMedicales();
        
        return $this->render('medecin/dossier_patient.html.twig', [
            'patient' => $patient,
            'historiques' => $historiques
        ]);
    }

    #[Route('/historique/{id}/documents', name: 'historique_add_documents', methods: ['POST'])]
    public function addDocuments(Request $request, HistoriqueMedical $historique, EntityManagerInterface $entityManager): Response
    {
        $uploadedFiles = $request->files->get('documents');
        
        if ($uploadedFiles) {
            $uploadDir = $this->getParameter('documents_directory');
            $documents = $historique->getDocuments() ?? [];

            foreach ($uploadedFiles as $file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($uploadDir, $fileName);
                $documents[] = $fileName;
            }
            
            $historique->setDocuments($documents);
            $entityManager->flush();
        }

        return $this->redirectToRoute('medecin_dossier_patient', [
            'id' => $historique->getPatient()->getId()
        ]);
    }

    #[Route('/patient/{id}/creer-historique', name: 'medecin_creer_historique', methods: ['POST'])]
public function creerHistorique(
    Patient $patient, 
    RDVRepository $rdvRepository,
    EntityManagerInterface $em
): Response {
    // Création d'un nouvel historique médical vide
    $nouveauHistorique = new HistoriqueMedical();
    $nouveauHistorique->setPatient($patient);
    $nouveauHistorique->setMedecin($this->getUser());
    $nouveauHistorique->setDate(new \DateTime());
    $nouveauHistorique->setDiagnostic('');
    $nouveauHistorique->setPrescription('');
    $nouveauHistorique->setNotes('');

    $em->persist($nouveauHistorique);
    $em->flush();

    // Redirection vers la page du dossier
    return $this->redirectToRoute('medecin_dossier_patient', [
        'id' => $patient->getId()
    ]);

       // Mettre à jour le statut du dernier rendez-vous
       $dernierRdv = $rdvRepository->findOneBy(
        ['patient' => $patient, 'medecin' => $this->getUser(), 'statut' => 'en_attente'],
        ['dateHeure' => 'DESC']
    );

    if ($dernierRdv) {
        $dernierRdv->setStatut('termine');
    }

    $em->flush();

    return $this->redirectToRoute('medecin_dossier_patient', [
        'id' => $patient->getId()
    ]);
}

/* #[Route('/patient/{id}/enregistrer-consultation', name: 'medecin_enregistrer_consultation', methods: ['POST'])]
public function enregistrerConsultation(
    Request $request, 
    Patient $patient, 
    EntityManagerInterface $em,
    RDVRepository $rdvRepository  // Ajoutez ce paramètre

): Response {
    $diagnostic = $request->request->get('diagnostic');
    $prescription = $request->request->get('prescription');
    $notes = $request->request->get('notes');
    $files = $request->files->get('documents');

    // Création d'une nouvelle consultation
    $consultation = new HistoriqueMedical();
    $consultation->setPatient($patient);
    $consultation->setMedecin($this->getUser());
    $consultation->setDate(new \DateTime());
    $consultation->setDiagnostic($diagnostic);
    $consultation->setPrescription($prescription);
    $consultation->setNotes($notes);

    // Gestion des fichiers uploadés
    if ($files) {
        $uploadedFiles = [];
        foreach ($files as $file) {
            $newFilename = uniqid() . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('documents_directory'), // Répertoire de stockage
                $newFilename
            );
            $uploadedFiles[] = $newFilename;
        }
        $consultation->setDocuments($uploadedFiles); // Stocker les noms des fichiers
    }

    $em->persist($consultation);
    $em->flush();

    // Redirection pour rafraîchir la page
    return $this->redirectToRoute('medecin_dossier_patient', [
        'id' => $patient->getId()
    ]);
} */

#[Route('/patient/{id}/enregistrer-consultation', name: 'medecin_enregistrer_consultation', methods: ['POST'])]
public function enregistrerConsultation(
    Request $request, 
    Patient $patient, 
    EntityManagerInterface $em,
    RDVRepository $rdvRepository
): Response {
    $diagnostic = $request->request->get('diagnostic');
    $prescription = $request->request->get('prescription');
    $notes = $request->request->get('notes');
    $files = $request->files->get('documents');

    // Création d'une nouvelle consultation
    $consultation = new HistoriqueMedical();
    $consultation->setPatient($patient);
    $consultation->setMedecin($this->getUser());
    $consultation->setDate(new \DateTime()); // Date de la consultation
    $consultation->setDiagnostic($diagnostic);
    $consultation->setPrescription($prescription);
    $consultation->setNotes($notes);

    // Gérer les documents
    if ($files) {
        $uploadDir = $this->getParameter('documents_directory');
        $documents = [];

        foreach ($files as $file) {
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($uploadDir, $fileName);
            $documents[] = $fileName;
        }

        $consultation->setDocuments($documents);
    }

    // Persister la consultation
    $em->persist($consultation);

    // Vérifier le rendez-vous du jour
    $dateAujourdhui = new \DateTime();
    $rendezVous = $rdvRepository->findOneBy([
        'patient' => $patient,
        'medecin' => $this->getUser(),
        'dateHeure' => $dateAujourdhui,
        'statut' => 'en_attente'
    ]);

    // Si un rendez-vous est trouvé, mettre à jour son statut
    if ($rendezVous) {
        $rendezVous->setStatut('termine');
    }

    // Enregistrer dans la base de données
    $em->flush();

    // Rediriger vers le dossier du patient
    return $this->redirectToRoute('medecin_dossier_patient', [
        'id' => $patient->getId()
    ]);
}


//modifier  un diagnostic
#[Route('/consultation/{id}/modifier', name: 'medecin_modifier_consultation')]
public function modifierConsultation(
    Request $request, 
    HistoriqueMedical $historique, 
    EntityManagerInterface $em
): Response {
    if ($request->isMethod('POST')) {
        $historique->setDiagnostic($request->request->get('diagnostic'));
        $historique->setPrescription($request->request->get('prescription'));
        $historique->setNotes($request->request->get('notes'));

        // Gestion des fichiers uploadés (ajout des nouveaux fichiers sans effacer les anciens)
        $files = $request->files->get('documents');
        if ($files) {
            $uploadedFiles = $historique->getDocuments();
            foreach ($files as $file) {
                $newFilename = uniqid() . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('documents_directory'),
                    $newFilename
                );
                $uploadedFiles[] = $newFilename;
            }
            $historique->setDocuments($uploadedFiles);
        }

        $em->flush();

        return $this->redirectToRoute('medecin_dossier_patient', ['id' => $historique->getPatient()->getId()]);
    }

    return $this->render('medecin/modifier_consultation.html.twig', [
        'historique' => $historique,
    ]);
}

//supprimer  un diagnostic

#[Route('/consultation/{id}/supprimer', name: 'medecin_supprimer_consultation')]
public function supprimerConsultation(
    HistoriqueMedical $historique, 
    EntityManagerInterface $em
): Response {
    $patientId = $historique->getPatient()->getId();

    // Supprimer les fichiers liés
    $documents = $historique->getDocuments();
    foreach ($documents as $document) {
        $filePath = $this->getParameter('documents_directory') . '/' . $document;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $em->remove($historique);
    $em->flush();

    return $this->redirectToRoute('medecin_dossier_patient', ['id' => $patientId]);
}


}
