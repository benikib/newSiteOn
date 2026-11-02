@extends('layouts.public')

@section('title', 'Règles de confidentialité | BISIKA')

@section('content')
<article class="container py-4 py-lg-5" style="max-width: 900px">
    <header class="mb-4">
        <p class="text-uppercase small fw-bold text-success mb-2">BISIKA</p>
        <h1 class="display-6 fw-bold">Règles de confidentialité et données personnelles</h1>
        <p class="text-muted mb-0">Cette page explique quelles données peuvent être traitées lors de l’utilisation de BISIKA et à quelles fins.</p>
    </header>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 p-md-5">
            <section class="mb-4">
                <h2 class="h4">1. Responsable et contact</h2>
                <p>BISIKA est une plateforme de découverte et de gestion d’établissements. Pour toute question ou demande concernant vos données, contactez l’équipe BISIKA à <a href="mailto:contactbisika@gmail.com">contactbisika@gmail.com</a> ou au <a href="tel:+243978002597">+243 978 002 597</a>.</p>
            </section>

            <section class="mb-4">
                <h2 class="h4">2. Données susceptibles d’être collectées</h2>
                <p>Selon les fonctionnalités utilisées, BISIKA peut traiter :</p>
                <ul>
                    <li>les données de compte : nom, adresse e-mail, téléphone, rôle et informations nécessaires à l’authentification ;</li>
                    <li>les informations d’établissement : nom, domaine d’activité, coordonnées, adresse, description et informations d’identification professionnelle fournies par le gestionnaire ;</li>
                    <li>les fichiers transmis dans l’application, comme des photos, médias publicitaires ou documents d’autorisation ;</li>
                    <li>les informations liées aux services, réservations, commandes, demandes de livraison, stocks et ventes ;</li>
                    <li>les données communiquées dans un message envoyé au support ;</li>
                    <li>les données techniques nécessaires au fonctionnement et à la sécurité du site, ainsi que les choix de consentement aux mesures d’audience.</li>
                </ul>
                <p>Ne transmettez que les informations nécessaires et évitez d’envoyer des données sensibles qui ne sont pas demandées par le service.</p>
            </section>

            <section class="mb-4">
                <h2 class="h4">3. Utilisation des données</h2>
                <p>Les données servent à fournir les fonctionnalités de BISIKA : créer et sécuriser les comptes, présenter les établissements et leurs offres, traiter les réservations et demandes, permettre la gestion des établissements, administrer les affiliations et cotisations patronales, répondre au support et améliorer le fonctionnement de la plateforme.</p>
            </section>

            <section class="mb-4">
                <h2 class="h4">4. Partage et prestataires</h2>
                <p>Les informations utiles à une transaction ou à une demande peuvent être communiquées aux établissements et organisations concernés afin qu’ils puissent y répondre. Les données peuvent également être traitées par les prestataires techniques nécessaires à l’hébergement, à l’envoi de courriels ou, si elle est configurée et acceptée, à la mesure d’audience.</p>
                <p>BISIKA ne vend pas les données personnelles. Les données ne sont partagées que pour les finalités du service, avec les personnes et prestataires qui en ont besoin, ou lorsque la loi l’exige.</p>
            </section>

            <section class="mb-4">
                <h2 class="h4">5. Cookies et mesure d’audience</h2>
                <p>Le site utilise des éléments techniques nécessaires aux sessions et à la sécurité des formulaires. Sur les pages où la mesure d’audience est activée, un choix vous est proposé pour accepter ou refuser les cookies analytiques. Si vous les refusez, la mesure d’audience correspondante n’est pas chargée. Votre choix est mémorisé dans le navigateur utilisé.</p>
            </section>

            <section class="mb-4">
                <h2 class="h4">6. Conservation et sécurité</h2>
                <p>Les données sont conservées pendant la durée nécessaire à la fourniture des services, à la gestion des comptes et des opérations, au respect des obligations applicables et au traitement d’éventuels litiges. Des mesures techniques et organisationnelles sont mises en œuvre pour protéger les données ; aucun système de transmission ou de stockage ne peut toutefois être garanti comme absolument sûr.</p>
            </section>

            <section class="mb-4">
                <h2 class="h4">7. Vos demandes</h2>
                <p>Vous pouvez contacter BISIKA pour demander l’accès à vos données, leur rectification ou des informations sur leur traitement. Une demande de suppression peut être limitée lorsque certaines données doivent être conservées pour fournir un service, satisfaire une obligation ou traiter un litige. Pour exercer une demande, écrivez à <a href="mailto:contactbisika@gmail.com">contactbisika@gmail.com</a> en précisant l’adresse e-mail associée à votre compte ; des informations complémentaires pourront être demandées afin de vérifier votre identité.</p>
            </section>

            <section>
                <h2 class="h4">8. Mise à jour de cette page</h2>
                <p class="mb-0">Cette notice peut être mise à jour pour refléter l’évolution de BISIKA ou de ses pratiques. La version publiée sur cette page est la version applicable au moment de votre consultation.</p>
            </section>
        </div>
    </div>

    <p class="small text-muted mt-3">Pour toute question, consultez aussi notre <a href="{{ route('contact') }}">page de contact</a>.</p>
</article>
@endsection
