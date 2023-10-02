<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


class StripeController extends AbstractController
{
  
    #[Route('/create-session/{reference}', name: 'stripe_create_session')]

    public function index( EntityManagerInterface $entityManager, Cart $cart, $reference)
    {
        $products_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';
         
        $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);
         
        // dd($order);
        if(!$order){
          new JsonResponse(['error'=>'order']);
        }
        // dd($order->getOrderDetails()->getValues());

        foreach($order->getOrderDetails()->getValues() as $product)
        {
          $product_object = $entityManager->getRepository(Product::class)->findOneByName($product->getProduct());
            $products_stripe[] = [
                'price_data' => [
                  'currency' => 'eur',
                  'product_data' => [
                    'name' => $product->getProduct(),
                    'images' => [$YOUR_DOMAIN."/uploads/".$product_object->getIllustration()]
                  ],
                  'unit_amount' => $product->getPrice(),
                ],
                'quantity' => $product->getQuantity(),
              ];
        }

        $products_stripe[] = [
          'price_data' => [
            'currency' => 'eur',
            'product_data' => [
              'name' => $order->getCarrierName(),
              'images' => [$YOUR_DOMAIN]
            ],
            'unit_amount' => $order->getCarrierPrice(),
          ],
          'quantity' => 1,
        ];
        
        // dd($products_stripe);

        Stripe::setApiKey('sk_test_51NBHcEIryeQax2zwR9tteGn2j4AB3Z3MD8zZEEy9iIRWqJoPKxUyGAyBsLSoVJxnX4XoBPnvsSxaD4hhiMGLNmau00fMwmC2Rg');

        $checkout_session = session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [[
              $products_stripe
            ]],
            
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
          ]);
    //     dump($checkout_session->id);
    //    dd($checkout_session);
        $order->setStripeSessionId($checkout_session->id);

        $entityManager->flush();

          return $this->redirect($checkout_session->url, 303);
         $response = new JsonResponse(['id'=> $checkout_session->id]);
         return $response;
     }

    //  /**
    //   * @Route("/success", name="success_url")
    //   */

    //   public function successUrl()
    //   {
    //     return $this->render("order/success.html.twig");
    //   }

    //   /**
    //    * @Route("/cancel", name="cancel_url")
    //    */

    //    public function cancelUrl()
    //   {
    //     return $this->render("payment/cancel.html.twig");
    //   }
    
}
