<?php

namespace App\Controller;

use App\Entity\Etablissement;
use App\Form\EtablissementType;
use App\Repository\EtablissementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/etablissement')]
final class EtablissementController extends AbstractController
{
    #[Route(name: 'app_etablissement_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(EtablissementRepository $etablissementRepository): Response
    {
        return $this->render('etablissement/index.html.twig', [
            'etablissements' => $etablissementRepository->findAll(),
        ]);
    }
    #[Route('/list', name: 'app_etablissement_list', methods: ['GET'])]
    public function list(EtablissementRepository $repo): JsonResponse
    {
        $noms = array_map(fn($v) => $v->getNom(), $repo->findAll());
        return new JsonResponse($noms);
    }

    #[Route('/create_if_needed', name: 'app_etablissement_create_if_needed', methods: ['POST'])]
    public function createIfNeeded(Request $request, EntityManagerInterface $em, EtablissementRepository $repo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $nom = $data['nom'] ?? null;
        if (!$nom) return new JsonResponse(['error'=>'Nom manquant'], 400);
        $etablissement = $repo->findOneBy(['nom'=>$nom]);
        if (!$etablissement) {
            $etablissement = new \App\Entity\Etablissement();
            $etablissement->setNom($nom);
            $em->persist($etablissement);
            $em->flush();
        }
        return new JsonResponse(['nom'=>$etablissement->getNom()]);
    }
    #[Route('/new', name: 'app_etablissement_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $etablissement = new Etablissement();
        $form = $this->createForm(EtablissementType::class, $etablissement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($etablissement);
            $entityManager->flush();

            return $this->redirectToRoute('app_etablissement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etablissement/new.html.twig', [
            'etablissement' => $etablissement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etablissement_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Etablissement $etablissement): Response
    {
        return $this->render('etablissement/show.html.twig', [
            'etablissement' => $etablissement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_etablissement_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Etablissement $etablissement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EtablissementType::class, $etablissement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_etablissement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etablissement/edit.html.twig', [
            'etablissement' => $etablissement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etablissement_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Etablissement $etablissement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$etablissement->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($etablissement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_etablissement_index', [], Response::HTTP_SEE_OTHER);
    }



}
