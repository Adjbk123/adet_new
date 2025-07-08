<?php

namespace App\Controller;

use App\Entity\Commission;
use App\Form\CommissionType;
use App\Repository\CommissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/commission')]
final class CommissionController extends AbstractController
{

    #[IsGranted('ROLE_USER')]
    #[Route(name: 'app_commission_index', methods: ['GET'])]
    public function index(CommissionRepository $commissionRepository): Response
    {

        return $this->render('commission/index.html.twig', [
            'commissions' => $commissionRepository->findAll(),
        ]);
    }
    #[Route('/list', name: 'app_commission_list', methods: ['GET'])]
    public function list(CommissionRepository $repo): JsonResponse
    {
        $noms = array_map(fn($v) => $v->getNom(), $repo->findAll());
        return new JsonResponse($noms);
    }

    #[Route('/create_if_needed', name: 'app_commission_create_if_needed', methods: ['POST'])]
    public function createIfNeeded(Request $request, EntityManagerInterface $em, CommissionRepository $repo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $nom = $data['nom'] ?? null;
        if (!$nom) return new JsonResponse(['error'=>'Nom manquant'], 400);
        $commission = $repo->findOneBy(['nom'=>$nom]);
        if (!$commission) {
            $commission = new \App\Entity\Commission();
            $commission->setLibelle($nom);
            $em->persist($commission);
            $em->flush();
        }
        return new JsonResponse(['nom'=>$commission->getNom()]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/new', name: 'app_commission_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $commission = new Commission();
        $form = $this->createForm(CommissionType::class, $commission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commission);
            $entityManager->flush();

            return $this->redirectToRoute('app_commission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commission/new.html.twig', [
            'commission' => $commission,
            'form' => $form,
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}', name: 'app_commission_show', methods: ['GET'])]
    public function show(Commission $commission): Response
    {

        return $this->render('commission/show.html.twig', [
            'commission' => $commission,
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}/edit', name: 'app_commission_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commission $commission, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(CommissionType::class, $commission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commission/edit.html.twig', [
            'commission' => $commission,
            'form' => $form,
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}', name: 'app_commission_delete', methods: ['POST'])]
    public function delete(Request $request, Commission $commission, EntityManagerInterface $entityManager): Response
    {

        if ($this->isCsrfTokenValid('delete'.$commission->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($commission);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commission_index', [], Response::HTTP_SEE_OTHER);
    }


}
