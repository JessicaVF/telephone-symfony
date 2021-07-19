<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
{
    /**
     * @Route("/auth", name="auth")
     */
    public function index(): Response
    {
        return $this->render('auth/index.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $requete, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $user= new User();
        $formRegister = $this->createForm(RegisterType::class, $user);
        $formRegister->handleRequest($requete);
        if($formRegister->isSubmitted() && $formRegister->isValid()){
            
            $hashedPassword = $hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('telephone');
        }
        return $this->render('auth/register.html.twig', ['formRegister'=> $formRegister->createView()]);

    }
    /**
     * @Route("/login", name="login")
     */
    public function login(): Response{

        return $this->render('auth/login.html.twig');
    }
    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): Response{
        return $this->redirectToRoute('telephone');
    }
}
