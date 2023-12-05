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

namespace App\Entity;

use App\Repository\PostRepository;
use App\Validation\Validateable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Katarzyna Krasińska <katheroine@gmail.com>
 * @copyright Copyright (c) Katarzyna Krasińska
 * @license http://opensource.org/licenses/MIT MIT License
 * @link https://github.com/katheroine/rest-api-demo-symfony
 */
#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    use Validateable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 127, nullable: true)]
    #[Assert\Length(
        max: 127,
        maxMessage: 'Slug cannot be longer than {{ limit }} characters',
    )]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Title cannot be longer than {{ limit }} characters',
    )]
    private ?string $title = null;

    #[ORM\Column(length: 1023, nullable: true)]
    #[Assert\Length(
        max: 1023,
        maxMessage: 'Content cannot be longer than {{ limit }} characters',
    )]
    private ?string $content = null;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @param \DateTimeImmutable $created_at
     *
     * @return static
     */
    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     * @param \DateTimeInterface $updated_at
     *
     * @return static
     */
    public function setUpdatedAt(\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param ?string $slug
     *
     * @return static
     */
    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param ?string $title
     *
     * @return static
     */
    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param ?string $content
     *
     * @return static
     */
    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }
}
