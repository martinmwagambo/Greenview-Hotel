<?php
/**
 * Email Configuration Test Script
 * Use this to diagnose email sending issues
 * Access it at: http://localhost/greenviewhotel/test_email.php
 */

echo "<h2>GreenView Hotel - Email Configuration Test</h2>";
echo "<hr>";

// Load config
$config = parse_ini_file('includes/config.ini', true);

echo "<h3>Configuration Check:</h3>";
echo "Email configured: " . (isset($config['user']) ? "<span style='color:green;'>✓ " . $config['user'] . "</span>" : "<span style='color:red;'>✗ Missing</span>") . "<br>";
echo "Password configured: " . (isset($config['pass']) ? "<span style='color:green;'>✓ Configured</span>" : "<span style='color:red;'>✗ Missing</span>") . "<br>";

// Try to load PHPMailer
echo "<h3>PHPMailer Check:</h3>";
if (file_exists('PHPMailer/PHPMailerAutoload.php')) {
    require 'PHPMailer/PHPMailerAutoload.php';
    echo "<span style='color:green;'>✓ PHPMailer found</span><br>";
} else {
    echo "<span style='color:red;'>✗ PHPMailer not found</span><br>";
    exit;
}

// Test SMTP Connection
echo "<h3>SMTP Connection Test:</h3>";
$mail = new PHPMailer;
$mail->isSMTP();
$mail->SMTPDebug = 2; // Show detailed debug info
$mail->Host = 'smtp.gmail.com';
$mail->Port = 587;
$mail->SMTPSecure = 'tls';
$mail->SMTPAuth = true;
$mail->Username = $config['user'];
$mail->Password = $config['pass'];

// Start output buffering to capture debug output
ob_start();
$connection_test = @$mail->smtpConnect();
$debug_output = ob_get_clean();

if ($connection_test === true) {
    echo "<span style='color:green;'>✓ SMTP Connection Successful</span><br>";
} else {
    echo "<span style='color:red;'>✗ SMTP Connection Failed</span><br>";
    echo "<h4>Error Details:</h4>";
    echo "<pre style='background:#f0f0f0; padding:10px; color:red;'>";
    echo htmlspecialchars($debug_output);
    echo "</pre>";
}

// Try a test email send
if ($connection_test === true) {
    echo "<h3>Test Email Send:</h3>";
    
    $test_email = isset($_POST['test_email']) ? $_POST['test_email'] : null;
    
    if ($test_email) {
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 2;
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        $mail->Username = $config['user'];
        $mail->Password = $config['pass'];
        
        $mail->setFrom($config['user'], 'GreenView Hotel');
        $mail->addAddress($test_email, 'Test User');
        $mail->Subject = 'GreenView Hotel - Test Email';
        $mail->Body = 'This is a test email from your registration system.';
        
        ob_start();
        $send_result = $mail->send();
        $send_output = ob_get_clean();
        
        if ($send_result) {
            echo "<span style='color:green;'>✓ Test Email Sent Successfully to " . htmlspecialchars($test_email) . "</span><br>";
        } else {
            echo "<span style='color:red;'>✗ Failed to send test email</span><br>";
            echo "<h4>Error Details:</h4>";
            echo "<pre style='background:#f0f0f0; padding:10px; color:red;'>";
            echo htmlspecialchars($send_output);
            echo "</pre>";
            echo "<br><strong>Error Info:</strong> " . htmlspecialchars($mail->ErrorInfo) . "<br>";
        }
    } else {
        echo "<form method='POST'>";
        echo "Test email address: <input type='email' name='test_email' required>";
        echo " <button type='submit'>Send Test Email</button>";
        echo "</form>";
    }
}

echo "<hr>";
echo "<h3>Common Issues & Solutions:</h3>";
echo "<ul>";
echo "<li><strong>Authentication Failed:</strong> Your Gmail App Password may be expired or incorrect. Generate a new one at <a href='https://myaccount.google.com/apppasswords' target='_blank'>myaccount.google.com/apppasswords</a></li>";
echo "<li><strong>Connection Timeout:</strong> Check if your hosting provider blocks SMTP port 587</li>";
echo "<li><strong>STARTTLS Error:</strong> Try changing SMTPSecure from 'tls' to 'ssl' and Port from 587 to 465</li>";
echo "</ul>";
?>
