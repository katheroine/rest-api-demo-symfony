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

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/api/posts', name: 'list_posts', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'RADS: list_posts',
        ]);
    }

    #[Route('/api/posts/{id}', name: 'show_post', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        return $this->json([
            'message' => 'RADS: show_post',
        ]);
    }

    #[Route('/api/posts', name: 'create_post', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        return $this->json([
            'message' => 'RADS: create_post',
        ]);
    }

    #[Route('/api/posts/{id}', name: 'update_post', methods: ['PUT'])]
    public function update(Request $request)
    {
        return $this->json([
            'message' => 'RADS: update_post',
        ]);
    }

    #[Route('/api/posts/{id}', name: 'delete_post', methods: ['DELETE'])]
    public function delete(int $id)
    {
        return $this->json([
            'message' => 'RADS: delete_post',
        ]);
    }
}
