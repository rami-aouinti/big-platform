<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use App\Crm\Domain\Entity\Team;
use App\Crm\Domain\Repository\Query\TeamQuery;
use App\Crm\Domain\Repository\TeamRepository;
use App\User\Domain\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<Team>
 */
final class TeamType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Team::class,
            'label' => 'team',
            'teamlead_only' => true,
            'choice_label' => function (Team $team) {
                return $team->getName();
            },
            'documentation' => [
                'type' => 'integer',
                'description' => 'Team ID',
            ],
        ]);

        $resolver->setDefault('query_builder', function (Options $options) {
            return function (TeamRepository $repo) use ($options) {
                /** @var User $user */
                $user = $options['user'];
                $query = new TeamQuery();
                $query->setCurrentUser($user);

                if (!$options['teamlead_only']) {
                    $query->setTeams($user->getTeams());
                }

                return $repo->getQueryBuilderForFormType($query);
            };
        });
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
