<?php

namespace App\Controller;

use App\Entity\NiveauEtude;
use App\Form\NiveauEtudeType;
use App\Repository\NiveauEtudeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/niveau/etude')]
final class NiveauEtudeController extends AbstractController
{    #[IsGranted('ROLE_USER')]
    #[Route(name: 'app_niveau_etude_index', methods: ['GET'])]
    public function index(NiveauEtudeRepository $niveauEtudeRepository): Response
    {

        return $this->render('niveau_etude/index.html.twig', [
            'niveau_etudes' => $niveauEtudeRepository->findAll(),
        ]);
    }
  #[Route('/list', name: 'app_niveau_etude_list', methods: ['GET'])]
    public function list(NiveauEtudeRepository $repo): JsonResponse
    {

        $noms = array_map(fn($v) => $v->getLibelle(), $repo->findAll());
        return new JsonResponse($noms);
    }
    #[Route('/create_if_needed', name: 'app_niveau_etude_create_if_needed', methods: ['POST'])]
    public function createIfNeeded(Request $request, EntityManagerInterface $em, NiveauEtudeRepository $repo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $nom = $data['nom'] ?? null;
        if (!$nom) return new JsonResponse(['error'=>'Nom manquant'], 400);
        $niveau = $repo->findOneBy(['libelle'=>$nom]);
        if (!$niveau) {
            $niveau = new \App\Entity\NiveauEtude();
            $niveau->setLibelle($nom);
            $em->persist($niveau);
            $em->flush();
        }
        return new JsonResponse(['nom'=>$niveau->getLibelle()]);
    }
    #[Route('/new', name: 'app_niveau_etude_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $niveauEtude = new NiveauEtude();
        $form = $this->createForm(NiveauEtudeType::class, $niveauEtude);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($niveauEtude);
            $entityManager->flush();

            return $this->redirectToRoute('app_niveau_etude_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('niveau_etude/new.html.twig', [
            'niveau_etude' => $niveauEtude,
            'form' => $form,
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}', name: 'app_niveau_etude_show', methods: ['GET'])]
    public function show(NiveauEtude $niveauEtude): Response
    {

        return $this->render('niveau_etude/show.html.twig', [
            'niveau_etude' => $niveauEtude,
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}/edit', name: 'app_niveau_etude_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, NiveauEtude $niveauEtude, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(NiveauEtudeType::class, $niveauEtude);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_niveau_etude_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('niveau_etude/edit.html.twig', [
            'niveau_etude' => $niveauEtude,
            'form' => $form,
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}', name: 'app_niveau_etude_delete', methods: ['POST'])]
    public function delete(Request $request, NiveauEtude $niveauEtude, EntityManagerInterface $entityManager): Response
    {

        if ($this->isCsrfTokenValid('delete'.$niveauEtude->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($niveauEtude);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_niveau_etude_index', [], Response::HTTP_SEE_OTHER);
    }




}
