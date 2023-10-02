<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountOrderController extends AbstractController
{   
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)

    {
        $this->entityManager= $entityManager;
    }
    #[Route('/compte/mes-commandes', name: 'account_order')]
    public function index(): Response
    {
        $orders=$this->entityManager->getRepository(Order::class)->findSuccessOrders($this->getUser());
        return $this->render('account/order.html.twig',[
            'orders'=> $orders
        ]);

    }
    #[Route('/compte/mes-commandes/{reference}', name: 'account_order_show')]
    public function show($reference, Cart $cart): Response
    {
        $order=$this->entityManager->getRepository(Order::class)->findOneByReference($reference);
        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('account_order');
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
     
        return $this->render('account/order_show.html.twig',[
            'order'=> $order,
            'total' => $total,
        ]);

    }
}