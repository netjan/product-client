<?php

namespace App\Controller;

use App\Entity\Product;
use App\Exception\ExceptionInterface;
use App\Filter\ProductFilter;
use App\Form\ProductSearchType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
#[Route('/product')]
class ProductController extends AbstractController
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $filter = new ProductFilter();
        $form = $this->createForm(ProductSearchType::class, $filter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            $filter = new ProductFilter();
        }
        try {
            $products = $this->productRepository->findByProductFilter($filter);
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
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->productRepository->save($product);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(string $id): Response
    {
        $product = $this->find($id);

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, string $id): Response
    {
        $product = $this->find($id);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->productRepository->save($product);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, string $id): Response
    {
        if ($this->isCsrfTokenValid('app_product_delete'.$id, (string) $request->request->get('_token'))) {
            $this->productRepository->remove($id);
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @throws NotFoundHttpException
     */
    private function find(string $id): Product
    {
        try {
            $product = $this->productRepository->find($id);
        } catch (ExceptionInterface $e) {
            throw $this->createNotFoundException();
        }
        if (null === $product) {
            throw $this->createNotFoundException();
        }

        return $product;
    }
}
