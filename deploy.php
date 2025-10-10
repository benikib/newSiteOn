<?php
// Activer le logging des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Logger les erreurs
file_put_contents('/home/u684456859/domains/bisika.menjidrc.com/deploy_errors.log',
    date('Y-m-d H:i:s')." - Démarrage\n",
    FILE_APPEND);

try {
   
    // // Votre code existant...
    // $secret = 'votre-secret';
    $payload = file_get_contents('php://input');
    $headers = getallheaders();

    file_put_contents('/home/u684456859/domains/bisika.menjidrc.com/deploy_errors.log',
        "Headers: ".print_r($headers, true)."\n",
        FILE_APPEND);

    // Exécution sécurisée
    $output = [];
    $returnCode = 0;
    exec('/usr/bin/bash /home/u684456859/domains/bisika.menjidrc.com/public_html/deploy.sh 2>&1', $output, $returnCode);

    file_put_contents('/home/u684456859/domains/bisika.menjidrc.com/deploy_errors.log',
        "Result: ".print_r($output, true)."\nReturn Code: $returnCode\n",
        FILE_APPEND);

    echo "Déploiement réussi!";
} catch (Exception $e) {
    file_put_contents('/home/u684456859/domains/bisika.menjidrc.com/deploy_errors.log',
        "ERROR: ".$e->getMessage()."\n",
        FILE_APPEND);
    http_response_code(500);
    echo "Erreur de déploiement";
}
