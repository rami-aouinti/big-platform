<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @package App\User\Domain\Entity
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[ORM\Embeddable]
class Address
{
    final public const string SET_USER_PROFILE = 'set.UserProfile';
    final public const string SET_USER_BASIC = 'set.UserBasic';

    #[ORM\Column(
        type: 'string',
        length: 255
    )]
    #[Groups([
        'User',
        'User.country',

        self::SET_USER_PROFILE,
        self::SET_USER_BASIC,
    ])]
    public string $country;

    #[ORM\Column(
        type: 'string',
        length: 255
    )]
    #[Groups([
        'User',
        'User.city',

        self::SET_USER_PROFILE,
        self::SET_USER_BASIC,
    ])]
    public string $city;

    #[ORM\Column(
        type: 'string',
        length: 255
    )]
    #[Groups([
        'User',
        'User.postcode',

        self::SET_USER_PROFILE,
        self::SET_USER_BASIC,
    ])]
    public string $postcode;

    #[ORM\Column(
        type: 'string',
        length: 255
    )]
    #[Groups([
        'User',
        'User.street',

        self::SET_USER_PROFILE,
        self::SET_USER_BASIC,
    ])]
    public string $street;

    #[ORM\Column(
        type: 'string',
        length: 255
    )]
    #[Groups([
        'User',
        'User.houseNumber',

        self::SET_USER_PROFILE,
        self::SET_USER_BASIC,
    ])]
    public string $houseNumber;

    public function __construct(
        string $country,
        string $city,
        string $postcode,
        string $street,
        string $houseNumber
    ) {
        $this->country = $country;
        $this->city = $city;
        $this->postcode = $postcode;
        $this->street = $street;
        $this->houseNumber = $houseNumber;
    }

    public function getCountry(): string
    {
        return $this->country;
    }
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }
    public function getCity(): string
    {
        return $this->city;
    }
    public function setCity(string $city): void
    {
        $this->city = $city;
    }
    public function getPostcode(): string
    {
        return $this->postcode;
    }
    public function setPostcode(string $postcode): void
    {
        $this->postcode = $postcode;
    }
    public function getStreet(): string
    {
        return $this->street;
    }
    public function setStreet(string $street): void
    {
        $this->street = $street;
    }
    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }
    public function setHouseNumber(string $houseNumber): void
    {
        $this->houseNumber = $houseNumber;
    }
}
