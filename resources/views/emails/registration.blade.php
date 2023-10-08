<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registration Confirmation</title>
    <style>
        /* Add some basic styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        p {
            font-size: 16px;
            line-height: 1.5;
            color: #666;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to ProjectHub.earth</h1>
        <p>Dear {{ $user->firstname }} {{ $user->lastname }},</p>

        <p>Thank you for joining ProjectHub.earth! Your account has been successfully created.</p>

        <p>If you have any questions or need assistance, feel free to contact our support team.</p>

        <p>Best regards,<br>Your ProjectHub.earth Team</p>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} ProjectHub.earth. All rights reserved.
    </div>
</body>
</html>
