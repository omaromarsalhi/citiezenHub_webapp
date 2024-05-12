<?php

namespace App\Controller;

use App\MyHelpers\ImageExpliciteApi;
use App\Entity\CommentPost;
use App\Entity\ImagePsot;
use App\MyHelpers\UploadImage;
use App\Repository\CommentPostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;


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

            $postUrl = $this->generateUrl('app_PostDetail', ['id' => $post->getId()]);

            $nbComments = count($post->getComments());


            $user = $post->getUser();
            $userName = $user->getLastName();
            $userSurname = $user->getFirstName();
            $userImage = $user->getImage();
            $userId = $user->getId();

            return [
                'id' => $post->getId(),
                'caption' => $post->getCaption(),
                'datePost' => $post->getDatePost()->format('Y-m-d H:i:s'),
                'nbReactions' => $post->getNbReactions(),
                'images' => $imagesArray,
                'url' => $postUrl,
                'nbComments' => $nbComments,
                'userName' => $userName,
                'userSurname' => $userSurname,
                'userImage' => $userImage,
                'userId' => $userId,
            ];
        }, $posts);

        return new JsonResponse(['posts' => $postsArray]);
    }

    #[Route('/getUserId', name: 'app_get_user_id', methods: ['GET'])]
    public function getUserId(UserRepository $userRepository): Response
    {
        $userEmail = $this->getUser()->getUserIdentifier();
        $user = $userRepository->findOneBy(['email' => $userEmail]);
        $userId = null;
        if ($user) {
            $userId = $user->getId();
        }

        return new JsonResponse(['userId' => $userId]);
    }

    #[Route('/blog/count', name: 'app_blog_count', methods: ['GET'])]
    public function count(PostRepository $postRepository, UserRepository $userRepository): Response
    {
        $count = $postRepository->count([]);
        return new JsonResponse(['count' => $count]);
    }

    #[Route('/new', name: 'app_blog_new', methods: ['GET', 'POST'])]
    public function new(Request $req, ManagerRegistry $doc, ValidatorInterface $validator): Response
    {
        if ($req->isXmlHttpRequest()) {
            $post = new Post();
            $caption = $req->get('caption');
            $imageFiles = $req->files->get('images');

            $user = $this->getUser();

            $post->setCaption($caption);
            $post->setDatePost(new DateTime());
            $post->setNbReactions(0);
            $post->setUser($user);

            // Add images to the Post entity before validation
            if ($imageFiles) {
                foreach ($imageFiles as $imageFile) {
                    $postImage = new ImagePsot();
                    $postImage->setImageFile($imageFile);
                    $postImage->setPost($post);
                    $post->addImage($postImage);
                }
            }

            $errors = $validator->validate($post);

            if (count($errors) > 1) {
                $errorsString = '';
                foreach ($errors as $error) {
                    $errorsString .= $error->getMessage() . "\n";
                }

                return new JsonResponse(['success' => false, 'message' => $errorsString]);
            }

            $em = $doc->getManager();
            $em->persist($post);

            // Persist images to the database
            foreach ($post->getImages() as $postImage) {
                $em->persist($postImage);
            }

            $em->flush();

            $imagesArray = array_map(function ($image) {
                return $image->getPath();
            }, $post->getImages());

            $nbComments = count($post->getComments());

            $postUrl = $this->generateUrl('app_PostDetail', ['id' => $post->getId()]);

            return new JsonResponse([
                'success' => true,
                'post' => [
                    'id' => $post->getId(),
                    'caption' => $post->getCaption(),
                    'datePost' => $post->getDatePost()->format('Y-m-d H:i:s'),
                    'nbReactions' => $post->getNbReactions(),
                    'images' => $imagesArray,
                    'url' => $postUrl,
                    'nbComments' => $nbComments,
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
        $imageFiles = $req->files->get('images');

        $post->setDatePost(new DateTime());
        $post->setCaption($caption);

        if ((empty($caption) && empty($imageFiles)) || (ctype_space($caption) && empty($imageFiles))) {
            return new JsonResponse(['success' => false, 'message' => 'Le caption ne peut pas être vide si aucune image n\'est fournie, et vice versa.']);
        }

        $em = $doctrine->getManager();

        $imagesArray = [];
        if ($imageFiles) {
            foreach ($imageFiles as $imageFile) {
                $postImage = new ImagePsot();
                $postImage->setImageFile($imageFile);
                $postImage->setPost($post);
                $em->persist($postImage);
                $imagesArray[] = $postImage->getPath();
            }
        }

        foreach ($post->getImages() as $image) {
            $imagesArray[] = $image->getPath();
        }

        $nbComments = count($post->getComments());

        $em->persist($post);
        $em->flush();

        $postUrl = $this->generateUrl('app_PostDetail', ['id' => $post->getId()]);

        $this->addFlash('success', 'Le post a bien été modifié.');

        return new JsonResponse([
            'success' => true,
            'post' => [
                'id' => $post->getId(),
                'caption' => $post->getCaption(),
                'datePost' => $post->getDatePost()->format('Y-m-d H:i:s'),
                'nbReactions' => $post->getNbReactions(),
                'images' => $imagesArray,
                'url' => $postUrl,
                'nbComments' => $nbComments,
            ]
        ]);
    }

    #[Route('/blog/search/{caption}', name: 'app_blog_search', methods: ['GET'])]
    public function search(string $caption, PostRepository $postRepository): Response
    {
        $posts = $postRepository->findByCaptionLike($caption);

        $postsArray = array_map(function ($post) {
            $images = $post->getImages();
            $imagesArray = array_map(function ($image) {
                return $image->getPath();
            }, $images);

            $postUrl = $this->generateUrl('app_PostDetail', ['id' => $post->getId()]);

            $nbComments = count($post->getComments());

            return [
                'id' => $post->getId(),
                'caption' => $post->getCaption(),
                'datePost' => $post->getDatePost()->format('Y-m-d H:i:s'),
                'nbReactions' => $post->getNbReactions(),
                'images' => $imagesArray,
                'url' => $postUrl,
                'nbComments' => $nbComments,
            ];
        }, $posts);

        return new JsonResponse(['posts' => $postsArray]);
    }

    #[Route('/edit/{id}/remove-image', name: 'app_blog_remove_image', methods: ['POST'])]
    public function removeImage(ManagerRegistry $doctrine, $id): Response
    {
        $post = $doctrine->getRepository(Post::class)->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Le post d\'id ' . $id . ' n\'a pas été trouvé.');
        }

        $em = $doctrine->getManager();
        $em->persist($post);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/blogAdmin2', name: 'app_blogAdmin')]
    public function indexAdmin(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy([], ['date_post' => 'DESC']);

        $postsArray = array_map(function ($post) {
            $images = $post->getImages();
            $imagesArray = array_map(function ($image) {
                $imagePath = $this->getParameter('images_directory') . '/' . $image->getPath();
                list($width, $height) = getimagesize($imagePath);
                $imageSize = $width > $height ? 'large' : 'small';
                return [
                    'path' => $image->getPath(),
                    'size' => $imageSize,
                ];
            }, $images);

            $comments = $post->getComments()->toArray();
            $commentsArray = array_map(function ($comment) {
                return [
                    'id' => $comment->getIdComment(),
                    'caption' => $comment->getCaption(),
                    'dateComment' => $comment->getDateComment()->format('Y-m-d H:i:s'),
                ];
            }, $comments);

            return [
                'id' => $post->getId(),
                'caption' => $post->getCaption(),
                'datePost' => $post->getDatePost()->format('Y-m-d H:i:s'),
                'nbReactions' => $post->getNbReactions(),
                'images' => $imagesArray,
                'comments' => $commentsArray,
                'nbComments' => count($comments),
            ];
        }, $posts);

        return $this->render('blog/blogAdmin2.html.twig', [
            'posts' => $postsArray,
        ]);
    }

    #[Route('/PostDetail/{id}', name: 'app_PostDetail')]
    public function indexPostDetail($id, PostRepository $postRepository, CommentPostRepository $commentPostRepository): Response
    {
        $post = $postRepository->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Le post d\'id ' . $id . ' n\'a pas été trouvé.');
        }

        $images = $post->getImages();
        $imagesArray = array_map(function ($image) {
            return $image->getPath();
        }, $images);

        $comments = $commentPostRepository->findBy(['post' => $post->getId()], ['dateComment' => 'DESC']);

        $commentsArray = array_map(function ($comment) {
            return [
                'id' => $comment->getIdComment(),
                'idPost' => $comment->getPost()->getId(),
                'caption' => $comment->getCaption(),
                'dateComment' => $comment->getDateComment()->format('Y-m-d H:i:s'),
            ];
        }, $comments);

        return $this->render('blog/postDetails.html.twig', [
            'post' => $post,
            'images' => $imagesArray,
            'comments' => $commentsArray,
            'nbComments' => count($comments),
        ]);
    }

    #[Route('/newComment', name: 'new_comment', methods: ['POST'])]
    public function newComment(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();


        $caption = $request->request->get('caption');
        $postId = $request->request->get('post_id');

        // Trouver le post correspondant
        $post = $entityManager->getRepository(Post::class)->find($postId);

        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id ' . $postId
            );
        }

        $comment = new CommentPost();
        $user = $this->getUser();

        $comment->setCaption($caption);
        $comment->setPost($post);
        $comment->setDateComment(new \DateTime());
        $comment->setUser($user);

        $entityManager->persist($comment);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'comment' => [
                'id' => $comment->getIdComment(),
                'idPost' => $comment->getPost()->getId(),
                'caption' => $comment->getCaption(),
                'dateComment' => $comment->getDateComment()->format('Y-m-d H:i:s'),
            ]
        ]);
    }


    #[Route('/deleteComment/{id}', name: 'delete_comment', methods: ['DELETE'])]
    public function deleteComment($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(CommentPost::class)->find($id);

        if (!$comment) {
            return new JsonResponse(['success' => false, 'message' => 'Commentaire non trouvé.']);
        }

        try {
            $em->remove($comment);
            $em->flush();
            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Une erreur est survenue lors de la suppression du commentaire.']);
        }
    }

    #[Route('/updateComment/{id}', name: 'update_comment', methods: ['POST'])]
    public function updateComment($id, Request $request): Response
    {
        $repository = $this->getDoctrine()->getRepository(CommentPost::class);


        $comment = $repository->find($id);

        if (!$comment) {

            return new JsonResponse(['success' => false, 'message' => 'Comment not found']);
        }


        $newCaption = $request->request->get('caption');


        $comment->setCaption($newCaption);
        $comment->setDateComment(new \DateTime());


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comment);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'comment' => [
                'id' => $comment->getIdComment(),
                'idPost' => $comment->getPost()->getId(),
                'caption' => $comment->getCaption(),
                'dateComment' => $comment->getDateComment()->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/checkImage', name: 'checkImage', methods: ['POST'])]
    public function checkImage(Request $request, ImageExpliciteApi $imageExpliciteApi, UploadImage $uploadImage): Response
    {
        $imageFile = $request->files->get('image');

        if ($imageFile) {
            $imageUrl = $uploadImage->uploadImageToImgBB($imageFile);

            $response = $imageExpliciteApi->checkExplicitContent($imageUrl);

            return new JsonResponse($response);
        }

        return new JsonResponse(['error' => 'Aucune image n\'a été reçue.']);
    }
}
