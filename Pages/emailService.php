<?php
// PHPMailer SMTP wrapper. Admins can update these settings from admin.php.
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$autoload = __DIR__ . "/vendor/autoload.php";
if (file_exists($autoload)) {
    require_once $autoload;
} else {
    require_once __DIR__ . "/src/Exception.php";
    require_once __DIR__ . "/src/PHPMailer.php";
    require_once __DIR__ . "/src/SMTP.php";
}

if (!defined("MAIL_FROM")) {
    $mailHost = "smtp.gmail.com";
    $mailPort = "587";
    $mailEncryption = "tls";
    $mailFrom = "yourgmail@gmail.com";
    $mailName = "SirChef";
    $mailUsername = "";
    $mailPassword = "xxxx xxxx xxxx xxxx";

    // Admin page can save SMTP settings in site_settings.
    // If the table is not available yet, the safe placeholders remain.
    if (isset($conn) && $conn instanceof mysqli) {
        $settingsResult = @$conn->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key IN ('mail_host','mail_port','mail_encryption','mail_from','mail_name','mail_username','mail_password')");
        if ($settingsResult) {
            while ($row = $settingsResult->fetch_assoc()) {
                if ($row["setting_key"] === "mail_host") {
                    $mailHost = $row["setting_value"];
                } elseif ($row["setting_key"] === "mail_port") {
                    $mailPort = $row["setting_value"];
                } elseif ($row["setting_key"] === "mail_encryption") {
                    $mailEncryption = $row["setting_value"];
                } elseif ($row["setting_key"] === "mail_from") {
                    $mailFrom = $row["setting_value"];
                } elseif ($row["setting_key"] === "mail_name") {
                    $mailName = $row["setting_value"];
                } elseif ($row["setting_key"] === "mail_username") {
                    $mailUsername = $row["setting_value"];
                } elseif ($row["setting_key"] === "mail_password") {
                    $mailPassword = $row["setting_value"];
                }
            }
        }
    }

    $mailHost = trim($mailHost);
    $mailEncryption = strtolower(trim($mailEncryption));
    $mailFrom = strtolower(trim($mailFrom));
    $mailName = trim($mailName);
    $mailUsername = trim($mailUsername);
    $mailPassword = trim($mailPassword);

    if ($mailUsername === "" || $mailUsername === "yourgmail@gmail.com") {
        $mailUsername = $mailFrom;
    }

    define("MAIL_HOST", $mailHost);
    define("MAIL_PORT", (int) $mailPort);
    define("MAIL_ENCRYPTION", $mailEncryption);
    define("MAIL_FROM", $mailFrom);
    define("MAIL_NAME", $mailName);
    define("MAIL_USERNAME", $mailUsername);
    define("MAIL_PASSWORD", $mailPassword);
}

function mailConfigured(): bool {
    return MAIL_HOST !== ""
        && MAIL_PORT > 0
        && filter_var(MAIL_FROM, FILTER_VALIDATE_EMAIL)
        && MAIL_FROM !== "yourgmail@gmail.com"
        && MAIL_USERNAME !== ""
        && MAIL_USERNAME !== "yourgmail@gmail.com"
        && MAIL_PASSWORD !== "xxxx xxxx xxxx xxxx"
        && MAIL_PASSWORD !== "";
}

function setLastEmailError(string $message): void {
    $GLOBALS["sirchef_last_email_error"] = $message;
}

function lastEmailError(): string {
    return $GLOBALS["sirchef_last_email_error"] ?? "";
}

function sendEmail(string $toEmail, string $subject, string $htmlBody, string $replyTo = ""): bool {
    if (!mailConfigured()) {
        setLastEmailError("SMTP settings are incomplete. Check host, port, sender email, username, and app password.");
        error_log("SirChef email not sent because SMTP settings are incomplete. Subject: " . $subject . " To: " . $toEmail);
        return false;
    }

    if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
        setLastEmailError("Recipient email address is invalid.");
        error_log("SirChef email not sent because recipient is invalid. Subject: " . $subject . " To: " . $toEmail);
        return false;
    }

    setLastEmailError("");
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        if (MAIL_ENCRYPTION === "ssl") {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif (MAIL_ENCRYPTION === "tls") {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPSecure = "";
        }
        $mail->Port = MAIL_PORT;
        $mail->CharSet = "UTF-8";
        $mail->Timeout = 20;

        $mail->setFrom(MAIL_FROM, MAIL_NAME);
        if ($replyTo !== "" && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $mail->addReplyTo($replyTo);
        }
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = strip_tags($htmlBody);
        $mail->send();
        return true;
    } catch (Exception $e) {
        $error = $mail->ErrorInfo ?: $e->getMessage();
        setLastEmailError($error);
        error_log("PHPMailer Error: " . $error);
        return false;
    }
}

function emailLayout(string $title, string $body): string {
    return "
    <div style='font-family:Arial,sans-serif;max-width:560px;margin:auto;border:1px solid #eee;border-radius:12px;overflow:hidden;background:#fffdf8'>
      <div style='background:#2d3047;color:#ffd166;padding:24px;text-align:center;font-size:26px;font-weight:bold'>SirChef</div>
      <div style='padding:28px;color:#333'>
        <h2 style='margin-top:0;color:#2d3047'>{$title}</h2>
        {$body}
      </div>
      <div style='background:#f8f2e8;color:#777;padding:14px;text-align:center;font-size:12px'>SirChef recipe community</div>
    </div>";
}

function welcomeEmail(string $firstName): string {
    return emailLayout("Welcome, " . htmlspecialchars($firstName), "<p>Your SirChef account is verified. You can now search with more ingredients, save recipes, share posts, follow cooks, and join cooking chats.</p>");
}

function loginAlertEmail(string $firstName): string {
    return emailLayout("New login detected", "<p>Hi " . htmlspecialchars($firstName) . ", your SirChef account was accessed on " . date("F j, Y g:i A") . ".</p><p>If this was not you, reset your password immediately.</p>");
}

function sendVerificationEmail(string $email, string $firstName, string $code): bool {
    return sendEmail($email, "Your SirChef verification code", emailLayout("Verify your email", "<p>Hi " . htmlspecialchars($firstName) . ", use this code to verify your SirChef account:</p><p style='font-size:32px;font-weight:bold;letter-spacing:6px;color:#e76f51'>{$code}</p><p>This code expires in 15 minutes.</p>"));
}

function sendPasswordResetEmail(string $email, string $firstName, string $code): bool {
    return sendEmail($email, "Your SirChef password reset code", emailLayout("Reset your password", "<p>Hi " . htmlspecialchars($firstName) . ", use this code to reset your password:</p><p style='font-size:32px;font-weight:bold;letter-spacing:6px;color:#e76f51'>{$code}</p><p>This code expires in 15 minutes.</p>"));
}
?>
