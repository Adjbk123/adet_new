<?php

namespace App\Controller;

use App\Entity\Filiere;
use App\Form\FiliereType;
use App\Repository\FiliereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/filiere')]
final class FiliereController extends AbstractController
{
    #[Route(name: 'app_filiere_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(FiliereRepository $filiereRepository): Response
    {
        return $this->render('filiere/index.html.twig', [
            'filieres' => $filiereRepository->findAll(),
        ]);
    }

    #[Route('/list', name: 'app_filiere_list', methods: ['GET'])]
    public function list(FiliereRepository $repo): JsonResponse
    {
        $noms = array_map(fn($v) => $v->getNom(), $repo->findAll());
        return new JsonResponse($noms);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/create_if_needed', name: 'app_filiere_create_if_needed', methods: ['POST'])]

    public function createIfNeeded(Request $request, EntityManagerInterface $em, FiliereRepository $repo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $nom = $data['nom'] ?? null;
        if (!$nom) return new JsonResponse(['error'=>'Nom manquant'], 400);
        $filiere = $repo->findOneBy(['nom'=>$nom]);
        if (!$filiere) {
            $filiere = new \App\Entity\Filiere();
            $filiere->setNom($nom);
            $em->persist($filiere);
            $em->flush();
        }
        return new JsonResponse(['nom'=>$filiere->getNom()]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/new', name: 'app_filiere_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $filiere = new Filiere();
        $form = $this->createForm(FiliereType::class, $filiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($filiere);
            $entityManager->flush();

            return $this->redirectToRoute('app_filiere_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('filiere/new.html.twig', [
            'filiere' => $filiere,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/{id}', name: 'app_filiere_show', methods: ['GET'])]
    public function show(Filiere $filiere): Response
    {

        return $this->render('filiere/show.html.twig', [
            'filiere' => $filiere,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/{id}/edit', name: 'app_filiere_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Filiere $filiere, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(FiliereType::class, $filiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_filiere_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('filiere/edit.html.twig', [
            'filiere' => $filiere,
            'form' => $form,
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}', name: 'app_filiere_delete', methods: ['POST'])]
    public function delete(Request $request, Filiere $filiere, EntityManagerInterface $entityManager): Response
    {

        if ($this->isCsrfTokenValid('delete'.$filiere->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($filiere);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_filiere_index', [], Response::HTTP_SEE_OTHER);
    }



}
