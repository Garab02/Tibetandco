<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAddressController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/adresses', name: 'account_address')]
    public function index(): Response
    {
        return $this->render('account/address.html.twig');
    }


    #[Route('/ajouter-une-adresse', name: 'account_address_add')]
    public function add(Request $request,ManagerRegistry $doctrine, Cart $cart): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            $entityManager = $doctrine->getManager();

            $entityManager->persist($address);
            $entityManager->flush();
            if($cart->get()){
                return $this->redirectToRoute('order');
            }else{
                return $this->redirectToRoute('account_address');
            }
        }
        return $this->render('account/address_add.html.twig',[
            'form' => $form->createView()
        ]);
    }
    #[Route('/modifier-une-adresse{id}', name: 'account_address_edit')]
    public function edit(Request $request,ManagerRegistry $doctrine, $id): Response
    {
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if(!$address || $address->getUser() != $this->getUser()){
            return $this->redirectToRoute('account_address');
        }
        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
          
            $entityManager = $doctrine->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('account_address');
        }
        return $this->render('account/address_add.html.twig',[
            'form' => $form->createView()
        ]);
    }
    #[Route('/suprimer-une-adresse{id}', name: 'account_address_delete')]
    public function delete($id): Response
    {
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if(!$address || $address->getUser() == $this->getUser()){
           
            $this->entityManager->remove($address);
            $this->entityManager->flush();
        }
            return $this->redirectToRoute('account_address');
        
        
    }
}
