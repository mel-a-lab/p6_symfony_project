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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
                    $newFilename = $originalFilename . '-' . uniqid() . '.' . $image->guessExtension();

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
                $trickImageRepository->save($image, true);

            }
            $trickRepository->save($trick, true);

            $this->addFlash('success_trick_creation', 'Création de la figure réussi');
            return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app_trick_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Trick $trick, CommentRepository $commentRepository, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();

        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $comment->setUser($this->getUser());
                $comment->setProperTrick($trick);
                $comment->setDateCreated(new \DateTime());
                $comment->setDateUpdated(new \DateTime());
                $entityManager->persist($comment);
                $entityManager->flush();

                return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
            } else {
                $this->addFlash('error', 'Vous devez être connecté pour ajouter un commentaire.');
            }
        }

        $comments = $commentRepository->findBy(['proper_trick' => $trick]);

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'comments' => $comments,
            'commentForm' => $form->createView(),
        ]);

    }

    #[Route('/{slug}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trick $trick, TrickRepository $trickRepository, TrickImageRepository $trickImageRepository): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            foreach ($images as $image) {

                if ($image) {
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename . '-' . uniqid() . '.' . $image->guessExtension();

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
                $trickImageRepository->save($image, true);

            }
            $trickRepository->save($trick, true);

            return $this->redirectToRoute('app_trick_edit', ['slug' => $trick->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_trick_delete', methods: ['POST'])]
    public function delete(Request $request, Trick $trick, TrickRepository $trickRepository, UrlGeneratorInterface $urlGenerator): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
            $trickRepository->remove($trick, true);
            $this->addFlash('success', 'Le trick a été supprimé avec succès.');
        }

        return $this->redirectToRoute('home.index', [], Response::HTTP_SEE_OTHER);
    }

}