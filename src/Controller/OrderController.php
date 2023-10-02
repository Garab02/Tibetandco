<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Entity\Product;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class OrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande', name: 'order')]
    public function index(Cart $cart, Request $request, ManagerRegistry $doctrine): Response
    {
        // dd($this->getUser()->getAddresses()->getValues());
        if(!$this->getUser()->getAddresses()->getValues())
        {
            return $this->redirectToRoute('account_address_add');
        }
       $form = $this->createForm(OrderType::class, null, [
        'user' => $this->getUser()
       ]);

       $cartComplete = [];

        if ($cart->get()) {
            foreach($cart->get() as $id => $quantity){
                $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id);
                if(!$product_object){
                    // $this->delete($id);
                }
                $cartComplete[] = [
                    'product' => $product_object,
                    'quantity' => $quantity
                ];
            }
        }
        
        
        // dd($cartComplete);
        $total = 0;
      foreach($cartComplete as $id => $value)
      {
        $total += $value['product']->getPrice() * $value['quantity'];
      }
      
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull(),
            'total' => $total,
        ]);
    }
   
    
    #[Route('/recapitulatif', name: 'recap', methods:'POST')]
    public function add(Cart $cart, Request $request): Response
    {
       $form = $this->createForm(OrderType::class, null, [
        'user' => $this->getUser()
       ]);
       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid()) 
       {
         $date = new \DateTime();
         $carriers = $form->get('carriers')->getData();
         $delivery = $form->get('addresses')->getData();
         $delivery_content = $delivery->getFirstname().''.$delivery->getLastname();
         $delivery_content .='<br/>'.$delivery->getPhone();

         if($delivery->getCompany())
         {
             $delivery_content .='<br/>'.$delivery->getCompany();
         }
         $delivery_content .='<br/>'.$delivery->getAddress();
         $delivery_content .='<br/>'.$delivery->getPostal().''.$delivery->getCity();
         $delivery_content .='<br/>'.$delivery->getCountry();
         
     
        //  dd($delivery_content);
     
         //enregistrer ma commande Order()
         $order = new Order();
         $reference = $date->format('dmY').'-'.uniqid();
         $order->setReference($reference);
         $order->setUser($this->getUser());
         $order->setCreatedAt($date);
         $order->setCarrierName($carriers->getName());
         $order->setCarrierPrice($carriers->getPrice());
         $order->setDelivery($delivery_content);
         $order->setIsPaid(0);
        //  $order->setState(0);
        
         $this->entityManager->persist($order);


      
        //enregistrer mes produits orderDetails()
         foreach($cart->getFull() as $product) {
             $orderDetails = new OrderDetails();
             $orderDetails->setMyOrder($order);
             $orderDetails->setProduct($product['product']->getName());
             $orderDetails->setQuantity($product['quantity']);
             $orderDetails->setPrice($product['product']->getPrice());
             $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
            //  dd($product);
             $this->entityManager->persist($orderDetails);


         }
        //    dd($order);
          $this->entityManager->flush();
          
      

        
        //dump($checkout_session->id);
        //dd($checkout_session);
     
         

          $cartComplete = [];

          if ($cart->get()) {
              foreach($cart->get() as $id => $quantity){
                  $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id);
                  if(!$product_object){
                      // $this->delete($id);
                  }
                  $cartComplete[] = [
                      'product' => $product_object,
                      'quantity' => $quantity
                  ];
              }
          }
          
          
          // dd($cartComplete);
          $total = 0;
        foreach($cartComplete as $id => $value)
        {
          $total += $value['product']->getPrice() * $value['quantity'];
        }
       
          return $this->render('order/add.html.twig', [
              'cart' => $cart->getFull(),
              'total' => $total,
              'carrier' => $carriers,
              'delivery' => $delivery_content, 
              'reference' => $order->getReference() 
          ]);
       }
       return $this->redirectToRoute('cart');
    }
}
