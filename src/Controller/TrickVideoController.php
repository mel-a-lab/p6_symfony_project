<?php

namespace App\Controller;

use App\Entity\TrickVideo;
use App\Form\TrickVideoType;
use App\Repository\TrickVideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/trick-video')]
class TrickVideoController extends AbstractController
{
    #[Route('/', name: 'app_trick_video_index', methods: ['GET'])]
    public function index(TrickVideoRepository $trickVideoRepository): Response
    {
        return $this->render('trick_video/index.html.twig', [
            'trick_videos' => $trickVideoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_trick_video_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TrickVideoRepository $trickVideoRepository): Response
    {
        $trickVideo = new TrickVideo();
        $form = $this->createForm(TrickVideoType::class, $trickVideo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickVideoRepository->save($trickVideo, true);

            return $this->redirectToRoute('app_trick_video_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick_video/new.html.twig', [
            'trick_video' => $trickVideo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trick_video_show', methods: ['GET'])]
    public function show(TrickVideo $trickVideo): Response
    {
        return $this->render('trick_video/show.html.twig', [
            'trick_video' => $trickVideo,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_trick_video_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TrickVideo $trickVideo, TrickVideoRepository $trickVideoRepository): Response
    {
        $form = $this->createForm(TrickVideoType::class, $trickVideo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickVideoRepository->save($trickVideo, true);

            return $this->redirectToRoute('app_trick_video_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick_video/edit.html.twig', [
            'trick_video' => $trickVideo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trick_video_delete', methods: ['POST'])]
    public function delete(Request $request, TrickVideo $trickVideo, TrickVideoRepository $trickVideoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete-trick-video', $request->request->get('_token'))) {
            $trickVideoRepository->remove($trickVideo, true);
        }

        return $this->redirectToRoute('app_trick_edit', ['slug' => $trickVideo->getTrick()->getSlug()], Response::HTTP_SEE_OTHER);
    }
}
