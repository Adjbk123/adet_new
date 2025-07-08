<?php

namespace App\Controller;

use App\Repository\EtudiantRepository;
use App\Repository\EtablissementRepository;
use App\Repository\FiliereRepository;
use App\Repository\NiveauEtudeRepository;
use App\Repository\CommissionRepository;
use App\Repository\VillageRepository;
use App\Repository\AnneeAcademiqueRepository;
use App\Repository\EngagementRepository;
use App\Repository\InformationAcademiqueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard')]
#[IsGranted('ROLE_USER')]
final class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard', methods: ['GET'])]
    public function index(
        EtudiantRepository $etudiantRepository,
        EtablissementRepository $etablissementRepository,
        FiliereRepository $filiereRepository,
        NiveauEtudeRepository $niveauEtudeRepository,
        CommissionRepository $commissionRepository,
        VillageRepository $villageRepository,
        AnneeAcademiqueRepository $anneeAcademiqueRepository,
        EngagementRepository $engagementRepository,
        InformationAcademiqueRepository $infoAcademiqueRepository
    ): Response {
        // Statistiques générales
        $totalEtudiants = $etudiantRepository->count([]);
        $totalEtablissements = $etablissementRepository->count([]);
        $totalFilieres = $filiereRepository->count([]);
        $totalNiveaux = $niveauEtudeRepository->count([]);
        $totalCommissions = $commissionRepository->count([]);
        $totalVillages = $villageRepository->count([]);
        $totalAnnees = $anneeAcademiqueRepository->count([]);

        // Statistiques par genre
        $etudiantsMasculin = $etudiantRepository->count(['sexe' => 'Masculin']);
        $etudiantsFeminin = $etudiantRepository->count(['sexe' => 'Féminin']);

        // Statistiques d'engagement
        $participantsActivites = $engagementRepository->count(['isParticipeActivite' => true]);
        $souhaitantMembre = $engagementRepository->count(['isSouhaiteDevenirMembre' => true]);
        $souhaitantCommission = $engagementRepository->count(['isSouhaiteIntegrerCommission' => true]);

        // Top 5 des établissements
        $topEtablissements = $etablissementRepository->findTopEtablissements(5);

        // Top 5 des filières
        $topFilieres = $filiereRepository->findTopFilieres(5);

        // Top 5 des villages
        $topVillages = $villageRepository->findTopVillages(5);

        // Répartition par niveau d'étude
        $repartitionNiveaux = $niveauEtudeRepository->getRepartitionNiveaux();

        // Répartition par année académique
        $repartitionAnnees = $anneeAcademiqueRepository->getRepartitionAnnees();

        // Statistiques récentes (derniers 30 jours)
        $nouveauxEtudiants = $etudiantRepository->countNouveauxEtudiants(30);

        // Évolution mensuelle des inscriptions
        $evolutionMensuelle = $etudiantRepository->getEvolutionMensuelle();

        return $this->render('dashboard/index.html.twig', [
            'stats' => [
                'totalEtudiants' => $totalEtudiants,
                'totalEtablissements' => $totalEtablissements,
                'totalFilieres' => $totalFilieres,
                'totalNiveaux' => $totalNiveaux,
                'totalCommissions' => $totalCommissions,
                'totalVillages' => $totalVillages,
                'totalAnnees' => $totalAnnees,
                'etudiantsMasculin' => $etudiantsMasculin,
                'etudiantsFeminin' => $etudiantsFeminin,
                'participantsActivites' => $participantsActivites,
                'souhaitantMembre' => $souhaitantMembre,
                'souhaitantCommission' => $souhaitantCommission,
                'nouveauxEtudiants' => $nouveauxEtudiants,
            ],
            'topEtablissements' => $topEtablissements,
            'topFilieres' => $topFilieres,
            'topVillages' => $topVillages,
            'repartitionNiveaux' => $repartitionNiveaux,
            'repartitionAnnees' => $repartitionAnnees,
            'evolutionMensuelle' => $evolutionMensuelle,
        ]);
    }
} 