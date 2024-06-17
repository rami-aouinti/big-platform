<?php

declare(strict_types=1);

namespace App\Admin\Resume\Controller;

use App\Resume\Domain\Entity\Formation;
use App\Resume\Infrastructure\Repository\FormationRepository;
use App\Resume\Transport\Form\FormationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FormationController
 *
 * @package App\Resume\Transport\Controller\BackOffice
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[Route('/admin/formations')]
class FormationController extends AbstractController
{
    #[Route('/', name: 'formation_manage', methods: ['GET'])]
    public function manage(FormationRepository $formationRepository): Response
    {
        $formations = $formationRepository->findAll();

        return $this->render('back_office/formation/manage.html.twig', [
            'formations' => $formations,
        ]);
    }

    #[Route('/create', name: 'formation_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($formation);
            $entityManager->flush();
            $this->addFlash('success', 'La formation a été ajoutée avec succès !');

            return $this->redirectToRoute('formation_manage');
        }

        return $this->render('back_office/formation/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/update', name: 'formation_update', methods: ['GET', 'POST'])]
    public function update(Formation $formation, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FormationType::class, $formation)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'La formation a été modifiée avec succès !');

            return $this->redirectToRoute('formation_manage');
        }

        return $this->render('back_office/formation/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'formation_delete', methods: ['POST'])]
    public function delete(Formation $formation, EntityManagerInterface $entityManager): RedirectResponse
    {
        $entityManager->remove($formation);
        $entityManager->flush();
        $this->addFlash('success', 'La formation a été supprimée avec succès !');

        return $this->redirectToRoute('formation_manage');
    }
}
