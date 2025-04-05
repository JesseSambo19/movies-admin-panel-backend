<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reset Your Password</title>
</head>

<body>
    <p>Hello {{ $name }},</p>

    <p>We received a request to reset your password. Click the button below to reset it:</p>

    <p><a href="{{ $resetLink }}"
            style="background: #230052; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Reset
            Password</a></p>

    <p>This link will expire in 60 minutes.</p>

    <p>If you didn't request a password reset, you can safely ignore this email.</p>

    <p>Regards,<br>Movies</p>

    <p>
        If you're having trouble clicking the "Reset Password" button, copy and paste the URL below
        into your web browser: <br>
        {{ $resetLink }}
    </p>
</body>

</html>