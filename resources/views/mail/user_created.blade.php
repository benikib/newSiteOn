<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Bienvenue</title>
</head>

<body style="margin:0; padding:0; font-family: Arial, sans-serif; background-color:#0a192f; color:#f0f0f0;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#0a192f; padding:30px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background-color:#112240; border-radius:10px; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.4);">

                    <!-- Logo -->
                    <tr>
                        <td style="padding:20px; text-align:center; background-color:#112240;">
                            <img src="https://bisika.site/assets/img/logo-ct.png" class="navbar-brand-img h-100"
                                style="border-radius: 50%;" alt="Logo" style="max-width:150px; height:auto;">
                        </td>
                    </tr>

                    <!-- Header -->
                    <tr>
                        <td
                            style="background-color:#1e3a8a; padding:20px; text-align:center; color:#ffffff; font-size:22px; font-weight:bold;">
                            🎉 Bienvenue sur notre plateforme !
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px; color:#e0e0e0; font-size:16px; line-height:1.6;">
                            <p>Bonjour <strong>{{ $user->name }}</strong>,</p>

                            <p>Votre compte a été créé avec succès. Pour sécuriser votre accès, merci de créer votre mot
                                de passe en cliquant sur le bouton ci-dessous :</p>

                            <div style="text-align:center; margin-top:30px;">
                                <a href="{{ $resetUrl }}"
                                    style="display:inline-block; background-color:#1e3a8a; color:#ffffff; padding:12px 24px; border-radius:6px; text-decoration:none; font-weight:bold;">
                                    🔑 Créer votre mot de passe
                                </a>
                            </div>

                            <p style="margin-top:20px;">⚠️ Ce lien expire dans 60 minutes pour des raisons de sécurité.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="background-color:#1e3a8a; padding:15px; text-align:center; color:#cbd5e1; font-size:14px;">
                            &copy; {{ date('Y') }} Votre Application. Tous droits réservés.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>
