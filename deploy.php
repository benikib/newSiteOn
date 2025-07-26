<?php
// $secret = 'votre-secret'; // Si vous utilisez un secret GitHub/GitLab
$payload = file_get_contents('php://input');
$headers = getallheaders();

// Vérification du secret (optionnel)
// if (isset($headers['X-Hub-Signature'])) {
//     $githubSig = $headers['X-Hub-Signature'];
//     $calculatedSig = 'sha1=' . hash_hmac('sha1', $payload, $secret);
//     if (!hash_equals($githubSig, $calculatedSig)) {
//         die('Accès refusé : signature invalide.');
//     }
// }

// Exécuter le script de déploiement
exec('sh domains/menjidrc.com/public_html/bisika/deploy.sh', $output, $returnCode);

// Logs (optionnel)
file_put_contents('deploy.log', date('Y-m-d H:i:s') . " - Déploiement effectué\n", FILE_APPEND);

echo "Déploiement réussi !";
