<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur Orchestra - Votre licence a été activée</title>
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
        .credentials-section {
            background-color: #f8f4ff;
            border: 2px solid #6347c0;
            border-radius: 6px;
            padding: 20px;
            margin: 25px 0;
            position: relative;
        }
        .credentials-section::before {
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
        .credentials {
            background-color: #eeeaff;
            font-family: monospace;
            padding: 12px;
            border-radius: 4px;
            text-align: left;
            font-size: 16px;
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
            <h1>Bienvenue sur Orchestra</h1>
        </div>
        
        <div class="content">
            <div class="welcome-message">
                <p>Bonjour <strong>{{ $user->firstname }}</strong>,</p>
                <p>L'entreprise <strong>{{ $enterprise->name }}</strong> a activé votre licence sur la plateforme Orchestra.</p>
            </div>
            
            <div class="credentials-section">
                <h2>Vos identifiants de connexion</h2>
                <div class="credentials">
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Mot de passe temporaire:</strong> {{ $temporaryPassword }}</p>
                </div>
                <p><strong>Pour des raisons de sécurité, nous vous recommandons de changer ce mot de passe dès votre première connexion.</strong></p>
            </div>
            
            <div class="next-steps">
                <h3>Pour commencer</h3>
                <ul class="steps-list">
                    <li>Connectez-vous à votre compte</li>
                    <li>Changez votre mot de passe temporaire</li>
                    <li>Complétez votre profil</li>
                    <li>Explorez les fonctionnalités disponibles</li>
                </ul>
                
                <a href="{{ config('app.frontend_url') }}/login" class="button">Se connecter à Orchestra</a>
            </div>
        </div>
        
        <div class="footer">
            <p class="thanks">Bienvenue dans l'équipe {{ $enterprise->name }} sur Orchestra !</p>
            <div class="contact-info">
                <p>Besoin d'assistance ? Contactez votre administrateur ou notre support</p>
                <p>© {{ date('Y') }} Orchestra. Tous droits réservés.</p>
            </div>
        </div>
    </div>
</body>
</html>