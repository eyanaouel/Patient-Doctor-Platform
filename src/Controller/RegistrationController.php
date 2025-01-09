<?php

namespace App\Controller;

use App\Entity\Medecin;
use App\Entity\Patient;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register/{type}', name: 'app_register', requirements: ['type' => 'medecin|patient'])]
    public function register(
        string $type,
        Request $request, 
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        // Crée un nouvel utilisateur selon le type
        $user = match ($type) {
            'medecin' => new Medecin(),
            'patient' => new Patient(),
            default => throw $this->createNotFoundException('Type invalide'),
        };

        // Ajoute l'option `is_medecin` pour adapter le formulaire
        $form = $this->createForm(RegistrationType::class, $user, [
            'is_medecin' => $type === 'medecin',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hashage du mot de passe
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            );
            $user->setPassword($hashedPassword);

            // Définir le rôle selon le type d'utilisateur
            $role = match ($type) {
                'medecin' => 'ROLE_MEDECIN',
                'patient' => 'ROLE_PATIENT',
            };
            $user->setRoles([$role]);


             // Si c'est un patient, on s'assure que la date est bien un objet DateTime
        if ($type === 'patient' && $form->has('dateNaissance')) {
            $dateNaissance = $form->get('dateNaissance')->getData();
            if ($dateNaissance instanceof \DateTimeInterface) {
                $user->setDateNaissance($dateNaissance);
            }
        }

            // Persister l'utilisateur en base
            $entityManager->persist($user);
            $entityManager->flush();

            // Message de succès et redirection
            $this->addFlash('success', 'Votre compte a été créé avec succès.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
            'type' => $type,
        ]);
    }
}
