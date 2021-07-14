<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route('/register', name: 'security.registration')]
    public function registration(Request $request, EntityManagerInterface $em):Response     
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $factory = new PasswordHasherFactory([
            'hashpassword' => ['algorithm' => 'bcrypt'],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $factory->getPasswordHasher('hashpassword');
            $user->setPassword($hash->hash($user->getPassword()));
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Votre inscription a bien été validée!');

            return $this->redirectToRoute('security.login');
        }

        return $this->render("security/registration.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route('/connection', name:'security.login')]
    public function login()
    {
        return $this->render('security/login.html.twig');
    }

    #[Route('/logout', name:'security.logout')]
    public function logout(){}
}
