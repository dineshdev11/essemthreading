<?php
// contact.php
function verifyCaptcha($captchaResponse) {
    $secretKey = "6LdmFN4rAAAAAJ3sCIV2AMMIylU__lSzOaAFpO3B2"; // your secret key
    $verifyURL = "https://www.google.com/recaptcha/api/siteverify";

    $data = [
        'secret'   => $secretKey,
        'response' => $captchaResponse
    ];

    $options = [
        "http" => [
            "header"  => "Content-type: application/x-www-form-urlencoded\r\n",
            "method"  => "POST",
            "content" => http_build_query($data),
        ]
    ];

    $context  = stream_context_create($options);
    $result   = file_get_contents($verifyURL, false, $context);
    $response = json_decode($result);

    return $response && $response->success;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $name = htmlspecialchars(trim($_POST['name']));
    $company = htmlspecialchars(trim($_POST['company']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));
    
    // Validation
    $errors = [];
    
    // Required fields validation
    if (empty($name)) $errors[] = "Full Name is required";
    if (empty($email)) $errors[] = "Email Address is required";
    if (empty($phone)) $errors[] = "Phone Number is required";
    if (empty($subject)) $errors[] = "Subject is required";
    if (empty($message)) $errors[] = "Message is required";
    
    // Email validation
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // If there are errors, return them
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }
    
    // Email configuration
    $to1 = "essemtap13@gmail.com";
    $to2 = "contact.in.dinesh@gmail.com";
    
    // Email subject
    $email_subject = "New Contact Form Submission: " . $subject;
    
    // Email headers
    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // Email body
    $email_body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #f8f9fa; padding: 15px; border-radius: 5px; }
            .field { margin-bottom: 10px; padding: 8px 0; border-bottom: 1px solid #eee; }
            .label { font-weight: bold; color: #555; }
            .value { color: #333; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Contact Form Submission</h2>
                <p><strong>Subject:</strong> $subject</p>
            </div>
            
            <div class='field'>
                <span class='label'>Full Name:</span>
                <span class='value'>$name</span>
            </div>
            
            <div class='field'>
                <span class='label'>Company Name:</span>
                <span class='value'>" . ($company ? $company : 'Not provided') . "</span>
            </div>
            
            <div class='field'>
                <span class='label'>Email Address:</span>
                <span class='value'>$email</span>
            </div>
            
            <div class='field'>
                <span class='label'>Phone Number:</span>
                <span class='value'>$phone</span>
            </div>
            
            <div class='field'>
                <span class='label'>Subject:</span>
                <span class='value'>$subject</span>
            </div>
            
            <div class='field'>
                <span class='label'>Message:</span>
                <div class='value' style='margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 5px;'>
                    " . nl2br($message) . "
                </div>
            </div>
            
            <div style='margin-top: 20px; padding: 15px; background: #e9ecef; border-radius: 5px;'>
                <small>This email was sent from the contact form on your website.</small>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Send emails
    $sent1 = mail($to1, $email_subject, $email_body, $headers);
    $sent2 = mail($to2, $email_subject, $email_body, $headers);
    
    // Check if emails were sent successfully
    if ($sent1 && $sent2) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Thank you! Your message has been sent successfully.']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Sorry, there was an error sending your message. Please try again.']);
    }
    
} else {
    // If not POST request
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>