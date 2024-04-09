<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy([], ['datePost' => 'DESC']);

        return $this->render('blog/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/blog/page/{page}', name: 'app_blog_page', methods: ['GET'])]
    public function page(int $page, PostRepository $postRepository): Response
    {
        $postsPerPage = 5;
        $offset = ($page - 1) * $postsPerPage;

        $posts = $postRepository->findBy([], ['datePost' => 'DESC'], $postsPerPage, $offset);

        $postsArray = array_map(function ($post) {
            return [
                'id' => $post->getId(),
                'caption' => $post->getCaption(),
                'image' => $post->getImage(),
                'datePost' => $post->getDatePost()->format('Y-m-d H:i:s'),
            ];
        }, $posts);

        return new JsonResponse(['posts' => $postsArray]);
    }

    #[Route('/blog/count', name: 'app_blog_count', methods: ['GET'])]
    public function count(PostRepository $postRepository): Response
    {
        $count = $postRepository->count([]);

        return new JsonResponse(['count' => $count]);
    }

    #[Route('/newPost', name: 'app_blog_new', methods: ['GET', 'POST'])]
    public function new(Request $req, ManagerRegistry $doc): Response
    {
        if ($req->isXmlHttpRequest()) {
            $post = new Post();
            $caption = $req->get('caption');
            $fichierImage = $req->files->get('image');

            if ((empty($caption) && empty($fichierImage)) || (ctype_space($caption) && empty($fichierImage))) {
                return new JsonResponse(['success' => false, 'message' => 'Le caption ne peut pas être vide si aucune image n\'est fournie, et vice versa.']);
            }

            $post->setCaption($caption);
            $post->setImageFile($fichierImage);
            $post->setDatePost(new DateTime());

            $em = $doc->getManager();
            $em->persist($post);
            $em->flush();
            return new JsonResponse([
                'success' => true,
                'post' => [
                    'id' => $post->getId(),
                    'caption' => $post->getCaption(),
                    'image' => $post->getImage(),
                    'datePost' => $post->getDatePost()->format('Y-m-d H:i:s'),
                ]
            ]);
        }
        return $this->redirectToRoute('app_blog');
    }


    #[Route('/blog/{id}', name: 'app_blog_delete', methods: ['DELETE'])]
    public function delete(ManagerRegistry $doctrine, $id, PostRepository $postRepository, Request $req): Response
    {
        if ($req->isXmlHttpRequest()) {
            $auteur = $postRepository->find($id);
            $em = $doctrine->getManager();
            $em->remove($auteur);
            $em->flush();
            return new Response('Post supprimé avec succès', Response::HTTP_OK);
        }
        return $this->redirectToRoute('app_blog');
    }

    #[Route('/edit/{id}', name: 'app_blog_update', methods: ['POST'])]
    public function update(ManagerRegistry $doctrine, $id, Request $req): Response
    {
        $post = $doctrine->getRepository(Post::class)->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Le post d\'id ' . $id . ' n\'a pas été trouvé.');
        }
        $caption = $req->get('caption');
        $fichierImage = $req->files->get('image');

        $post->setDatePost(new DateTime());
        $post->setCaption($caption);
        $post->setImageFile($fichierImage);

        if ((empty($caption) && empty($fichierImage)) || (ctype_space($caption) && empty($fichierImage))) {
            return new JsonResponse(['success' => false, 'message' => 'Le caption ne peut pas être vide si aucune image n\'est fournie, et vice versa.']);
        }

        $em = $doctrine->getManager();
        $em->persist($post);
        $em->flush();

        $this->addFlash('success', 'Le post a bien été modifié.');

        return new JsonResponse([
            'success' => true,
            'post' => [
                'id' => $post->getId(),
                'caption' => $post->getCaption(),
                'image' => $post->getImage(),
                'datePost' => $post->getDatePost()->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    #[Route('/edit/{id}/remove-image', name: 'app_blog_remove_image', methods: ['POST'])]
    public function removeImage(ManagerRegistry $doctrine, $id): Response
    {
        $post = $doctrine->getRepository(Post::class)->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Le post d\'id ' . $id . ' n\'a pas été trouvé.');
        }

        $post->setImage(null);

        $em = $doctrine->getManager();
        $em->persist($post);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }
}
