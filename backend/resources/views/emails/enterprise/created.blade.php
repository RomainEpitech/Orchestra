<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de création de votre entreprise</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #4f70ce 0%, #2a3f87 100%);
            color: white;
            padding: 20px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .welcome-message {
            font-size: 18px;
            margin-bottom: 25px;
            color: #2a3f87;
        }
        .key-section {
            background-color: #f8f4ff;
            border: 2px solid #6347c0;
            border-radius: 6px;
            padding: 20px;
            margin: 25px 0;
            position: relative;
        }
        .key-section::before {
            content: "⚠️ IMPORTANT";
            position: absolute;
            top: -12px;
            left: 20px;
            background-color: #6347c0;
            color: white;
            padding: 2px 10px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
        }
        .recovery-key {
            background-color: #eeeaff;
            font-family: monospace;
            padding: 12px;
            border-radius: 4px;
            text-align: center;
            font-size: 18px;
            letter-spacing: 1px;
            margin: 15px 0;
            border: 1px dashed #6347c0;
            word-break: break-all;
        }
        .next-steps {
            background-color: #f0f7ff;
            border-radius: 6px;
            padding: 20px;
            margin: 25px 0;
        }
        .next-steps h3 {
            margin-top: 0;
            color: #2a5298;
        }
        .steps-list {
            margin-left: 20px;
            padding-left: 0;
        }
        .steps-list li {
            margin-bottom: 10px;
            position: relative;
            list-style-type: none;
            padding-left: 25px;
        }
        .steps-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #4f70ce;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            background-color: #4f70ce;
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 4px;
            font-weight: 600;
            margin: 15px 0;
            text-align: center;
        }
        .button:hover {
            background-color: #3a5bb9;
        }
        .footer {
            background-color: #f2f2f2;
            padding: 20px 30px;
            text-align: center;
            font-size: 14px;
            color: #666;
            border-top: 1px solid #e0e0e0;
        }
        .footer .thanks {
            font-size: 16px;
            margin-bottom: 15px;
            font-style: italic;
            color: #2a3f87;
        }
        .contact-info {
            margin-top: 15px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        @media only screen and (max-width: 620px) {
            .container {
                width: 100%;
                border-radius: 0;
            }
            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="Orchestra Logo" class="logo">
            <h1>Bienvenue sur Orchestra</h1>
        </div>
        
        <div class="content">
            <div class="welcome-message">
                <p>Cher(e) <strong>{{ $ownerName }}</strong>,</p>
                <p>Nous sommes ravis de vous confirmer la création réussie de votre entreprise <strong>{{ $enterpriseName }}</strong> sur notre plateforme Orchestra.</p>
            </div>
            
            <div class="key-section">
                <h2>Votre clé de récupération</h2>
                <div class="recovery-key">{{ $recoveryKey }}</div>
                <p><strong>Cette clé est CRUCIALE pour la sécurité de votre compte d'entreprise.</strong> Elle constitue le SEUL moyen de récupérer l'accès administrateur à votre espace si vous perdez vos identifiants.</p>
                <p>Il est impératif de :</p>
                <ul>
                    <li>La conserver dans un endroit sûr et confidentiel</li>
                    <li>Ne pas la partager par email ou messagerie non sécurisée</li>
                    <li>En limiter l'accès aux personnes de confiance uniquement</li>
                    <li>Envisager l'utilisation d'un gestionnaire de mots de passe sécurisé</li>
                </ul>
                <p>Sans cette clé, il sera <strong>impossible</strong> de restaurer l'accès administrateur en cas de perte.</p>
            </div>
            
            <div class="next-steps">
                <h3>Prochaines étapes</h3>
                <ul class="steps-list">
                    <li>Complétez le profil de votre entreprise</li>
                    <li>Invitez vos collaborateurs</li>
                    <li>Configurez vos espaces de travail</li>
                    <li>Explorez les modules disponibles</li>
                </ul>
                
                <a href="{{ $loginUrl }}" class="button">Accéder à votre tableau de bord</a>
            </div>
        </div>
        
        <div class="footer">
            <p class="thanks">L'équipe Orchestra vous remercie de votre confiance</p>
            <div class="contact-info">
                <p>Besoin d'assistance ? Contactez-nous à {{ $supportEmail }}</p>
                <p>© {{ date('Y') }} Orchestra. Tous droits réservés.</p>
            </div>
        </div>
    </div>
</body>
</html>