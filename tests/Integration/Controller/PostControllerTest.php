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

        $responseAsJsonString = $this->getResponseContent();
        $responseAsArray = json_decode($responseAsJsonString);

        $this->assertCount(3, $responseAsArray);

        $expectedPostObject1 = (object) [
            'id' => 1,
            'createdAt' => "2023-11-28T20:46:04+00:00",
            'updatedAt' => "2023-11-28T20:46:04+00:00",
            'slug' => "some-post-fixture-1",
            'title' => "Some post fixture 1",
            'content' => "Some text of some post fixture 1."
        ];

        $actualPostObject1 = $responseAsArray[0];

        $this->assertEquals($expectedPostObject1, $actualPostObject1);

        $expectedPostObject2 = (object) [
            'id' => 2,
            'createdAt' => "2023-11-29T20:46:04+00:00",
            'updatedAt' => "2023-11-29T20:46:04+00:00",
            'slug' => "some-post-fixture-2",
            'title' => "Some post fixture 2",
            'content' => "Some text of some post fixture 2."
        ];

        $actualPostObject2 = $responseAsArray[1];

        $this->assertEquals($expectedPostObject2, $actualPostObject2);

        $expectedPostObject3 = (object) [
            'id' => 3,
            'createdAt' => "2023-11-30T20:46:04+00:00",
            'updatedAt' => "2023-11-30T20:46:04+00:00",
            'slug' => "some-post-fixture-3",
            'title' => "Some post fixture 3",
            'content' => "Some text of some post fixture 3."
        ];

        $actualPostObject3 = $responseAsArray[2];

        $this->assertEquals($expectedPostObject3, $actualPostObject3);
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

        $expectedPostObject = (object) [
            'id' => 1,
            'createdAt' => "2023-11-28T20:46:04+00:00",
            'updatedAt' => "2023-11-28T20:46:04+00:00",
            'slug' => "some-post-fixture-1",
            'title' => "Some post fixture 1",
            'content' => "Some text of some post fixture 1."
        ];

        $responseAsJsonString = $this->getResponseContent();
        $actualPostObject = json_decode($responseAsJsonString);

        $this->assertEquals($expectedPostObject, $actualPostObject);
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

        $this->sendRequest(
            method: 'POST',
            uri: self::buildApiUri("posts"),
            parameters: [
                'slug' => 'some-post-fixture',
                'title' => 'Some post fixture',
                'content' => 'Some text of some post fixture.',
            ]
        );

        $this->assertResonseStatusIs(201);

        $this->assertResponseContentIsJson();

        $expectedPostObject = (object) [
            'id' => 4,
            'createdAt' => $textualCreationDateTime,
            'updatedAt' => $textualCreationDateTime,
            'slug' => "some-post-fixture",
            'title' => "Some post fixture",
            'content' => "Some text of some post fixture."
        ];

        $responseAsJsonString = $this->getResponseContent();
        $actualPostObject = json_decode($responseAsJsonString);

        $this->assertEquals($expectedPostObject, $actualPostObject);
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

        $this->assertResonseStatusIs(400);

        $this->assertResponseContentIsJson();

        $expectedPostObject = (object) [
            'slug' => 'Slug cannot be longer than 127 characters'
        ];

        $responseAsJsonString = $this->getResponseContent();
        $actualPostObject = json_decode($responseAsJsonString);

        $this->assertEquals($expectedPostObject, $actualPostObject);
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

        $this->assertResonseStatusIs(400);

        $this->assertResponseContentIsJson();

        $expectedPostObject = (object) [
            'title' => 'Title cannot be longer than 255 characters'
        ];

        $responseAsJsonString = $this->getResponseContent();
        $actualPostObject = json_decode($responseAsJsonString);

        $this->assertEquals($expectedPostObject, $actualPostObject);
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

        $this->assertResonseStatusIs(400);

        $this->assertResponseContentIsJson();

        $expectedPostObject = (object) [
            'content' => 'Content cannot be longer than 1023 characters'
        ];

        $responseAsJsonString = $this->getResponseContent();
        $actualPostObject = json_decode($responseAsJsonString);

        $this->assertEquals($expectedPostObject, $actualPostObject);
    }

    public function testUpdatePost()
    {
        $postId = 1;

        $creationDateTime = new DateTime();
        $textualCreationDateTime = $creationDateTime->format('c');

        $this->sendRequest(
            method: 'PUT',
            uri: self::buildApiUri("posts/{$postId}"),
            parameters: [
                'slug' => 'some-post-updated',
                'title' => 'Some post updated',
                'content' => 'Some updated text of some updated post.',
            ]
        );

        $this->assertResonseStatusIs(200);

        $this->assertResponseContentIsJson();

        $expectedPostObject = (object) [
            'id' => 1,
            'createdAt' => "2023-11-28T20:46:04+00:00",
            'updatedAt' => $textualCreationDateTime,
            'slug' => 'some-post-updated',
            'title' => 'Some post updated',
            'content' => 'Some updated text of some updated post.'
        ];

        $responseAsJsonString = $this->getResponseContent();
        $actualPostObject = json_decode($responseAsJsonString);

        $this->assertEquals($expectedPostObject, $actualPostObject);
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

        $this->assertResonseStatusIs(400);

        $this->assertResponseContentIsJson();

        $expectedPostObject = (object) [
            'slug' => 'Slug cannot be longer than 127 characters'
        ];

        $responseAsJsonString = $this->getResponseContent();
        $actualPostObject = json_decode($responseAsJsonString);

        $this->assertEquals($expectedPostObject, $actualPostObject);
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

        $this->assertResonseStatusIs(400);

        $this->assertResponseContentIsJson();

        $expectedPostObject = (object) [
            'title' => 'Title cannot be longer than 255 characters'
        ];

        $responseAsJsonString = $this->getResponseContent();
        $actualPostObject = json_decode($responseAsJsonString);

        $this->assertEquals($expectedPostObject, $actualPostObject);
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

        $this->assertResonseStatusIs(400);

        $this->assertResponseContentIsJson();

        $expectedPostObject = (object) [
            'content' => 'Content cannot be longer than 1023 characters'
        ];

        $responseAsJsonString = $this->getResponseContent();
        $actualPostObject = json_decode($responseAsJsonString);

        $this->assertEquals($expectedPostObject, $actualPostObject);
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

        $expectedPostObject = (object) [
            'id' => null,
            'createdAt' => "2023-11-29T20:46:04+00:00",
            'updatedAt' => "2023-11-29T20:46:04+00:00",
            'slug' => "some-post-fixture-2",
            'title' => "Some post fixture 2",
            'content' => "Some text of some post fixture 2."
        ];

        $responseAsJsonString = $this->getResponseContent();
        $actualPostObject = json_decode($responseAsJsonString);

        $this->assertEquals($expectedPostObject, $actualPostObject);
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
