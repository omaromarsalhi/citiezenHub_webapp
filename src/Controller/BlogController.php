<?php

namespace App\Controller;


use App\Entity\ImagePsot;
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
        $posts = $postRepository->findBy([], ['date_post' => 'DESC']);

        return $this->render('blog/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/blog/page/{page}', name: 'app_blog_page', methods: ['GET'])]
    public function page(int $page, PostRepository $postRepository): Response
    {
        $postsPerPage = 5;
        $offset = ($page - 1) * $postsPerPage;

        $posts = $postRepository->findBy([], ['date_post' => 'DESC'], $postsPerPage, $offset);

        $postsArray = array_map(function ($post) {
            $images = $post->getImages();
            $imagesArray = array_map(function ($image) {
                return $image->getPath();
            }, $images);

            return [
                'id' => $post->getId(),
                'caption' => $post->getCaption(),
                'datePost' => $post->getDatePost()->format('Y-m-d H:i:s'),
                'nbReactions' => $post->getNbReactions(),
                'images' => $imagesArray,
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

    #[Route('/new', name: 'app_blog_new', methods: ['GET', 'POST'])]
    public function new(Request $req, ManagerRegistry $doc): Response
    {
        if ($req->isXmlHttpRequest()) {
            $post = new Post();
            $caption = $req->get('caption');
            $imageFiles = $req->files->get('images');

            $post->setCaption($caption);
            $post->setDatePost(new DateTime());
            $post->setNbReactions(0);

            $em = $doc->getManager();
            $em->persist($post);

            $imagesArray = [];
            if ($imageFiles) { // Vérifier si des images ont été fournies
                foreach ($imageFiles as $imageFile) {
                    $postImage = new ImagePsot();
                    $postImage->setImageFile($imageFile);
                    $postImage->setPost($post);
                    $em->persist($postImage);
                    $imagesArray[] = $postImage->getPath();
                }
            }

            $em->flush();
            return new JsonResponse([
                'success' => true,
                'post' => [
                    'id' => $post->getId(),
                    'caption' => $post->getCaption(),
                    'datePost' => $post->getDatePost()->format('Y-m-d H:i:s'),
                    'nbReactions' => $post->getNbReactions(),
                    'images' => $imagesArray,
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
    $imageFiles = $req->files->get('images'); // Récupérez les fichiers d'image

    $post->setDatePost(new DateTime());
    $post->setCaption($caption);

    if ((empty($caption) && empty($imageFiles)) || (ctype_space($caption) && empty($imageFiles))) {
        return new JsonResponse(['success' => false, 'message' => 'Le caption ne peut pas être vide si aucune image n\'est fournie, et vice versa.']);
    }

    $em = $doctrine->getManager();

    $imagesArray = [];
    if ($imageFiles) { // Vérifiez si des images ont été fournies
        foreach ($imageFiles as $imageFile) {
            $postImage = new ImagePsot();
            $postImage->setImageFile($imageFile);
            $postImage->setPost($post);
            $em->persist($postImage);
            $imagesArray[] = $postImage->getPath();
        }
    }

    // Ajoutez les images déjà présentes dans le post
    foreach ($post->getImages() as $image) {
        $imagesArray[] = $image->getPath();
    }

    $em->persist($post);
    $em->flush();

    $this->addFlash('success', 'Le post a bien été modifié.');

    return new JsonResponse([
        'success' => true,
        'post' => [
            'id' => $post->getId(),
            'caption' => $post->getCaption(),
            'datePost' => $post->getDatePost()->format('Y-m-d H:i:s'),
            'nbReactions' => $post->getNbReactions(),
            'images' => $imagesArray,
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

    #[Route('/blogAdmin', name: 'app_blogAdmin')]
    public function indexAdmin(PostRepository $postRepository): Response
    {
        return $this->render('blog/blogAdmin.html.twig');
    }
}
