<?php

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;

class PostControllerTest extends WebTestCase
{
    private AbstractBrowser $client;

    private const API_URI_PREFIX = '/api/';

    public function testListPosts()
    {
        $this->sendRequest(
            method: 'GET',
            uri: self::buildApiUri('posts')
        );

        $this->assertEquals(
            200,
            $this->getResponseStatusCode()
        );

        $this->assertJsonStringEqualsJsonString(
            '{"message": "RADS: list_posts"}',
            $this->getResponseContent()
        );
    }

    public function testShowPost()
    {
        $postId = 1;

        $this->sendRequest(
            method: 'GET',
            uri: self::buildApiUri("posts/{$postId}")
        );

        $this->assertEquals(
            200,
            $this->getResponseStatusCode()
        );

        $this->assertJsonStringEqualsJsonString(
            '{"message": "RADS: show_post"}',
            $this->getResponseContent()
        );
    }

    public function testCreatePost()
    {
        $creationDateTime = new DateTime();
        $textualCreationDateTime = $creationDateTime->format('Y-m-d H:i:s');

        $this->sendRequest(
            method: 'POST',
            uri: self::buildApiUri("posts"),
            parameters: [
                'createdAt' => $textualCreationDateTime,
                'updatedAt' => $textualCreationDateTime,
                'slug' => 'some-post',
                'title' => 'Some post',
                'contnet' => 'Some text of some post.',
            ]
        );

        $this->assertEquals(
            200,
            $this->getResponseStatusCode()
        );

        $this->assertJsonStringEqualsJsonString(
            '{"message": "RADS: create_post"}',
            $this->getResponseContent()
        );
    }

    public function testUpdatePost()
    {
        $postId = 1;

        $creationDateTime = new DateTime();
        $textualCreationDateTime = $creationDateTime->format('Y-m-d H:i:s');

        $this->sendRequest(
            method: 'PUT',
            uri: self::buildApiUri("posts/{$postId}"),
            parameters: [
                'createdAt' => $textualCreationDateTime,
                'updatedAt' => $textualCreationDateTime,
                'slug' => 'some-post-updated',
                'title' => 'Some post updated',
                'contnet' => 'Some updated text of some updated post.',
            ]
        );

        $this->assertEquals(
            200,
            $this->getResponseStatusCode()
        );

        $this->assertJsonStringEqualsJsonString(
            '{"message": "RADS: update_post"}',
            $this->getResponseContent()
        );
    }

    public function testDeletePost()
    {
        $postId = 1;

        $this->sendRequest(
            method: 'DELETE',
            uri: self::buildApiUri("posts/{$postId}")
        );

        $this->assertEquals(
            200,
            $this->getResponseStatusCode()
        );

        $this->assertJsonStringEqualsJsonString(
            '{"message": "RADS: delete_post"}',
            $this->getResponseContent()
        );
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
     * @return int
     */
    private function getResponseStatusCode(): int
    {
        return $this->client->getResponse()->getStatusCode();
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
