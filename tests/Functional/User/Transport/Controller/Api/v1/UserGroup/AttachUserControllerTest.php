<?php

declare(strict_types=1);

namespace App\Tests\Functional\User\Transport\Controller\Api\v1\UserGroup;

use App\General\Domain\Utils\JSON;
use App\General\Transport\Utils\Tests\WebTestCase;
use App\Role\Domain\Enum\Role;
use App\User\Application\Resource\UserGroupResource;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use App\User\Domain\Entity\UserGroup;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class AttachUserControllerTest
 *
 * @package App\Tests
 */
class AttachUserControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/v1/user_group';
    private UserGroup $userGroup;
    private User $userForAttach;
    private UserResource $userResource;
    private UserGroupResource $userGroupResource;

    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();

        $userGroupResource = static::getContainer()->get(UserGroupResource::class);
        $userResource = static::getContainer()->get(UserResource::class);
        self::assertInstanceOf(UserGroupResource::class, $userGroupResource);
        self::assertInstanceOf(UserResource::class, $userResource);
        $this->userGroupResource = $userGroupResource;
        $this->userResource = $userResource;
        /** @var UserGroup|null $userGroup */
        $userGroup = $this->userGroupResource->findOneBy([
            'role' => Role::LOGGED->value,
        ]);
        self::assertInstanceOf(UserGroup::class, $userGroup);
        $this->userGroup = $userGroup;
        // let's check that before running test the userGroup has only 1 attached user
        self::assertEquals(1, $this->userGroup->getUsers()->count());
        $user = $this->userGroup->getUsers()->first();
        self::assertInstanceOf(User::class, $user);
        self::assertEquals('john-logged', $user->getUsername());
        $userForAttach = $this->userResource->findOneBy([
            'username' => 'john-user',
        ]);
        self::assertInstanceOf(User::class, $userForAttach);
        $this->userForAttach = $userForAttach;
    }

    /**
     * @testdox Test that `POST /api/v1/user_group/{groupId}/user/{userId}` under the root user returns success.
     *
     * @throws Throwable
     */
    public function testThatAttachUserToTheUserGroupUnderRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $client->request('POST', $this->baseUrl . '/' . $this->userGroup->getId() . '/user/'
            . $this->userForAttach->getId());
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertIsArray($responseData);
        self::assertCount(2, $responseData);
        foreach ($responseData as $user) {
            self::assertIsArray($user);
            self::assertArrayHasKey('id', $user);
            self::assertArrayHasKey('username', $user);
            self::assertContains($user['username'], ['john-logged', $this->userForAttach->getUsername()]);
            self::assertArrayHasKey('firstName', $user);
            self::assertArrayHasKey('lastName', $user);
            self::assertArrayHasKey('email', $user);
            self::assertArrayHasKey('language', $user);
            self::assertArrayHasKey('locale', $user);
            self::assertArrayHasKey('timezone', $user);
        }

        // let's check that inside database we have the same data as in response above
        /** @var UserGroup|null $userGroup */
        $userGroup = $this->userGroupResource->findOne($this->userGroup->getId());
        self::assertInstanceOf(UserGroup::class, $userGroup);
        self::assertEquals(2, $userGroup->getUsers()->count());

        // cleanup our actions above in order to have only 1 attached user to the user group
        /** @var User|null $userForAttach */
        $userForAttach = $this->userResource->findOneBy([
            'username' => $this->userForAttach->getUsername(),
        ]);
        self::assertInstanceOf(User::class, $userForAttach);
        $userGroup = $this->userGroupResource->save($userGroup->removeUser($userForAttach), false);
        $this->userResource->save($userForAttach, true, true);
        self::assertEquals(1, $userGroup->getUsers()->count());
    }

    /**
     * @testdox Test that `POST /api/v1/user_group/{groupId}/user/{userId}` under the non-root user returns error.
     *
     * @throws Throwable
     */
    public function testThatAttachUserToTheUserGroupUnderNonRootUserReturnsErrorResponse(): void
    {
        $client = $this->getTestClient('john-admin', 'password-admin');

        $client->request('POST', $this->baseUrl . '/' . $this->userGroup->getId() . '/user/'
            . $this->userForAttach->getId());
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode(), "Response:\n" . $response);

        // let's check that inside database we have the same data as before request
        /** @var UserGroup|null $userGroup */
        $userGroup = $this->userGroupResource->findOneBy([
            'role' => Role::LOGGED->value,
        ]);
        self::assertInstanceOf(UserGroup::class, $userGroup);
        self::assertEquals(1, $userGroup->getUsers()->count());
    }
}
