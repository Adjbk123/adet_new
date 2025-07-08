<?php

namespace App\Controller;

use App\Entity\Village;
use App\Form\VillageType;
use App\Repository\VillageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/village')]
final class VillageController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route(name: 'app_village_index', methods: ['GET'])]
    public function index(VillageRepository $villageRepository): Response
    {
        return $this->render('village/index.html.twig', [
            'villages' => $villageRepository->findAll(),
        ]);
    }



    #[Route('/list', name: 'app_village_list', methods: ['GET'])]
    public function list(VillageRepository $repo): JsonResponse
    {
        $noms = array_map(fn($v) => $v->getNom(), $repo->findAll());
        return new JsonResponse($noms);
    }

    #[Route('/create_if_needed', name: 'app_village_create_if_needed', methods: ['POST'])]
    public function createIfNeeded(Request $request, EntityManagerInterface $em, VillageRepository $repo): JsonResponse
    {

        $data = json_decode($request->getContent(), true);
        $nom = $data['nom'] ?? null;
        if (!$nom) return new JsonResponse(['error'=>'Nom manquant'], 400);
        $village = $repo->findOneBy(['nom'=>$nom]);
        if (!$village) {
            $village = new \App\Entity\Village();
            $village->setNom($nom);
            $em->persist($village);
            $em->flush();
        }
        return new JsonResponse(['nom'=>$village->getNom()]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/new', name: 'app_village_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $village = new Village();
        $form = $this->createForm(VillageType::class, $village);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($village);
            $entityManager->flush();

            return $this->redirectToRoute('app_village_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('village/new.html.twig', [
            'village' => $village,
            'form' => $form,
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}', name: 'app_village_show', methods: ['GET'])]
    public function show(Village $village): Response
    {

        return $this->render('village/show.html.twig', [
            'village' => $village,
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}/edit', name: 'app_village_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Village $village, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(VillageType::class, $village);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_village_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('village/edit.html.twig', [
            'village' => $village,
            'form' => $form,
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}', name: 'app_village_delete', methods: ['POST'])]
    public function delete(Request $request, Village $village, EntityManagerInterface $entityManager): Response
    {

        if ($this->isCsrfTokenValid('delete'.$village->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($village);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_village_index', [], Response::HTTP_SEE_OTHER);
    }



}
