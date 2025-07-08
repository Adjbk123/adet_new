<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Form\EtudiantType;
use App\Repository\EtudiantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/etudiant')]
final class EtudiantController extends AbstractController
{
    #[Route(name: 'app_etudiant_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(EtudiantRepository $etudiantRepository): Response
    {
        return $this->render('etudiant/index.html.twig', [
            'etudiants' => $etudiantRepository->findAll(),
        ]);
    }
    #[Route('/inscription_ajax', name: 'app_etudiant_inscription_ajax', methods: ['POST'])]
    public function inscriptionAjax(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) return new JsonResponse(['success'=>false, 'message'=>'Données invalides'], 400);

        // Vérification des champs obligatoires
        $required = ['nom','prenoms','sexe','dateNaissance','lieuNaissance','telephone','email','village','niveauEtude','filiere','etablissement','anneeAcademique','nomContact','numeroTelephone','isParticipeActivite','isSouhaiteDevenirMembre','isSouhaiteIntegrerCommission'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field]==='') {
                return new JsonResponse(['success'=>false, 'message'=>'Champ obligatoire manquant : '.$field], 400);
            }
        }

        // Création ou récupération des entités dynamiques
        $village = $em->getRepository(\App\Entity\Village::class)->findOneBy(['nom'=>$data['village']['nom']??$data['village']]) ?? null;
        if (!$village && !empty($data['village']['nom'])) {
            $village = new \App\Entity\Village();
            $village->setNom($data['village']['nom']);
            $em->persist($village);
        }
        $filiere = $em->getRepository(\App\Entity\Filiere::class)->findOneBy(['nom'=>$data['filiere']['nom']??$data['filiere']]) ?? null;
        if (!$filiere && !empty($data['filiere']['nom'])) {
            $filiere = new \App\Entity\Filiere();
            $filiere->setNom($data['filiere']['nom']);
            $em->persist($filiere);
        }
        $etablissement = $em->getRepository(\App\Entity\Etablissement::class)->findOneBy(['nom'=>$data['etablissement']['nom']??$data['etablissement']]) ?? null;
        if (!$etablissement && !empty($data['etablissement']['nom'])) {
            $etablissement = new \App\Entity\Etablissement();
            $etablissement->setNom($data['etablissement']['nom']);
            $em->persist($etablissement);
        }
        $commission = null;
        if (!empty($data['commission'])) {
            $commission = $em->getRepository(\App\Entity\Commission::class)->findOneBy(['libelle'=>$data['commission']['libelle']??$data['commission']]) ?? null;
            if (!$commission && !empty($data['commission']['libelle'])) {
                $commission = new \App\Entity\Commission();
                $commission->setLibelle($data['commission']['libelle']);
                $em->persist($commission);
            }
        }
        $niveau = $em->getRepository(\App\Entity\NiveauEtude::class)->findOneBy(['libelle'=>$data['niveauEtude']['libelle']??$data['niveauEtude']]) ?? null;
        if (!$niveau && !empty($data['niveauEtude']['libelle'])) {
            $niveau = new \App\Entity\NiveauEtude();
            $niveau->setLibelle($data['niveauEtude']['libelle']);
            $em->persist($niveau);
        }
        $annee = $em->getRepository(\App\Entity\AnneeAcademique::class)->findOneBy(['libelle'=>$data['anneeAcademique']['libelle']??$data['anneeAcademique']]) ?? null;
        if (!$annee && !empty($data['anneeAcademique']['libelle'])) {
            $annee = new \App\Entity\AnneeAcademique();
            $annee->setLibelle($data['anneeAcademique']['libelle']);
            $em->persist($annee);
        }

        // Création de l'information académique
        $infoA = new \App\Entity\InformationAcademique();
        $infoA->setNiveauEtude($niveau);
        $infoA->setFiliere($filiere);
        $infoA->setEtablissement($etablissement);
        $infoA->setAnneeAcademique($annee);
        $infoA->setCreatedAt(new \DateTimeImmutable());
        $em->persist($infoA);

        // Création de l'information sociale (un seul contact)
        $infoS = new \App\Entity\InformationSociale();
        $infoS->setNomContact($data['nomContact']??null);
        $infoS->setNumeroTelephone($data['numeroTelephone']??null);
        $em->persist($infoS);

        // Création de l'engagement
        $engagement = new \App\Entity\Engagement();
        $engagement->setIsParticipeActivite((bool)$data['isParticipeActivite']);
        $engagement->setIsSouhaiteDevenirMembre((bool)$data['isSouhaiteDevenirMembre']);
        $engagement->setIsSouhaiteIntegrerCommission((bool)$data['isSouhaiteIntegrerCommission']);
        $engagement->setCommission($commission);
        $em->persist($engagement);

        // Création de l'étudiant
        $etudiant = new \App\Entity\Etudiant();
        $etudiant->setNom($data['nom']??null);
        $etudiant->setPrenoms($data['prenoms']??null);
        $etudiant->setSexe($data['sexe']??null);
        $etudiant->setDateNaissance(new \DateTime($data['dateNaissance']??'now'));
        $etudiant->setLieuNaissance($data['lieuNaissance']??null);
        $etudiant->setVillage($village);
        $etudiant->setTelephone($data['telephone']??null);
        $etudiant->setEmail($data['email']??null);
        $em->persist($etudiant);

        // Liaisons
        $infoA->setEtudiant($etudiant);
        $infoS->setEtudiant($etudiant);
        $engagement->setEtudiant($etudiant);

        $em->flush();
        return new JsonResponse(['success'=>true]);
    }

    #[Route('/new', name: 'app_etudiant_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $etudiant = new Etudiant();
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($etudiant);
            $entityManager->flush();

            return $this->redirectToRoute('app_etudiant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etudiant/new.html.twig', [
            'etudiant' => $etudiant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etudiant_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Etudiant $etudiant): Response
    {
        return $this->render('etudiant/show.html.twig', [
            'etudiant' => $etudiant,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_etudiant_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Etudiant $etudiant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_etudiant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etudiant/edit.html.twig', [
            'etudiant' => $etudiant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etudiant_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Etudiant $etudiant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$etudiant->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($etudiant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_etudiant_index', [], Response::HTTP_SEE_OTHER);
    }


}
