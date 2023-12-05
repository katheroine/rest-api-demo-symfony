<?php

declare(strict_types=1);

/*
 * This file is part of REST API Demo Symfony application.
 *
 * (c) Katarzyna Krasińska
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */

namespace App\Controller;

use App\Controller\ValidationObject\Limitation;
use App\Entity\Post;
use App\Repository\PostRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Katarzyna Krasińska <katheroine@gmail.com>
 * @copyright Copyright (c) Katarzyna Krasińska
 * @license http://opensource.org/licenses/MIT MIT License
 * @link https://github.com/katheroine/rest-api-demo-symfony
 */
#[Route('/api', name: 'api_')]
class PostController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/posts', name: 'list_posts', methods: ['GET'])]
    public function index(PostRepository $postRepository, Request $request): JsonResponse
    {
        $limit = $request->query->getInt('limit', default: 10);
        $offset = $request->query->getInt('offset', default: 0);

        $limitation = new Limitation($limit, $offset);

        $validationErrors = $limitation->validate();

        if (!empty($validationErrors)) {
            return $this->json($validationErrors, status: 422);
        }

        $posts = $postRepository->findAllLimited($limit, $offset);

        return $this->json($posts, status: 200);
    }

    #[Route('/posts/{id}', name: 'show_post', methods: ['GET'])]
    public function show(PostRepository $postRepository, int $id): JsonResponse
    {
        $post = $postRepository->findOneById($id);

        if (is_null($post)) {
            $message = "Post with id {$id} not found.";

            return $this->json($message, status: 404);
        }

        return $this->json($post, status: 200);
    }

    #[Route('/posts', name: 'create_post', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $post = new Post();
        $this->hydratePostForCreation($post, $request);

        $validationErrors = $post->validate();

        if (!empty($validationErrors)) {
            return $this->json($validationErrors, status: 422);
        }

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $this->json($post, status: 201);
    }

    #[Route('/posts/{id}', name: 'update_post', methods: ['PUT', 'PATCH'])]
    public function update(PostRepository $postRepository, Request $request, int $id): JsonResponse
    {
        $post = $postRepository->findOneById($id);

        if (is_null($post)) {
            $message = "Post with id {$id} not found.";

            return $this->json($message, status: 404);
        }

        $this->hydratePostForUpdate($post, $request);

        $validationErrors = $post->validate();

        if (!empty($validationErrors)) {
            return $this->json($validationErrors, status: 422);
        }

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $this->json($post, status: 200);
    }

    #[Route('/posts/{id}', name: 'delete_post', methods: ['DELETE'])]
    public function delete(PostRepository $postRepository, int $id)
    {
        $post = $postRepository->findOneById($id);

        if (is_null($post)) {
            $message = "Post with id {$id} not found.";

            return $this->json($message, status: 404);
        }

        $this->entityManager->remove($post);
        $this->entityManager->flush();

        return $this->json($post, status: 200);
    }

    /**
     * @param Post $post
     * @param Request $request
     *
     * @return Post
     */
    private function hydratePostForCreation(Post $post, Request $request): Post
    {
        $dateTime = new DateTimeImmutable();
        $post
            ->setCreatedAt($dateTime)
            ->setUpdatedAt($dateTime)
            ->setSlug($request->get('slug'))
            ->setTitle($request->get('title'))
            ->setContent($request->get('content'));

        return $post;
    }

    /**
     * @param Post $post
     * @param Request $request
     *
     * @return Post
     */
    private function hydratePostForUpdate(Post $post, Request $request): Post
    {
        $dateTime = new DateTimeImmutable();
        $post
            ->setUpdatedAt($dateTime)
            ->setSlug($request->get('slug'))
            ->setTitle($request->get('title'))
            ->setContent($request->get('content'));

        return $post;
    }
}
