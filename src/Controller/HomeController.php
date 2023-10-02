<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
   /**
    * @Route("/", name="home")
    */
    public function index(ManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
    {
        $products =$this->entityManager->getRepository(Product::class)->findByIsBest(1);
        // dd($products);
        // $headers =$this->entityManager->getRepository(Header::class)->findAll();
         return $this->render('home/index.html.twig',[
             'products' => $products,
            //  'headers' => $headers
         ]);
    }

    /**
    * @Route("/about", name="about")
    */
    public function about(): Response
    {
        
        return $this->render('home/about.html.twig');
    }
}
