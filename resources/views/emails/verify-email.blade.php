<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de vérification</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .logo-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, #4f46e5, #818cf8);
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        .logo-text {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
        }
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

        .body { padding: 36px 40px; text-align: center; }
        .body .intro {
            color: #64748b;
            font-size: 14.5px;
            line-height: 1.8;
            margin-bottom: 28px;
        }

        .code-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 12px;
        }
        .code-container {
            display: inline-flex;
            gap: 8px;
            margin-bottom: 28px;
            flex-wrap: nowrap;
        }
        .code-digit {
            width: 44px; height: 56px;
            background: #eef2ff;
            border: 2px solid #c7d2fe;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 800;
            color: #4f46e5;
            flex-shrink: 0;
        }

        .timer-box {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 50px;
            padding: 10px 20px;
            margin-bottom: 28px;
        }
        .timer-box span { font-size: 13px; color: #ea580c; font-weight: 600; }

        .divider { border: none; border-top: 1px solid #f1f5f9; margin: 4px 0 24px; }

        .warning {
            background: #fef2f2;
            border-left: 4px solid #f87171;
            border-radius: 8px;
            padding: 14px 16px;
            text-align: left;
            font-size: 13px;
            color: #b91c1c;
            line-height: 1.6;
        }

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
            <div class="logo-icon">💻</div>
            <span class="logo-text">Tech<span>Shop</span></span>
        </div>

        <div class="card">

            <div class="banner">
                <div class="icon">🔐</div>
                <h1>Vérification de votre email</h1>
                <p>Confirmez votre identité pour activer votre compte</p>
            </div>

            <div class="body">
                <p class="intro">
                    Bienvenue sur <strong>TechShop Tunisia</strong> !<br>
                    Voici votre code de vérification à usage unique pour activer votre compte :
                </p>

                <div class="code-label">Votre code</div>
                <div class="code-container">
                    @foreach(str_split($code) as $digit)
                        <div class="code-digit">{{ $digit }}</div>
                    @endforeach
                </div>

                <div>
                    <div class="timer-box">
                        <span>⏱️ Ce code expire dans <strong>5 minutes</strong></span>
                    </div>
                </div>

                <hr class="divider">

                <div class="warning">
                    ⚠️ <strong>Vous n'avez pas créé de compte ?</strong><br>
                    Ignorez cet email. Votre adresse ne sera pas enregistrée.
                </div>
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