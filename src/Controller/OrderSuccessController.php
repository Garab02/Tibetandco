<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/commande/merci/{stripeSessionId}', name: 'order_validate')]
    public function index($stripeSessionId, Cart $cart)
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);
         
        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }
         

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


      if (!$order->getIsPaid(0))
      {   
          //vider la session 'cart'
           $cart->remove();
      
           // modifier le statut IsPaid de la commande en mettant 1
          $order->setIsPaid(1);
          $this->entityManager->flush();

          //Envoyer un email à notre client pour lui confirmer sa commande
          $mail =new Mail();
          $content="Bonjour ".''.$order->getUser()->getFirstname()."<br/><br/>Votre commande numéro : ".$order->getReference()." a bien été validé".
          " <br/>Merci pour votre commande et à bientôt!.<br/><br/> 
          Vous pouvez vérifier votre facture  <a href='{{ path('order_validate')}}'><strong>ici</strong></a>";
          $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstname(),
          'Votre commande sur tibetandco est bien validée.',$content);
      }
    

        return $this->render('order_success/index.html.twig',[
            'order' => $order,
            "items" => $cartComplete,
            'cart' => $cart->get(),
            "total" => $total,
            
        ]);
    }
}
