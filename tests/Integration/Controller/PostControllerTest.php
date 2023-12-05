<?php

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;

class PostControllerTest extends WebTestCase
{
    private AbstractBrowser $client;

    private const API_URI_PREFIX = '/api/';

    private const HEADER_CONTENT_TYPE_KEY = 'Content-Type';
    private const HEADER_CONTENT_TYPE_VALUE_JSON = 'application/json';

    public function testListPosts()
    {
        $this->sendRequest(
            method: 'GET',
            uri: self::buildApiUri('posts')
        );

        $this->assertResonseStatusIs(200);
        $this->assertResponseContentIsJson();
        $this->assertResponseContainsAllPostItems();
    }

    public function testListPostsWithLimit()
    {
        $limit = 2;

        $this->sendRequest(
            method: 'GET',
            uri: self::buildApiUri("posts?limit={$limit}")
        );

        $this->assertResonseStatusIs(200);
        $this->assertResponseContentIsJson();
        $this->assertResponseContainsLimitedPostItems();
    }

    public function testListPostsWithOffset()
    {
        $offset = 1;

        $this->sendRequest(
            method: 'GET',
            uri: self::buildApiUri("posts?offset={$offset}")
        );

        $this->assertResonseStatusIs(200);
        $this->assertResponseContentIsJson();
        $this->assertResponseContainsOffsetPostItems();
    }

    public function testListPostsWithLimitAndOffset()
    {
        $limit = 1;
        $offset = 2;

        $this->sendRequest(
            method: 'GET',
            uri: self::buildApiUri("posts?limit={$limit}&offset={$offset}")
        );

        $this->assertResonseStatusIs(200);
        $this->assertResponseContentIsJson();
        $this->assertResponseContainsLimitedAndOffsetPostItems();
    }

    public function testListPostsWenLimitIsNegative()
    {
        $limit = -1;

        $this->sendRequest(
            method: 'GET',
            uri: self::buildApiUri("posts?limit={$limit}")
        );

        $this->assertResonseStatusIs(422);
        $this->assertResponseContentIsJson();
        $this->assertResponseContainsNagativeLimitError();
    }

    public function testListPostsWenLimitIsTooBig()
    {
        $limit = 101;

        $this->sendRequest(
            method: 'GET',
            uri: self::buildApiUri("posts?limit={$limit}")
        );

        $this->assertResonseStatusIs(422);
        $this->assertResponseContentIsJson();
        $this->assertResponseContainsTooBigLimitError();
    }

    public function testListPostsWenLimitIsString()
    {
        $limit = 'apple';

        $this->sendRequest(
            method: 'GET',
            uri: self::buildApiUri("posts?limit={$limit}")
        );

        $this->assertResonseStatusIs(200);
        $this->assertResponseContentIsJson();
        $this->assertResponseContainsNoPostItems();
    }

    public function testListPostsWenOffsetIsNegative()
    {
        $offset = -2;

        $this->sendRequest(
            method: 'GET',
            uri: self::buildApiUri("posts?offset={$offset}")
        );

        $this->assertResonseStatusIs(422);
        $this->assertResponseContentIsJson();
        $this->assertResponseContainsNegativeOffsetError();
    }

    public function testListPostsWenOffsetIsString()
    {
        $offset = 'apple';

        $this->sendRequest(
            method: 'GET',
            uri: self::buildApiUri("posts?offset={$offset}")
        );

        $this->assertResonseStatusIs(200);
        $this->assertResponseContentIsJson();
        $this->assertResponseContainsAllPostItems();
    }

    public function testShowPost()
    {
        $postId = 1;

        $this->sendRequest(
            method: 'GET',
            uri: self::buildApiUri("posts/{$postId}")
        );

        $this->assertResonseStatusIs(200);
        $this->assertResponseContentIsJson();
        $this->assertResponseContainsOnePostWithId1();
    }

    public function testShowPostWhenPostDoesNotExist()
    {
        $postId = 1000;

        $this->sendRequest(
            method: 'GET',
            uri: self::buildApiUri("posts/{$postId}")
        );

        $this->assertResonseStatusIs(404);
        $this->assertResponseIsPostNotFoundMessage($postId);
    }

    public function testCreatePost()
    {
        $creationDateTime = new DateTime();
        $textualCreationDateTime = $creationDateTime->format('c');

        $parameters = [
            'slug' => 'some-post-fixture',
            'title' => 'Some post fixture',
            'content' => 'Some text of some post fixture.',
        ];

        $this->sendRequest(
            method: 'POST',
            uri: self::buildApiUri("posts"),
            parameters: $parameters
        );

        $fields = $parameters;
        $fields['id'] = 4;
        $fields['createdAt'] = $textualCreationDateTime;
        $fields['updatedAt'] = $textualCreationDateTime;

        $this->assertResonseStatusIs(201);
        $this->assertResponseContentIsJson();
        $this->assertResponseContainsOnePostWithFields($fields);
    }

    public function testCreatePostWhenSlugIsTooLong()
    {
        $slug = str_repeat('a', 128);

        $this->sendRequest(
            method: 'POST',
            uri: self::buildApiUri("posts"),
            parameters: [
                'slug' => $slug,
                'title' => 'Some post fixture',
                'content' => 'Some text of some post fixture.',
            ]
        );

        $this->assertResonseStatusIs(422);
        $this->assertResponseContentIsJson();
        $this->assertResponseContainsTooLongSlugError();
    }

    public function testCreatePostWhenTitleIsTooLong()
    {
        $title = str_repeat('a', 256);

        $this->sendRequest(
            method: 'POST',
            uri: self::buildApiUri("posts"),
            parameters: [
                'slug' => 'some-post-fixture',
                'title' => $title,
                'content' => 'Some text of some post fixture.',
            ]
        );

        $this->assertResonseStatusIs(422);
        $this->assertResponseContentIsJson();
        $this->assertResponseContainsTooLongTitleError();
    }

    public function testCreatePostWhenContentIsTooLong()
    {
        $content = str_repeat('a', 1024);

        $this->sendRequest(
            method: 'POST',
            uri: self::buildApiUri("posts"),
            parameters: [
                'slug' => 'some-post-fixture',
                'title' => 'Some post fixture',
                'content' => $content,
            ]
        );

        $this->assertResonseStatusIs(422);
        $this->assertResponseContentIsJson();
        $this->assertResponseContainsTooLongContentError();
    }

    public function testUpdatePost()
    {
        $postId = 1;

        $creationDateTime = new DateTime();
        $textualCreationDateTime = $creationDateTime->format('c');

        $parameters = [
            'slug' => 'some-post-updated',
            'title' => 'Some post updated',
            'content' => 'Some updated text of some updated post.',
        ];

        $this->sendRequest(
            method: 'PUT',
            uri: self::buildApiUri("posts/{$postId}"),
            parameters: $parameters,
        );

        $fields = $parameters;
        $fields['id'] = 1;
        $fields['createdAt'] = '2023-11-28T20:46:04+00:00';
        $fields['updatedAt'] = $textualCreationDateTime;

        $this->assertResonseStatusIs(200);
        $this->assertResponseContentIsJson();
        $this->assertResponseContainsOnePostWithFields($fields);
    }

    public function testUpdatePostWhenSlugIsTooLong()
    {
        $slug = str_repeat('a', 128);

        $postId = 1;

        $this->sendRequest(
            method: 'PUT',
            uri: self::buildApiUri("posts/{$postId}"),
            parameters: [
                'slug' => $slug,
                'title' => 'Some post updated',
                'content' => 'Some updated text of some updated post.',
            ]
        );

        $this->assertResonseStatusIs(422);
        $this->assertResponseContentIsJson();
        $this->assertResponseContainsTooLongSlugError();
    }

    public function testUpdatePostWhenTitleIsTooLong()
    {
        $title = str_repeat('a', 256);

        $postId = 1;

        $this->sendRequest(
            method: 'PUT',
            uri: self::buildApiUri("posts/{$postId}"),
            parameters: [
                'slug' => 'some-post-updated',
                'title' => $title,
                'content' => 'Some updated text of some updated post.',
            ]
        );

        $this->assertResonseStatusIs(422);
        $this->assertResponseContentIsJson();
        $this->assertResponseContainsTooLongTitleError();
    }

    public function testUpdatePostWhenContentIsTooLong()
    {
        $content = str_repeat('a', 1024);

        $postId = 1;

        $this->sendRequest(
            method: 'PUT',
            uri: self::buildApiUri("posts/{$postId}"),
            parameters: [
                'slug' => 'some-post-updated',
                'title' => 'Some post updated',
                'content' => $content,
            ]
        );

        $this->assertResonseStatusIs(422);
        $this->assertResponseContentIsJson();
        $this->assertResponseContainsTooLongContentError();
    }

    public function testUpdatePostWhenPostDoesNotExist()
    {
        $postId = 1000;

        $this->sendRequest(
            method: 'PUT',
            uri: self::buildApiUri("posts/{$postId}"),
            parameters: [
                'content' => 'Some updated text of some updated post.',
                'slug' => 'some-post-updated',
                'title' => 'Some post updated',
            ]
        );

        $this->assertResonseStatusIs(404);
        $this->assertResponseIsPostNotFoundMessage($postId);
    }

    public function testDeletePost()
    {
        $postId = 2;

        $this->sendRequest(
            method: 'DELETE',
            uri: self::buildApiUri("posts/{$postId}")
        );

        $this->assertResonseStatusIs(200);
        $this->assertResponseContentIsJson();
        $this->assertResponseContainsOnePostWithNoId();
    }

    public function testDeletePostWhenPostDoesNotExist()
    {
        $postId = 1000;

        $this->sendRequest(
            method: 'DELETE',
            uri: self::buildApiUri("posts/{$postId}")
        );

        $this->assertResonseStatusIs(404);
        $this->assertResponseIsPostNotFoundMessage($postId);
    }

    private function assertResponseContainsAllPostItems(): void
    {
        $responseItems = $this->getDecodedResponseContent();

        $this->assertCount(3, $responseItems);

        $expectedPostObject1 = (object) [
            'id' => 1,
            'createdAt' => "2023-11-28T20:46:04+00:00",
            'updatedAt' => "2023-11-28T20:46:04+00:00",
            'slug' => "some-post-fixture-1",
            'title' => "Some post fixture 1",
            'content' => "Some text of some post fixture 1."
        ];
        $actualPostObject1 = $responseItems[0];
        $this->assertEquals($expectedPostObject1, $actualPostObject1);

        $expectedPostObject2 = (object) [
            'id' => 2,
            'createdAt' => "2023-11-29T20:46:04+00:00",
            'updatedAt' => "2023-11-29T20:46:04+00:00",
            'slug' => "some-post-fixture-2",
            'title' => "Some post fixture 2",
            'content' => "Some text of some post fixture 2."
        ];
        $actualPostObject2 = $responseItems[1];
        $this->assertEquals($expectedPostObject2, $actualPostObject2);

        $expectedPostObject3 = (object) [
            'id' => 3,
            'createdAt' => "2023-11-30T20:46:04+00:00",
            'updatedAt' => "2023-11-30T20:46:04+00:00",
            'slug' => "some-post-fixture-3",
            'title' => "Some post fixture 3",
            'content' => "Some text of some post fixture 3."
        ];
        $actualPostObject3 = $responseItems[2];
        $this->assertEquals($expectedPostObject3, $actualPostObject3);
    }

    private function assertResponseContainsLimitedPostItems(): void
    {
        $responseItems = $this->getDecodedResponseContent();

        $this->assertCount(2, $responseItems);

        $expectedPostObject1 = (object) [
            'id' => 1,
            'createdAt' => "2023-11-28T20:46:04+00:00",
            'updatedAt' => "2023-11-28T20:46:04+00:00",
            'slug' => "some-post-fixture-1",
            'title' => "Some post fixture 1",
            'content' => "Some text of some post fixture 1."
        ];
        $actualPostObject1 = $responseItems[0];
        $this->assertEquals($expectedPostObject1, $actualPostObject1);

        $expectedPostObject2 = (object) [
            'id' => 2,
            'createdAt' => "2023-11-29T20:46:04+00:00",
            'updatedAt' => "2023-11-29T20:46:04+00:00",
            'slug' => "some-post-fixture-2",
            'title' => "Some post fixture 2",
            'content' => "Some text of some post fixture 2."
        ];
        $actualPostObject2 = $responseItems[1];
        $this->assertEquals($expectedPostObject2, $actualPostObject2);
    }

    private function assertResponseContainsOffsetPostItems(): void
    {
        $responseItems = $this->getDecodedResponseContent();

        $this->assertCount(2, $responseItems);

        $expectedPostObject1 = (object) [
            'id' => 2,
            'createdAt' => "2023-11-29T20:46:04+00:00",
            'updatedAt' => "2023-11-29T20:46:04+00:00",
            'slug' => "some-post-fixture-2",
            'title' => "Some post fixture 2",
            'content' => "Some text of some post fixture 2."
        ];
        $actualPostObject1 = $responseItems[0];
        $this->assertEquals($expectedPostObject1, $actualPostObject1);

        $expectedPostObject2 = (object) [
            'id' => 3,
            'createdAt' => "2023-11-30T20:46:04+00:00",
            'updatedAt' => "2023-11-30T20:46:04+00:00",
            'slug' => "some-post-fixture-3",
            'title' => "Some post fixture 3",
            'content' => "Some text of some post fixture 3."
        ];
        $actualPostObject2 = $responseItems[1];
        $this->assertEquals($expectedPostObject2, $actualPostObject2);
    }

    private function assertResponseContainsLimitedAndOffsetPostItems(): void
    {
        $responseItems = $this->getDecodedResponseContent();

        $this->assertCount(1, $responseItems);

        $expectedPostObject1 = (object) [
            'id' => 3,
            'createdAt' => "2023-11-30T20:46:04+00:00",
            'updatedAt' => "2023-11-30T20:46:04+00:00",
            'slug' => "some-post-fixture-3",
            'title' => "Some post fixture 3",
            'content' => "Some text of some post fixture 3."
        ];
        $actualPostObject1 = $responseItems[0];
        $this->assertEquals($expectedPostObject1, $actualPostObject1);
    }

    private function assertResponseContainsOnePostWithId1(): void
    {
        $expectedPostObject = (object) [
            'id' => 1,
            'createdAt' => "2023-11-28T20:46:04+00:00",
            'updatedAt' => "2023-11-28T20:46:04+00:00",
            'slug' => "some-post-fixture-1",
            'title' => "Some post fixture 1",
            'content' => "Some text of some post fixture 1."
        ];
        $actualPostObject = $this->getDecodedResponseContent();
        $this->assertEquals($expectedPostObject, $actualPostObject);
    }

    private function assertResponseContainsOnePostWithNoId(): void
    {
        $expectedPostObject = (object) [
            'id' => null,
            'createdAt' => "2023-11-29T20:46:04+00:00",
            'updatedAt' => "2023-11-29T20:46:04+00:00",
            'slug' => "some-post-fixture-2",
            'title' => "Some post fixture 2",
            'content' => "Some text of some post fixture 2."
        ];
        $actualPostObject = $this->getDecodedResponseContent();
        $this->assertEquals($expectedPostObject, $actualPostObject);
    }

    private function assertResponseContainsOnePostWithFields(array $fields): void
    {
        $expectedPostObject = (object) $fields;
        $actualPostObject = $this->getDecodedResponseContent();
        $this->assertEquals($expectedPostObject, $actualPostObject);
    }

    private function assertResponseContainsNoPostItems(): void
    {
        $responseItems = $this->getDecodedResponseContent();
        $this->assertCount(0, $responseItems);
    }

    private function assertResponseContainsNagativeLimitError(): void
    {
        $expectedErrorObject = (object) [
            'limit' => 'This value should be either positive or zero.'
        ];
        $actualErrorObject = $this->getDecodedResponseContent();
        $this->assertEquals($expectedErrorObject, $actualErrorObject);
    }

    private function assertResponseContainsNegativeOffsetError(): void
    {
        $expectedErrorObject = (object) [
            'offset' => 'This value should be either positive or zero.'
        ];
        $actualErrorObject = $this->getDecodedResponseContent();
        $this->assertEquals($expectedErrorObject, $actualErrorObject);
    }

    private function assertResponseContainsTooBigLimitError(): void
    {
        $expectedErrorObject = (object) [
            'limit' => 'This value should be less than 100.'
        ];
        $actualErrorObject = $this->getDecodedResponseContent();
        $this->assertEquals($expectedErrorObject, $actualErrorObject);
    }

    private function assertResponseContainsTooLongSlugError(): void
    {
        $expectedErrorObject = (object) [
            'slug' => 'Slug cannot be longer than 127 characters'
        ];
        $actualErrorObject = $this->getDecodedResponseContent();
        $this->assertEquals($expectedErrorObject, $actualErrorObject);
    }

    public function assertResponseContainsTooLongTitleError(): void
    {
        $expectedPostObject = (object) [
            'title' => 'Title cannot be longer than 255 characters'
        ];

        $actualPostObject = $this->getDecodedResponseContent();

        $this->assertEquals($expectedPostObject, $actualPostObject);
    }

    private function assertResponseContainsTooLongContentError(): void
    {
        $expectedPostObject = (object) [
            'content' => 'Content cannot be longer than 1023 characters'
        ];

        $actualPostObject = $this->getDecodedResponseContent();

        $this->assertEquals($expectedPostObject, $actualPostObject);
    }

    private function getDecodedResponseContent(): array|object
    {
        $encodedResponse = $this->getResponseContent();
        $decodedResponse = json_decode($encodedResponse);

        return $decodedResponse;
    }

    /**
     * @param string $uriCore
     *
     * @return string
     */
    private static function buildApiUri(string $uriCore): string
    {
        return self::API_URI_PREFIX . $uriCore;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $parameters
     * @param string $content
     *
     * @return void
     */
    private function sendRequest(string $method, string $uri, array $parameters = [], string $content = ''): void
    {
        $this->client->request(
            method: $method,
            uri: $uri,
            parameters: $parameters,
            files: [],
            server: [
                'CONTENT_TYPE' => 'application/json'
            ],
            content: $content
        );
    }

    /**
     * @param int $statusCode
     *
     * @return void
     */
    private function assertResonseStatusIs(int $statusCode): void
    {
        $failMessage = "Failed asserting that response status code is {$statusCode}.";

        $this->assertEquals(
            $statusCode,
            $this->getResponseStatusCode(),
            $failMessage
        );
    }

    /**
     * @return void
     */
    private function assertResponseContentIsJson(): void
    {
        $this->assertTrue(
            $this->getResponseHeaders()->contains(
                self::HEADER_CONTENT_TYPE_KEY,
                self::HEADER_CONTENT_TYPE_VALUE_JSON
            )
        );
    }

    /**
     * @param int $postId
     *
     * @return void
     */
    private function assertResponseIsPostNotFoundMessage(int $postId): void
    {
        $this->assertEquals(
            "\"Post with id {$postId} not found.\"",
            $this->getResponseContent()
        );
    }

    /**
     * @return int
     */
    private function getResponseStatusCode(): int
    {
        return $this->client->getResponse()->getStatusCode();
    }

    private function getResponseHeaders()
    {
        return $this->client->getResponse()->headers;
    }

    /**
     * @return string
     */
    private function getResponseContent(): string
    {
        return $this->client->getResponse()->getContent();
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
    }
}
