<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Product;
use App\Form\SearchType;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    

    #[Route('/nos-produits', name: 'products')]

    public function index(ManagerRegistry $doctrine, Request $request,PaginatorInterface $paginator)

     { 
       
        // $products = $doctrine->getRepository(Product::class)->findAll();

        // $products = $paginator->paginate(
        //     $products,
        //     $request->query->getInt('page',1),
        //     6
        // );

        
    //    $form = $this->createForm(SearchAnnonceType::class);
    //     $searchAnnonce = $form->handleRequest($request);
    //     if($form->isSubmitted() && $form->isValid())
    //     {
    //         $products = $doctrine->getRepository($searchAnnonce->get('product')
    //         ->getData());

    //     }
        $products = $doctrine->getRepository(Product::class)->findAll();

        $search = new Search;
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {    
            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);
            // dd($search);

        }
        return $this->render("product/index.html.twig",[
            "products" => $products,
            // 'Search' => $Search->createView(),
            'form' => $form->createView()
            
        ]);
        

     }

    #[Route('/produit{slug}', name: 'product')]

    public function show($slug, ManagerRegistry $doctrine)
     {

        $product = $doctrine->getRepository(Product::class)->findOneBySlug($slug);
        $products =$this->entityManager->getRepository(Product::class)->findByIsBest(1);
       
        if(!$product){
            return $this->redirectToRoute('products');
        }
        return $this->render("product/show.html.twig",[
            "product" => $product,
            'products'=>$products
        ]);
       

     }

}
