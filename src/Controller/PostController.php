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

use App\Entity\Post;
use App\Repository\PostRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class PostController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/posts', name: 'list_posts', methods: ['GET'])]
    public function index(PostRepository $postRepository): JsonResponse
    {
        $posts = $postRepository->findAll();

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
        $dateTime = new DateTimeImmutable();
        $post
            ->setCreatedAt($dateTime)
            ->setUpdatedAt($dateTime)
            ->setSlug($request->get('slug'))
            ->setTitle($request->get('title'))
            ->setContent($request->get('content'));

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

        $dateTime = new DateTimeImmutable();
        $post
            ->setUpdatedAt($dateTime)
            ->setSlug($request->get('slug'))
            ->setTitle($request->get('title'))
            ->setContent($request->get('content'));

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
}
