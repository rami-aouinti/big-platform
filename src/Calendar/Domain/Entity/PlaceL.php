<?php

declare(strict_types=1);

/*
 * This file is part of the bjoern-hempel/php-calendar-api project.
 *
 * (c) Björn Hempel <https://www.hempel.li/>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Calendar\Domain\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Calendar\Domain\Entity\Trait\TimestampsTrait;
use App\Calendar\Domain\Repository\PlaceLRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity class Place L
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.1 (2022-11-21)
 * @since 0.1.1 (2022-11-21) Update to symfony 6.1
 * @since 0.1.0 (2022-05-20) First version.
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: PlaceLRepository::class)]
#[ORM\Table('platform_calendar_place_l')]
#[ORM\Index(columns: ['coordinate'], name: 'coordinate_place_l', flags: ['spatial'])]
#[ApiResource]
class PlaceL extends Place
{
    use TimestampsTrait;
}
