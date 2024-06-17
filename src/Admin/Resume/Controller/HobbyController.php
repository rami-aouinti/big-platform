<?php

declare(strict_types=1);

namespace App\Admin\Resume\Controller;

use App\Resume\Domain\Entity\Hobby;
use App\Resume\Infrastructure\Repository\HobbyRepository;
use App\Resume\Transport\Form\HobbyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

/**
 * Class HobbyController
 *
 * @package App\Resume\Transport\Controller\BackOffice
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[Route('/admin/hobby')]
class HobbyController extends AbstractController
{
    #[Route('/', name: 'app_hobby_index', methods: ['GET'])]
    public function index(HobbyRepository $hobbyRepository): Response
    {
        return $this->render('back_office/hobby/index.html.twig', [
            'hobbies' => $hobbyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_hobby_new', methods: ['GET', 'POST'])]
    public function new(
        #[CurrentUser]
        $user,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $hobby = new Hobby();
        $hobby->setUser($user);
        $form = $this->createForm(HobbyType::class, $hobby);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($hobby);
            $entityManager->flush();

            return $this->redirectToRoute('app_hobby_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back_office/hobby/new.html.twig', [
            'hobby' => $hobby,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hobby_show', methods: ['GET'])]
    public function show(Hobby $hobby): Response
    {
        return $this->render('back_office/hobby/show.html.twig', [
            'hobby' => $hobby,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_hobby_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Hobby $hobby, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HobbyType::class, $hobby);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_hobby_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back_office/hobby/edit.html.twig', [
            'hobby' => $hobby,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hobby_delete', methods: ['POST'])]
    public function delete(Request $request, Hobby $hobby, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $hobby->getId(), $request->request->get('_token'))) {
            $entityManager->remove($hobby);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_hobby_index', [], Response::HTTP_SEE_OTHER);
    }
}
