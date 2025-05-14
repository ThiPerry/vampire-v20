<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route(path: '/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): Response {
        // 1) on crée un nouvel User
        $user = new User();
        $form = $this->createForm(RegistrationForm::class, $user);

        // 2) on hydrate avec les données POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $user->setCreatedAt(new \DateTimeImmutable());
            // 3) hash du mot de passe
            $plainPwd = $form->get('plainPassword')->getData();
            $hashedPwd = $passwordHasher->hashPassword($user, $plainPwd);
            $user->setPassword($hashedPwd);

            // 4) on persiste et flush
            $em->persist($user);
            $em->flush();

            // 5) on redirige vers la page de login
            $this->addFlash('success', 'Votre compte a bien été créé ! Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        // 6) on affiche le formulaire
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
