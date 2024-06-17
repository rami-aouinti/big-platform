<?php

declare(strict_types=1);

namespace App\Admin\Resume\Controller;

use App\Resume\Domain\Entity\Skill;
use App\Resume\Infrastructure\Repository\SkillRepository;
use App\Resume\Transport\Form\SkillType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SkillController
 *
 * @package App\Resume\Transport\Controller\BackOffice
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[Route('/admin/skills')]
class SkillController extends AbstractController
{
    #[Route('/', name: 'skill_manage', methods: ['GET'])]
    public function manage(SkillRepository $skillRepository): Response
    {
        $skills = $skillRepository->findAll();

        return $this->render('back_office/skill/manage.html.twig', [
            'skills' => $skills,
        ]);
    }

    #[Route('/create', name: 'skill_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $skill = new Skill();
        $form = $this->createForm(SkillType::class, $skill)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($skill);
            $entityManager->flush();
            $this->addFlash('success', 'La compétence a été ajoutée avec succès !');

            return $this->redirectToRoute('skill_manage');
        }

        return $this->render('back_office/skill/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/update', name: 'skill_update', methods: ['GET', 'POST'])]
    public function update(Skill $skill, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SkillType::class, $skill)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'La compétence a été modifiée avec succès !');

            return $this->redirectToRoute('skill_manage');
        }

        return $this->render('back_office/skill/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'skill_delete', methods: ['POST'])]
    public function delete(Skill $skill, EntityManagerInterface $entityManager): RedirectResponse
    {
        $entityManager->remove($skill);
        $entityManager->flush();
        $this->addFlash('success', 'La compétence a été supprimée avec succès !');

        return $this->redirectToRoute('skill_manage');
    }
}
