<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountPasswordController extends AbstractController
{
    #[Route('/modifier-mon-mot-de-passe', name: 'account_password')]
    public function index(Request $request,EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $notification = null;
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);
        
        $form ->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
               $old_pwd = $form->get('old_password')->getData();
              
               if($userPasswordHasher->isPasswordValid($user, $old_pwd)) {
                $new_pwd = $form->get('new_password')->getData();
                $password = $userPasswordHasher->hashPassword($user, $new_pwd);
                $user->setPassword($password);
                $entityManager->flush();
                $notification = " Votre mot de passe a bien été mis à jour";
               } else {
                $notification = "Votre momt de passe actuel n'est pas le bon";
               }
        }
        return $this->render('account/password.html.twig',[
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
