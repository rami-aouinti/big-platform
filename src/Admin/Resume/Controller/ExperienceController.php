<?php

declare(strict_types=1);

namespace App\Admin\Resume\Controller;

use App\Resume\Domain\Entity\Experience;
use App\Resume\Infrastructure\Repository\ExperienceRepository;
use App\Resume\Transport\Form\ExperienceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ExperienceController
 *
 * @package App\Resume\Transport\Controller\BackOffice
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[Route('/admin/experience')]
class ExperienceController extends AbstractController
{
    #[Route('/', name: 'app_experience_index', methods: ['GET'])]
    public function index(ExperienceRepository $experienceRepository): Response
    {
        return $this->render('back_office/experience/index.html.twig', [
            'experiences' => $experienceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_experience_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $experience = new Experience();
        $form = $this->createForm(ExperienceType::class, $experience);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($experience);
            $entityManager->flush();

            return $this->redirectToRoute('app_experience_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back_office/experience/new.html.twig', [
            'experience' => $experience,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_experience_show', methods: ['GET'])]
    public function show(Experience $experience): Response
    {
        return $this->render('back_office/experience/show.html.twig', [
            'experience' => $experience,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_experience_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Experience $experience, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExperienceType::class, $experience);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_experience_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back_office/experience/edit.html.twig', [
            'experience' => $experience,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_experience_delete', methods: ['POST'])]
    public function delete(Request $request, Experience $experience, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $experience->getId(), $request->request->get('_token'))) {
            $entityManager->remove($experience);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_experience_index', [], Response::HTTP_SEE_OTHER);
    }
}
