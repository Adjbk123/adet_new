<?php

namespace App\Controller;

use App\Repository\AnneeAcademiqueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/annee_academique')]
final class AnneeAcademiqueController extends AbstractController
{
    #[Route('/list', name: 'app_annee_academique_list', methods: ['GET'])]
    public function list(AnneeAcademiqueRepository $repo): JsonResponse
    {

        $noms = array_map(fn($v) => $v->getLibelle(), $repo->findAll());
        return new JsonResponse($noms);
    }

   #[Route('/create_if_needed', name: 'app_annee_academique_create_if_needed', methods: ['POST'])]
    public function createIfNeeded(Request $request, EntityManagerInterface $em, AnneeAcademiqueRepository $repo): JsonResponse
    {

        $data = json_decode($request->getContent(), true);
        $nom = $data['nom'] ?? null;
        if (!$nom) return new JsonResponse(['error'=>'Nom manquant'], 400);
        $annee = $repo->findOneBy(['libelle'=>$nom]);
        if (!$annee) {
            $annee = new \App\Entity\AnneeAcademique();
            $annee->setLibelle($nom);
            $em->persist($annee);
            $em->flush();
        }
        return new JsonResponse(['nom'=>$annee->getLibelle()]);
    }
}
