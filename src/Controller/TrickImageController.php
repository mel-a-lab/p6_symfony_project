<?php

namespace App\Controller;

use App\Entity\TrickImage;
use App\Form\TrickImageType;
use App\Repository\TrickImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/trick-image')]
class TrickImageController extends AbstractController
{
    #[Route('/', name: 'app_trick_image_index', methods: ['GET'])]
    public function index(TrickImageRepository $trickImageRepository): Response
    {
        return $this->render('trick_image/index.html.twig', [
            'trick_images' => $trickImageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_trick_image_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TrickImageRepository $trickImageRepository): Response
    {
        $trickImage = new TrickImage();
        $form = $this->createForm(TrickImageType::class, $trickImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickImageRepository->save($trickImage, true);

            return $this->redirectToRoute('app_trick_image_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick_image/new.html.twig', [
            'trick_image' => $trickImage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trick_image_show', methods: ['GET'])]
    public function show(TrickImage $trickImage): Response
    {
        return $this->render('trick_image/show.html.twig', [
            'trick_image' => $trickImage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_trick_image_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TrickImage $trickImage, TrickImageRepository $trickImageRepository): Response
    {
        $form = $this->createForm(TrickImageType::class, $trickImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickImageRepository->save($trickImage, true);

            return $this->redirectToRoute('app_trick_image_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick_image/edit.html.twig', [
            'trick_image' => $trickImage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trick_image_delete', methods: ['POST'])]
    public function delete(Request $request, TrickImage $trickImage, TrickImageRepository $trickImageRepository): Response
    {
        if ($this->isCsrfTokenValid('delete-trick-image', $request->request->get('_token'))) {
            $trickImageRepository->remove($trickImage, true);
        }

        return $this->redirectToRoute('app_trick_edit', ['slug' => $trickImage->getTrick()->getSlug()], Response::HTTP_SEE_OTHER);
    }
}
