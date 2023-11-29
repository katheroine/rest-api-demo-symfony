<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Post;
use DateTime;
use DateTimeImmutable;

class AccountTest extends TestCase
{
    private const POST_ENTITY_FULLY_QUALIFIED_CLASS_NAME = 'App\Entity\Post';

    public function testPostEntityClassExists()
    {
        $this->assertTrue(
            class_exists(self::POST_ENTITY_FULLY_QUALIFIED_CLASS_NAME)
        );
    }

    /**
     * @dataProvider accessorNamesProvider
     */
    public function testAccessorsExists(string $accessorName)
    {
        $this->assertTrue(
            method_exists(
                self::POST_ENTITY_FULLY_QUALIFIED_CLASS_NAME,
                $accessorName
            )
        );
    }

    private static array $getterNames = [
        'id' => 'getId',
        'createdAt' => 'getCreatedAt',
        'updatedAt' => 'getUpdatedAt',
        'slug' => 'getSlug',
        'title' => 'getTitle',
        'content' => 'getContent',
    ];

    private static array $setterNames = [
        'createdAt' => 'setCreatedAt',
        'updatedAt' => 'setUpdatedAt',
        'slug' => 'setSlug',
        'title' => 'setTitle',
        'content' => 'setContent',
    ];

    /**
     * @dataProvider setterNamesAndArguemntsProvider
     */
    public function testSettersReturnSamePostObject(string $setterName, mixed $setterArgument)
    {
        $post = new Post();

        $this->assertSame($post, $post->$setterName($setterArgument));
    }

    /**
     * @dataProvider getterNamesProvider
     */
    public function testGettersReturnNullWhenEntityIsNotHydrated(string $getterName)
    {
        $post = new Post();

        $actualReturnedType = gettype($post->$getterName());

        $this->assertSame('NULL', $actualReturnedType);
    }

    /**
     * @dataProvider setCreatedAtImproperArgumentsProvider
     */
    public function testSetCreatedAtWhenArgumentHasImproperType(mixed $createdAtImproperValue)
    {
        $post = new Post();

        $this->expectException('\TypeError');

        $post->setCreatedAt($createdAtImproperValue);
    }

    /**
     * @dataProvider setCreatedAtProperArgumentsProvider
     */
    public function testCreatedAtAccessors(mixed $createdAtExpectedValue)
    {
        $post = new Post();

        $post->setCreatedAt($createdAtExpectedValue);
        $createdAtActualValue = $post->getCreatedAt();

        $this->assertSame($createdAtExpectedValue, $createdAtActualValue);
    }

    /**
     * @dataProvider setUpdatedAtImproperArgumentsProvider
     */
    public function testSetUpdatedAtWhenArgumentHasImproperType(mixed $updatedAtImproperValue)
    {
        $post = new Post();

        $this->expectException('\TypeError');

        $post->setUpdatedAt($updatedAtImproperValue);
    }

    /**
     * @dataProvider setUpdatedAtProperArgumentsProvider
     */
    public function testUpdatedAtAccessors(mixed $updatedAtExpectedValue)
    {
        $post = new Post();

        $post->setUpdatedAt($updatedAtExpectedValue);
        $updatedAtActualValue = $post->getUpdatedAt();

        $this->assertSame($updatedAtExpectedValue, $updatedAtActualValue);
    }

    /**
     * @dataProvider setSlugImproperArgumentsProvider
     */
    public function testSetSlugWhenArgumentHasImproperType(mixed $slugImproperValue)
    {
        $post = new Post();

        $this->expectException('\TypeError');

        $post->setSlug($slugImproperValue);
    }

    public function testSetSlugWhenArgumentHasMaximalLength()
    {
        $post = new Post();

        $slugValue = str_repeat('a', 127);

        $post->setSlug($slugValue);
        $errors = $post->validate();

        $this->assertEmpty($errors);
    }

    public function testSetSlugWhenArgumentIsTooLong()
    {
        $post = new Post();

        $slugValue = str_repeat('a', 128);

        $post->setSlug($slugValue);
        $errors = $post->validate();

        $this->assertCount(1, $errors);
        $this->assertArrayHasKey('slug', $errors);
        $this->assertEquals(
            'Slug cannot be longer than 127 characters',
            $errors['slug']
        );
    }

    /**
     * @dataProvider setSlugProperArgumentsProvider
     */
    public function testSlugAccessors(mixed $slugExpectedValue)
    {
        $post = new Post();

        $post->setSlug($slugExpectedValue);
        $slugActualValue = $post->getSlug();

        $this->assertSame($slugExpectedValue, $slugActualValue);
    }

    /**
     * @dataProvider setTitleAndSetContentImproperArgumentsProvider
     */
    public function testSetTitleWhenArgumentHasImproperType(mixed $titleImproperValue)
    {
        $post = new Post();

        $this->expectException('\TypeError');

        $post->setTitle($titleImproperValue);
    }

    public function testSetTitleWhenArgumentHasMaximalLength()
    {
        $post = new Post();

        $titleValue = str_repeat('a', 255);

        $post->setTitle($titleValue);
        $errors = $post->validate();

        $this->assertEmpty($errors);
    }

    public function testSetTitleWhenArgumentIsTooLong()
    {
        $post = new Post();

        $titleValue = str_repeat('a', 256);

        $post->setTitle($titleValue);
        $errors = $post->validate();

        $this->assertCount(1, $errors);
        $this->assertArrayHasKey('title', $errors);
        $this->assertEquals(
            'Title cannot be longer than 255 characters',
            $errors['title']
        );
    }

    /**
     * @dataProvider setTitleAndSetContentProperArgumentsProvider
     */
    public function testTitleAccessors(mixed $titleExpectedValue)
    {
        $post = new Post();

        $post->setTitle($titleExpectedValue);
        $titleActualValue = $post->getTitle();

        $this->assertSame($titleExpectedValue, $titleActualValue);
    }

    /**
     * @dataProvider setTitleAndSetContentImproperArgumentsProvider
     */
    public function testSetContentWhenArgumentHasImproperType(mixed $contentImproperValue)
    {
        $post = new Post();

        $this->expectException('\TypeError');

        $post->setContent($contentImproperValue);
    }

    public function testSetContentWhenArgumentHasMaximalLength()
    {
        $post = new Post();

        $contentValue = str_repeat('a', 1023);

        $post->setContent($contentValue);
        $errors = $post->validate();

        $this->assertEmpty($errors);
    }

    public function testSetContentWhenArgumentIsTooLong()
    {
        $post = new Post();

        $contentValue = str_repeat('a', 1024);

        $post->setContent($contentValue);
        $errors = $post->validate();

        $this->assertCount(1, $errors);
        $this->assertArrayHasKey('content', $errors);
        $this->assertEquals(
            'Content cannot be longer than 1023 characters',
            $errors['content']
        );
    }

    /**
     * @dataProvider setTitleAndSetContentProperArgumentsProvider
     */
    public function testContentAccessors(mixed $contentExpectedValue)
    {
        $post = new Post();

        $post->setTitle($contentExpectedValue);
        $contentActualValue = $post->getTitle();

        $this->assertSame($contentExpectedValue, $contentActualValue);
    }

    public static function accessorNamesProvider(): array
    {
        $accessorNames = array_merge(
            array_map(
                function ($getterName) {
                    return [$getterName];
                },
                self::$getterNames
            ),
            array_map(
                function ($setterName) {
                    return [$setterName];
                },
                self::$setterNames
            )
        );

        return $accessorNames;
    }

    public static function setterNamesAndArguemntsProvider(): array
    {
        $nowDateTime = new DateTimeImmutable();

        return [
            [self::$setterNames['createdAt'], $nowDateTime],
            [self::$setterNames['updatedAt'], $nowDateTime],
            [self::$setterNames['slug'], 'some-post'],
            [self::$setterNames['title'], 'Some title'],
            [self::$setterNames['content'], 'Some content'],
        ];
    }

    public static function getterNamesProvider(): array
    {
        $accessorNames = array_map(
            function ($getterName) {
                return [$getterName];
            },
            self::$getterNames
        );

        return $accessorNames;
    }

    public static function setCreatedAtImproperArgumentsProvider(): array
    {
        return [
            [null],
            [2],
            [3.5],
            [''],
            ['Some text'],
            [[]],
            [new DateTime()],
        ];
    }

    public static function setCreatedAtProperArgumentsProvider(): array
    {
        return [
            [new DateTimeImmutable()],
            [new DateTimeImmutable('2023-01-01')],
        ];
    }

    public static function setUpdatedAtImproperArgumentsProvider(): array
    {
        return [
            [null],
            [2],
            [3.5],
            [''],
            ['Some text'],
            [[]],
        ];
    }

    public static function setUpdatedAtProperArgumentsProvider(): array
    {
        return [
            [new DateTimeImmutable()],
            [new DateTimeImmutable('2023-01-01')],
            [new DateTime()],
            [new DateTime('2023-01-01')],
        ];
    }

    public static function setSlugImproperArgumentsProvider(): array
    {
        return [
            [2],
            [3.5],
            [[]],
            [new DateTime()],
            [new DateTimeImmutable()],
        ];
    }

    public static function setSlugProperArgumentsProvider(): array
    {
        return [
            [null],
            [''],
            ['some-slug'],
            ['Some slug'],
        ];
    }

    public static function setTitleAndSetContentImproperArgumentsProvider(): array
    {
        return [
            [2],
            [3.5],
            [[]],
            [new DateTime()],
            [new DateTimeImmutable()]
        ];
    }

    public static function setTitleAndSetContentProperArgumentsProvider(): array
    {
        return [
            [null],
            [''],
            ['Some text']
        ];
    }
}
