<?php

namespace App\Controller;

use App\Entity\Product;
use App\Exception\ExceptionInterface;
use App\Filter\ProductFilter;
use App\Form\ProductSearchType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
            $filter = new ProductFilter();
        }
        try {
            $products = $productRepository->findByProductFilter($filter);
        } catch (ExceptionInterface $e) {
            $this->addFlash('Error', $e->getMessage());
            $products = [];
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

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $dom): Response
    {
        return $this->render('dom/show.html.twig', [
            'dom' => $dom,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $dom, ProductRepository $domRepository): Response
    {
        return $this->render('dom/show.html.twig', [
            'dom' => $dom,
        ]);
    }
}
