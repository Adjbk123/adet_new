<?php

$controllers = [
    'src/Controller/FiliereController.php',
    'src/Controller/NiveauEtudeController.php',
    'src/Controller/CommissionController.php',
    'src/Controller/VillageController.php',
    'src/Controller/AnneeAcademiqueController.php'
];

foreach ($controllers as $controller) {
    $content = file_get_contents($controller);
    
    // Corriger le placement des annotations IsGranted
    $content = preg_replace(
        '/#\[Route[^]]*\][^}]*public function [^{]*{\s*#[^G]*IsGranted[^}]*}/',
        function($match) {
            // Extraire la ligne Route
            preg_match('/#\[Route[^]]*\][^}]*public function [^{]*{/', $match, $routeMatch);
            $routeLine = $routeMatch[0];
            
            // Retourner avec IsGranted correctement placé
            return $routeLine . "\n    #[IsGranted('ROLE_USER')]" . "\n    {";
        },
        $content
    );
    
    file_put_contents($controller, $content);
    echo "Corrigé: $controller\n";
}

echo "Tous les contrôleurs ont été corrigés\n"; 