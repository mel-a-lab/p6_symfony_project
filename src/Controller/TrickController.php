<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\TrickType;
use App\Entity\TrickImage;
use App\Form\CommentFormType;
use App\Repository\TrickRepository;
use App\Repository\CommentRepository;
use App\Repository\TrickImageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/trick')]
class TrickController extends AbstractController
{
    #[Route('/', name: 'app_trick_index', methods: ['GET'])]
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('trick/index.html.twig', [
            'tricks' => $trickRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_trick_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TrickRepository $trickRepository, TrickImageRepository $trickImageRepository): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            foreach ($images as $image) {

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$image->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
            }
            $image = new TrickImage();
            $image->setImagePath($newFilename);
            $image->setTrick($trick);
            $trickRepository->save($trick, true);
            $trickImageRepository->save($image, true);
        }
            
            return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app_trick_show', methods: ['GET'])]
    public function show(Request $request,Trick $trick, CommentRepository $commentRepository): Response
    {
        //comment part
        // create comment
        $comment = new Comment;

        //generate form
        $commentForm = $this->createform(CommentFormType::class, $comment);
        $commentForm->handleRequest($request);

        //get comments
        $comments = $commentRepository->findBy(['proper_trick' => $trick]);

        //form processing

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'comments' => $comments,
            'commentform' => $commentForm->createView()
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickRepository->save($trick, true);

            return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trick_delete', methods: ['POST'])]
    public function delete(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            $trickRepository->remove($trick, true);
        }

        return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
    }
}
