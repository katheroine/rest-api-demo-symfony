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

namespace App\DataFixtures;

use App\Entity\Post;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Katarzyna Krasińska <katheroine@gmail.com>
 * @copyright Copyright (c) Katarzyna Krasińska
 * @license http://opensource.org/licenses/MIT MIT License
 * @link https://github.com/katheroine/rest-api-demo-symfony
 */
class PostFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $post1 = new Post();

        $creationDateTime = new DateTimeImmutable('2023-11-28 20:46:04.000000');
        $post1
            ->setCreatedAt($creationDateTime)
            ->setUpdatedAt($creationDateTime)
            ->setSlug('some-post-fixture-1')
            ->setTitle('Some post fixture 1')
            ->setContent('Some text of some post fixture 1.');

        $manager->persist($post1);
        $manager->flush();

        $post2 = new Post();

        $creationDateTime = new DateTimeImmutable('2023-11-29 20:46:04.000000');
        $post2
            ->setCreatedAt($creationDateTime)
            ->setUpdatedAt($creationDateTime)
            ->setSlug('some-post-fixture-2')
            ->setTitle('Some post fixture 2')
            ->setContent('Some text of some post fixture 2.');

        $manager->persist($post2);
        $manager->flush();

        $post3 = new Post();

        $creationDateTime = new DateTimeImmutable('2023-11-30 20:46:04.000000');
        $post3
            ->setCreatedAt($creationDateTime)
            ->setUpdatedAt($creationDateTime)
            ->setSlug('some-post-fixture-3')
            ->setTitle('Some post fixture 3')
            ->setContent('Some text of some post fixture 3.');

        $manager->persist($post3);
        $manager->flush();
    }
}
