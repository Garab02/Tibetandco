<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Carrier;
use App\Entity\Product;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/mon-panier', name: 'cart')]
    public function index(Cart $cart)
    {
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
     

      
        return $this->render('cart/index.html.twig',[
            'cart' => $cart->get(),
            "items" => $cartComplete,
            "total" => $total,
        
        ]);
    }

    #[Route('/cart/add/{id}', name: 'add_to_cart')]
    public function add( Cart $cart, $id): Response
    {
        $cart->add($id);
      
        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/remove', name: 'remove_my_cart')]
    public function remove( Cart $cart): Response
    {
        $cart->remove();
      
        return $this->redirectToRoute('products');
    }
    #[Route('/cart/delete{id}', name: 'delete_to_cart')]
    public function detele( Cart $cart, $id): Response
    {
        $cart->delete($id);
      
        return $this->redirectToRoute('cart');
    }
    #[Route('/cart/decrease{id}', name: 'decrease_to_cart')]
    public function decrease( Cart $cart, $id): Response
    {
        $cart->decrease($id);
      
        return $this->redirectToRoute('cart');
    }

    
}
