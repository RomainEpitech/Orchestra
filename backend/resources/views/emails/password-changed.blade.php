<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orchestra - Votre mot de passe a été modifié</title>
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
        .alert-message {
            font-size: 18px;
            margin-bottom: 25px;
            color: #2a3f87;
        }
        .warning-section {
            background-color: #fff8f8;
            border: 2px solid #c04747;
            border-radius: 6px;
            padding: 20px;
            margin: 25px 0;
            position: relative;
        }
        .warning-section::before {
            content: "⚠️ ALERTE SÉCURITÉ";
            position: absolute;
            top: -12px;
            left: 20px;
            background-color: #c04747;
            color: white;
            padding: 2px 10px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
        }
        .information {
            background-color: #f0f7ff;
            padding: 12px;
            border-radius: 4px;
            text-align: left;
            font-size: 16px;
            margin: 15px 0;
            border: 1px dashed #4f70ce;
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
            content: "!";
            position: absolute;
            left: 0;
            color: #c04747;
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
            <h1>Alerte de Sécurité</h1>
        </div>
        
        <div class="content">
            <div class="alert-message">
                <p>Bonjour <strong>{{ $user->firstname }}</strong>,</p>
                <p>Nous vous informons que le mot de passe de votre compte Orchestra a été modifié le {{ $timestamp }}.</p>
            </div>
            
            <div class="information">
                <p>Si vous êtes à l'origine de cette modification, vous pouvez ignorer ce message.</p>
            </div>
            
            <div class="warning-section">
                <h2>Action requise si ce n'est pas vous</h2>
                <p>Si vous n'avez pas demandé ce changement, veuillez immédiatement :</p>
                <ul class="steps-list">
                    <li>Modifiez votre mot de passe dans les plus brefs délais</li>
                    <li>Contacter votre administrateur pour signaler l'incident</li>
                    <li>Contacter <strong>support@orchestra.fr</strong></li>
                </ul>
            </div>
            
            <div class="next-steps">
                <h3>Accès à votre compte</h3>
                <p>Pour des raisons de sécurité, vous avez été déconnecté de tous vos autres appareils.</p>
                <a href="{{ $loginUrl }}" class="button">Accéder à mon compte</a>
            </div>
        </div>
        
        <div class="footer">
            <p class="thanks">L'équipe Orchestra vous remercie pour votre vigilance</p>
            <div class="contact-info">
                <p>Ce message a été envoyé automatiquement, merci de ne pas y répondre.</p>
                <p>© {{ date('Y') }} Orchestra. Tous droits réservés.</p>
            </div>
        </div>
    </div>
</body>
</html>