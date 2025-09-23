<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $subject ?? 'Explorer Elite' }}</title>
    <!-- Some clients ignore web fonts; we still declare for those that support -->
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
  </head>
  <body style="margin:0;padding:0;background-color:#ffffff;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#ffffff;">
      <tr>
        <td align="center" style="padding:0;">
          <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" style="width:600px;max-width:600px;">
            <tr>
              <td style="padding:16px 24px;">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                  <tr>
                    <td align="left" style="font-family:Inter,Arial,sans-serif;font-size:14px;color:#111;">
                      <img src="{{ $logo_url ?? 'https://explorerelite.com/wp-content/uploads/2024/12/logo-black.png' }}" alt="Explorer Elite" height="28" style="display:block;border:0;outline:none;text-decoration:none;height:28px;" />
                    </td>
                    <td align="right" style="font-family:Inter,Arial,sans-serif;font-size:14px;">
                      <a href="{{ $login_url ?? ($frontend_url ?? config('app.frontend_url', url('/'))) }}" style="color:#111;text-decoration:none;border:1px solid #111;border-radius:10px;padding:8px 14px;display:inline-block;">🔒 Login</a>
                      <a href="https://explorerelite.com/support/" style="color:#111;text-decoration:none;border:1px solid #111;border-radius:10px;padding:8px 14px;display:inline-block;margin-left:8px;">☎️ Contact</a>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>

            <tr>
              <td align="center" style="padding:24px 24px 8px;">
                <!-- Hourglass icon (inline SVG for best client support) -->
                <div style="width:120px;height:120px;">
                  <svg width="120" height="120" viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
                    <rect x="26" y="30" width="68" height="10" rx="2" fill="#111" />
                    <rect x="26" y="80" width="68" height="10" rx="2" fill="#111" />
                    <path d="M40 40h40c0 18-20 18-20 30 0-12-20-12-20-30z" fill="#111"/>
                    <path d="M60 61c0-8 12-8 12-17H48c0 9 12 9 12 17z" fill="#ec8d22"/>
                  </svg>
                </div>
              </td>
            </tr>

            <tr>
              <td align="center" style="padding:0 24px 8px;">
                <div style="font-family:Anton,Arial Black,Arial,sans-serif;font-size:36px;line-height:1.2;color:#111;text-align:center;">
                  {{ $heading ?? 'Your Listing is Under Review' }}
                </div>
              </td>
            </tr>

            <tr>
              <td style="padding:12px 36px 0;font-family:Inter,Arial,sans-serif;font-size:16px;line-height:1.6;color:#111;">
                {!! $intro_html ?? 'Great news! Your Listing is under review by our team, we’ll notify you once it’s done. You can always contact us if you need to make changes.' !!}
              </td>
            </tr>

            <tr>
              <td align="center" style="padding:24px 24px;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                  <tr>
                    <td align="center" style="padding-right:12px;">
                      <a href="{{ $primary_url ?? ($frontend_url ?? config('app.frontend_url', url('/'))) }}" style="display:inline-block;background-color:{{ $primary_bg ?? '#000000' }};color:{{ $primary_color ?? '#ffffff' }};text-decoration:none;padding:12px 18px;border-radius:8px;font-family:Inter,Arial,sans-serif;font-size:14px;">{{ $primary_text ?? 'Go To Dashboard' }}</a>
                    </td>
                    @if(!empty($secondary_url))
                    <td align="center" style="padding-left:12px;">
                      <a href="{{ $secondary_url }}" style="display:inline-block;background-color:{{ $secondary_bg ?? '#ec8d22' }};color:{{ $secondary_color ?? '#ffffff' }};text-decoration:none;padding:12px 18px;border-radius:8px;font-family:Inter,Arial,sans-serif;font-size:14px;">{{ $secondary_text ?? 'Contact Support' }}</a>
                    </td>
                    @endif
                  </tr>
                </table>
              </td>
            </tr>

            <tr>
              <td style="padding:16px 36px 0;font-family:Inter,Arial,sans-serif;font-size:16px;line-height:1.6;color:#111;">
                {{ $closing_text ?? "Thank you for choosing our platform to share your adventures! We’re excited to approve it soon!" }}
              </td>
            </tr>

            <tr>
              <td align="center" style="padding:24px 24px 8px;">
                <hr style="border:none;border-top:1px solid #d9d9d9;width:60%;" />
              </td>
            </tr>

            <tr>
              <td style="padding:8px 24px 0;font-family:Inter,Arial,sans-serif;font-size:14px;line-height:1.6;color:#111;" align="center">
                For questions, visit our <a href="https://explorerelite.com/support/" style="color:#ec8d22;text-decoration:none;">Support Page</a>
                or contact us directly via <a href="{{ $whatsapp_url ?? 'https://wa.me/' }}" style="color:#ec8d22;text-decoration:none;">WhatsApp</a>.
              </td>
            </tr>

            <tr>
              <td align="center" style="padding:20px 24px 4px;">
                <a href="{{ $instagram_url ?? '#' }}" style="text-decoration:none;color:#111;font-family:Inter,Arial,sans-serif;font-size:16px;margin:0 10px;">Instagram</a>
                <a href="{{ $whatsapp_url ?? 'https://wa.me/' }}" style="text-decoration:none;color:#111;font-family:Inter,Arial,sans-serif;font-size:16px;margin:0 10px;">WhatsApp</a>
                <a href="{{ $x_url ?? '#' }}" style="text-decoration:none;color:#111;font-family:Inter,Arial,sans-serif;font-size:16px;margin:0 10px;">X</a>
                <a href="{{ $linkedin_url ?? '#' }}" style="text-decoration:none;color:#111;font-family:Inter,Arial,sans-serif;font-size:16px;margin:0 10px;">LinkedIn</a>
              </td>
            </tr>

            <tr>
              <td align="center" style="padding:8px 24px 28px;font-family:Inter,Arial,sans-serif;font-size:12px;color:#666;">
                <div style="margin-top:12px;">www.ExplorerElite.com</div>
                <div style="margin-top:6px;">
                  <a href="https://explorerelite.com/legal/terms-of-services/" style="color:#666;text-decoration:none;">TERMS OF SERVICE</a>
                  &nbsp;|&nbsp;
                  <a href="https://explorerelite.com/legal/privacy-policy/" style="color:#666;text-decoration:none;">PRIVACY POLICY</a>
                  &nbsp;|&nbsp;
                  <a href="https://explorerelite.com/legal" style="color:#666;text-decoration:none;">LEGAL</a>
                </div>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
  </html>


