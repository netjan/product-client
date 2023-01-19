<?php

namespace App\Controller;

use App\Filter\ProductFilter;
use App\Form\ProductSearchType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $filter = new ProductFilter();
        $form = $this->createForm(ProductSearchType::class, $filter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            $products = $productRepository->findAll();

        } else {
            $products = $productRepository->findByProductFilter($filter);
        }

        return $this->render('product/index.html.twig', [
            'form' => $form,
            'products' => $products,
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => [],
        ]);
    }
}
