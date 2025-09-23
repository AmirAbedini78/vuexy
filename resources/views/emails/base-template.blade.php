<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Explorer Elite' }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            background-color: #000000;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            color: #ffffff;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo-icon {
            width: 30px;
            height: 30px;
            background-color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #000000;
        }
        .header-buttons {
            display: flex;
            gap: 10px;
        }
        .header-btn {
            background-color: transparent;
            border: 1px solid #ffffff;
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .main-heading {
            font-size: 28px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 20px;
        }
        .body-text {
            font-size: 16px;
            color: #333333;
            line-height: 1.6;
            text-align: left;
            margin-bottom: 30px;
        }
        .cta-button {
            background-color: #ff6b35;
            color: #ffffff;
            padding: 15px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 30px;
        }
        .important-note {
            font-size: 14px;
            color: #666666;
            line-height: 1.5;
            text-align: left;
            margin-bottom: 40px;
        }
        .footer {
            border-top: 1px solid #e0e0e0;
            padding: 30px;
            text-align: center;
        }
        .contact-info {
            font-size: 14px;
            color: #333333;
            margin-bottom: 20px;
        }
        .contact-link {
            color: #ff6b35;
            text-decoration: none;
        }
        .social-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        .social-icon {
            width: 30px;
            height: 30px;
            background-color: #000000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            text-decoration: none;
            font-size: 14px;
        }
        .website-url {
            font-size: 16px;
            color: #000000;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .legal-links {
            font-size: 12px;
            color: #666666;
            margin-bottom: 10px;
        }
        .legal-link {
            color: #666666;
            text-decoration: none;
        }
        .copyright {
            font-size: 12px;
            color: #999999;
        }
        @media only screen and (max-width: 600px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }
            .content {
                padding: 30px 20px;
            }
            .main-heading {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <div class="logo-icon">⛰️</div>
                {{ config('app.name') }}
            </div>
            <div class="header-buttons">
                <a href="{{ config('app.frontend_url') }}/login" class="header-btn">
                    <span>🔐</span> Login
                </a>
                <a href="{{ config('app.frontend_url') }}/contact" class="header-btn">
                    <span>💬</span> Contact
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content">
            <h1 class="main-heading">{{ $title }}</h1>
            
            <div class="body-text">
                {!! $body !!}
            </div>

            @if(isset($actionUrl) && isset($actionText))
            <a href="{{ $actionUrl }}" class="cta-button">{{ $actionText }}</a>
            @endif

            <div class="important-note">
                {!! $importantNote ?? 'This link is valid for 24 hours. If you didn\'t request this, please ignore this email or contact our support team.' !!}
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="contact-info">
                For questions, visit our <a href="https://explorerelite.com/support/" class="contact-link">Support Page</a> or contact us directly via <a href="https://wa.me/your-whatsapp-number" class="contact-link">WhatsApp</a>.
            </div>
            
            <div class="social-icons">
                <a href="#" class="social-icon">📷</a>
                <a href="#" class="social-icon">📱</a>
                <a href="#" class="social-icon">🐦</a>
                <a href="#" class="social-icon">💼</a>
            </div>
            
            <div class="website-url">www.{{ strtolower(str_replace(' ', '', config('app.name'))) }}.com</div>
            
            <div class="legal-links">
                <a href="{{ config('app.frontend_url') }}/terms" class="legal-link">TERMS OF SERVICE</a> | 
                <a href="{{ config('app.frontend_url') }}/privacy" class="legal-link">PRIVACY POLICY</a> | 
                <a href="{{ config('app.frontend_url') }}/legal" class="legal-link">LEGAL</a>
            </div>
            
            <div class="copyright">Sent With ❤️ From {{ config('app.name') }}.</div>
        </div>
    </div>
</body>
</html> 
