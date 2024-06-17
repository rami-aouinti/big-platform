<?php

declare(strict_types=1);

namespace App\User\Infrastructure\DataFixtures\ORM;

use App\Crm\Domain\Entity\AccessToken;
use App\Crm\Domain\Entity\UserPreference;
use App\General\Domain\Enum\Language;
use App\General\Domain\Enum\Locale;
use App\General\Domain\Rest\UuidHelper;
use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\Address;
use App\User\Domain\Entity\Enum\SexEnum;
use App\User\Domain\Entity\User;
use App\User\Domain\Entity\UserGroup;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Throwable;

use function array_map;

/**
 * @package App\User
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
#[AutoconfigureTag('doctrine.fixture.orm')]
final class LoadUserData extends Fixture implements OrderedFixtureInterface
{
    public const int MIN_RATE = 30;
    public const int MAX_RATE = 120;
    /**
     * @var array<string, string>
     */
    public static array $uuids = [
        'john' => '20000000-0000-1000-8000-000000000001',
        'john-logged' => '20000000-0000-1000-8000-000000000002',
        'john-api' => '20000000-0000-1000-8000-000000000003',
        'john-user' => '20000000-0000-1000-8000-000000000004',
        'john-admin' => '20000000-0000-1000-8000-000000000005',
        'john-root' => '20000000-0000-1000-8000-000000000006',
    ];

    public function __construct(
        private readonly RolesServiceInterface $rolesService,
    ) {
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @throws Throwable
     */
    public function load(ObjectManager $manager): void
    {
        // Create entities
        array_map(
            fn (?string $role): bool => $this->createUser($manager, $role),
            [
                null,
                ...$this->rolesService->getRoles(),
            ],
        );
        // Flush database changes
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     */
    public function getOrder(): int
    {
        return 3;
    }

    public static function getUuidByKey(string $key): string
    {
        return self::$uuids[$key];
    }

    /**
     * Method to create User entity with specified role.
     *
     * @throws Throwable
     */
    private function createUser(ObjectManager $manager, ?string $role = null): bool
    {
        $address = $this->createAddress();
        $suffix = $role === null ? '' : '-' . $this->rolesService->getShort($role);
        // Create new entity
        $entity = (new User())
            ->setUsername('john' . $suffix)
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setEmail('john.doe' . $suffix . '@test.com')
            ->setLanguage(Language::EN)
            ->setLocale(Locale::EN)
            ->setLastLogin(new \DateTime('now'))
            ->setEnabled(true)
            ->setPlainPassword('password' . $suffix);

        $entity->setDescription('Hi, I’m john.doe' . $suffix . ', Decisions: If you can’t decide, the answer is no.
             If two equally difficult paths, choose the one more painful in the short term (pain avoidance is creating an illusion of equality).');
        $entity->setPhone('+4999999999999');
        $entity->setBirthday(new DateTime('now'));
        $entity->setSex(SexEnum::Male);
        $entity->setAddress($address);
        $entity->setGoogleUrl('google_id');
        $entity->setInstagramUrl('instagram_id');
        $entity->setFacebookUrl('facebook_id');
        $entity->setTweeterUrl('twitter_id');

        $entity->setFullName('john' . $suffix);
        $entity->setAlias('john' . $suffix);
        $entity->setAvatar('john' . $suffix . '.png');
        if ($role !== null) {
            /** @var UserGroup $userGroup */
            $userGroup = $this->getReference('UserGroup-' . $this->rolesService->getShort($role), UserGroup::class);
            $entity->addUserGroup($userGroup);
        }

        PhpUnitUtil::setProperty(
            'id',
            UuidHelper::fromString(self::$uuids['john' . $suffix]),
            $entity
        );

        $prefs = $this->getUserPreferences($entity);
        $entity->setPreferences($prefs);
        $manager->persist($prefs[0]);

        // Persist entity
        $manager->persist($entity);

        $accessToken = new AccessToken($entity, 'api_platform_' . $entity->getUsername());
        $accessToken->setName('Test fixture');
        $manager->persist($accessToken);
        // Create reference for later usage
        $this->addReference('User-' . $entity->getUsername(), $entity);

        return true;
    }

    private function getUserPreferences(User $user, string $timezone = null): array
    {
        $preferences = [];

        $prefHourlyRate = new UserPreference(UserPreference::HOURLY_RATE, rand(self::MIN_RATE, self::MAX_RATE));
        $user->addPreference($prefHourlyRate);
        $preferences[] = $prefHourlyRate;

        if ($timezone !== null) {
            $prefTimezone = new UserPreference(UserPreference::TIMEZONE, $timezone);
            $user->addPreference($prefTimezone);
            $preferences[] = $prefTimezone;
        }

        return $preferences;
    }

    private function createAddress(): Address
    {
        return new Address(
            'Germany',
            'Köln',
            '50859',
            'Widdersdorder landstr',
            '11'
        );
    }
}
