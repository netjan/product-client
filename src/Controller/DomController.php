<?php

namespace App\Controller;

use App\Entity\Dom;
use App\Form\DomType;
use App\Repository\DomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dom')]
class DomController extends AbstractController
{
    #[Route('/', name: 'app_dom_index', methods: ['GET'])]
    public function index(DomRepository $domRepository): Response
    {
        return $this->render('dom/index.html.twig', [
            'doms' => $domRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_dom_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DomRepository $domRepository): Response
    {
        $dom = new Dom();
        $form = $this->createForm(DomType::class, $dom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $domRepository->save($dom, true);

            return $this->redirectToRoute('app_dom_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dom/new.html.twig', [
            'dom' => $dom,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dom_show', methods: ['GET'])]
    public function show(Dom $dom): Response
    {
        return $this->render('dom/show.html.twig', [
            'dom' => $dom,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dom_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Dom $dom, DomRepository $domRepository): Response
    {
        $form = $this->createForm(DomType::class, $dom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $domRepository->save($dom, true);

            return $this->redirectToRoute('app_dom_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dom/edit.html.twig', [
            'dom' => $dom,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dom_delete', methods: ['POST'])]
    public function delete(Request $request, Dom $dom, DomRepository $domRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dom->getId(), $request->request->get('_token'))) {
            $domRepository->remove($dom, true);
        }

        return $this->redirectToRoute('app_dom_index', [], Response::HTTP_SEE_OTHER);
    }
}
