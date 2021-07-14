<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    #[Route('/posts', name: 'post.index')]
    public function index(PostRepository $repository): Response
    {
        $posts = $repository->findAll();        
        
        return $this->render('post/index.html.twig', [
            "posts" => $posts
        ]);
    }

    #[Route('/post/{id}', name: 'post.show', requirements: ['id' => '\d+'])]
    /***
     * ##[Route('/post/{id}', name: 'post.show', requirements: ['id' => '\d+'])]
     * @Route("/post/{id}", name="post.show", requirements={"id": "\d+"})
     */
    public function show(Post $post, Request $request, EntityManagerInterface $em): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setPost($post)
                    ->setCreatedAt(new \DateTime());
            
            $em->persist($comment);
            $em->flush();
            
            $this->addFlash('success', 'Votre commentaire a été ajouté avec succès!');

            return $this->redirectToRoute('post.show', ["id" => $post->getId()]);
        }

        return $this->render('post/show.html.twig', [
            "post" => $post,
            "commentForm" => $form->createView()
        ]);
    }

    #[Route('/post/new', name: 'post.create')]
    #[Route('/post/{id}/edit', name: 'post.edit')]
    public function newOrEdit(Post $post = null, Request $request, EntityManagerInterface $manager): Response
    {
        if (!$post) {
            $post = new Post();
        }

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request); 
        
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$post->getId()) {
                $post->setCreatedAt(new \DateTime());
            }

            $manager->persist($post);
            
            $manager->flush();

            return $this->redirectToRoute('post.index');
        }

        return $this->render('post/create.html.twig', [
            'postForm' => $form->createView(),
            'editMode' => $post->getId() !== null
        ]);
    }

    #[Route('/post/{id}/delete', name: 'post.delete')]
    public function destroy(Post $post)
    {
        return $this->redirectToRoute('post.show', ['id' => $post->getId()]);
    }
}
