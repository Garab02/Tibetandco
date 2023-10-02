<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/compte', name: 'account')]
    
    public function index(): Response
    {
       


$Hour = date('G');
$message = '';
if ( $Hour >= 00 && $Hour <= 11 ) {
    $message =  "Bonjour|Good Morning";
} else if ( $Hour >= 12 && $Hour <= 18 ) {
    $message =  "Bonjour|Good Afternoon";
} else if ( $Hour >= 19 || $Hour <= 00 ) {
    $message =  "Bonsoir|Good Evening";
}
        return $this->render('account/index.html.twig',[
            'hour' => $Hour,
            'message' => $message
        ]);
    }
}
