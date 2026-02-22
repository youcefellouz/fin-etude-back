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
        .wrapper {
            max-width: 520px;
            margin: 0 auto;
        }

        /* Header Logo Area */
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

        /* Card */
        .card {
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 40px rgba(79, 70, 229, 0.12);
        }

        /* Banner */
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
            backdrop-filter: blur(4px);
        }
        .banner h1 {
            color: #ffffff;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .banner p {
            color: rgba(255,255,255,0.8);
            font-size: 14px;
            margin-top: 6px;
        }

        /* Body */
        .body {
            padding: 36px 40px;
            text-align: center;
        }
        .body .intro {
            color: #6b7280;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 28px;
        }

        /* Code Box */
        .code-label {
            font-size: 12px;
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
    max-width: 100%;
    flex-wrap: nowrap;
}
.code-digit {
    width: 44px;
    height: 56px;
    background: #f5f3ff;
    border: 2px solid #e0d9ff;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: 800;
    color: #4f46e5;
    flex-shrink: 0;
}

        /* Timer */
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
        .timer-box span {
            font-size: 13px;
            color: #ea580c;
            font-weight: 600;
        }

        /* Divider */
        .divider {
            border: none;
            border-top: 1px solid #f3f4f6;
            margin: 4px 0 24px;
        }

        /* Warning */
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

        /* Footer */
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
        .footer strong {
            color: #4f46e5;
        }
    </style>
</head>
<body>
    <div class="wrapper">

        <!-- Logo -->
        <div class="logo-area">
            <span>🛒 MyShop</span>
        </div>

        <div class="card">

            <!-- Banner -->
            <div class="banner">
                <div class="icon">🔐</div>
                <h1>Vérification de votre email</h1>
                <p>Confirmez votre identité pour activer votre compte</p>
            </div>

            <!-- Body -->
            <div class="body">
                <p class="intro">
                    Bonjour ! Vous avez demandé la création d'un compte.<br>
                    Voici votre code de vérification à usage unique :
                </p>

                <div class="code-label">Votre code</div>

                <!-- Each digit in its own box -->
                <div class="code-container">
                    @foreach(str_split($code) as $digit)
                        <div class="code-digit">{{ $digit }}</div>
                    @endforeach
                </div>

                <!-- Timer -->
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

            <!-- Footer -->
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