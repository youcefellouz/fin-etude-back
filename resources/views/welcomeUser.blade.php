<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #eef2f7;
            padding: 40px 16px;
        }
        .wrapper { max-width: 520px; margin: 0 auto; }

        .logo-area {
            text-align: center;
            margin-bottom: 24px;
        }
        .logo-area span {
            font-size: 28px;
            font-weight: 800;
            color: #4f46e5;
            letter-spacing: 1px;
        }

        .card {
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 40px rgba(79, 70, 229, 0.12);
        }

        .banner {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 48px 32px 40px;
            text-align: center;
            position: relative;
        }
        .banner::after {
            content: '';
            position: absolute;
            bottom: -1px; left: 0; right: 0;
            height: 30px;
            background: #ffffff;
            border-radius: 50% 50% 0 0 / 100% 100% 0 0;
        }
        .banner .icon {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 16px;
        }
        .banner h1 {
            color: #ffffff;
            font-size: 22px;
            font-weight: 700;
        }
        .banner p {
            color: rgba(255,255,255,0.8);
            font-size: 14px;
            margin-top: 6px;
        }

        .body { padding: 36px 40px; text-align: center; }

        .welcome-name {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
        }
        .welcome-name span { color: #4f46e5; }

        .body p {
            color: #6b7280;
            font-size: 15px;
            line-height: 1.8;
            margin-bottom: 28px;
        }

        /* Features */
        .features {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 32px;
    text-align: left;
    width: 100%;
}
.feature-item {
    display: flex;
    align-items: center;
    gap: 14px;
    background: #f5f3ff;
    border-radius: 12px;
    padding: 14px 16px;
    width: 100%;
}
        .feature-icon {
            font-size: 22px;
            min-width: 32px;
            text-align: center;
        }
        .feature-text strong {
            display: block;
            font-size: 14px;
            color: #1f2937;
            font-weight: 600;
        }
        .feature-text span {
            font-size: 12px;
            color: #9ca3af;
        }

        /* CTA Button */
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 40px;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .footer {
            padding: 24px 40px;
            text-align: center;
            background: #f9fafb;
            border-top: 1px solid #f3f4f6;
        }
        .footer p {
            font-size: 12px;
            color: #9ca3af;
            line-height: 1.8;
        }
        .footer strong { color: #4f46e5; }
    </style>
</head>
<body>
    <div class="wrapper">

        <div class="logo-area">
            <span>🛒 MyShop</span>
        </div>

        <div class="card">

            <div class="banner">
                <div class="icon">🎉</div>
                <h1>Bienvenue sur MyShop !</h1>
                <p>Votre compte a été créé avec succès</p>
            </div>

            <div class="body">
                <p class="welcome-name">
                    Bonjour, <span>{{ $user->name }}</span> 👋
                </p>
                <p>
                    Nous sommes ravis de vous accueillir dans notre communauté.<br>
                    Votre compte est maintenant actif et prêt à l'emploi !
                </p>

                <!-- Features -->
                <div class="features">
                    <div class="feature-item">
                        <div class="feature-icon">🛍️</div>
                        <div class="feature-text">
                            <strong>Parcourez nos produits</strong>
                            <span>Des milliers d'articles disponibles</span>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">🚚</div>
                        <div class="feature-text">
                            <strong>Livraison rapide</strong>
                            <span>Recevez vos commandes rapidement</span>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">🔒</div>
                        <div class="feature-text">
                            <strong>Paiement sécurisé</strong>
                            <span>Vos données sont protégées</span>
                        </div>
                    </div>
                </div>

                <a href="#" class="btn">Commencer mes achats →</a>
            </div>

            <div class="footer">
                <p>
                    Cet email a été envoyé automatiquement par <strong>MyShop</strong>.<br>
                    © {{ date('Y') }} MyShop — Tous droits réservés.
                </p>
            </div>

        </div>
    </div>
</body>
</html>