<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur TechShop</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #eef2f7;
            padding: 40px 16px;
        }
        .wrapper { max-width: 520px; margin: 0 auto; }

        .logo-area { text-align: center; margin-bottom: 24px; }
        .logo-text { font-size: 22px; font-weight: 800; color: #0f172a; }
        .logo-text span { color: #4f46e5; }

        .card {
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 40px rgba(79, 70, 229, 0.12);
        }

        .banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #4f46e5 100%);
            padding: 48px 32px 44px;
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
            width: 72px; height: 72px;
            background: rgba(255,255,255,0.12);
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 18px;
        }
        .banner h1 { color: #ffffff; font-size: 22px; font-weight: 800; }
        .banner p  { color: rgba(255,255,255,0.65); font-size: 13.5px; margin-top: 8px; }

        .body { padding: 40px 40px 36px; text-align: center; }

        .badge-tunisia {
            display: inline-block;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #059669;
            font-size: 12px;
            font-weight: 600;
            padding: 5px 14px;
            border-radius: 20px;
            margin-bottom: 20px;
        }

        .welcome-name { font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 14px; }
        .welcome-name span { color: #4f46e5; }

        .body p { color: #64748b; font-size: 14.5px; line-height: 1.85; }

        .footer {
            padding: 22px 40px;
            text-align: center;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
        }
        .footer p { font-size: 11.5px; color: #94a3b8; line-height: 1.8; }
        .footer strong { color: #4f46e5; }
    </style>
</head>
<body>
    <div class="wrapper">

        <div class="logo-area">
            <span class="logo-text">💻 Tech<span>Shop</span></span>
        </div>

        <div class="card">

            <div class="banner">
                <div class="icon">🎉</div>
                <h1>Bienvenue sur TechShop !</h1>
                <p>Votre compte a été créé avec succès</p>
            </div>

            <div class="body">
                <div class="badge-tunisia">🇹🇳 &nbsp;Le meilleur matériel informatique en Tunisie</div>

                <p class="welcome-name">Bonjour, <span>{{ $user->name }}</span> 👋</p>

                <p>
                    Nous sommes ravis de vous accueillir sur <strong>TechShop Tunisia</strong>.<br>
                    Votre compte est maintenant actif et prêt à l'emploi !
                </p>
            </div>

            <div class="footer">
                <p>
                    Cet email a été envoyé automatiquement par <strong>TechShop Tunisia</strong>.<br>
                    © {{ date('Y') }} TechShop — Tous droits réservés.
                </p>
            </div>

        </div>
    </div>
</body>
</html>