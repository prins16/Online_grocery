
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Message Sent - Online Grocery</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #e8f5e9, #ffffff);
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-box {
            background: white;
            width: 90%;
            max-width: 550px;

            padding: 50px 35px;

            text-align: center;

            border-radius: 18px;

            box-shadow: 0 8px 30px rgba(0,0,0,0.10);
        }

        .success-icon {
            width: 80px;
            height: 80px;

            margin: 0 auto 20px;

            border-radius: 50%;

            background: #e8f5e9;

            color: #2e7d32;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 42px;
        }

        h1 {
            color: #1b5e20;
            margin-bottom: 15px;
        }

        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            text-decoration: none;

            padding: 12px 22px;

            border-radius: 8px;

            font-weight: bold;

            transition: 0.2s;
        }

        .home-btn {
            background: #2e7d32;
            color: white;
        }

        .home-btn:hover {
            background: #1b5e20;
        }

        .contact-btn {
            background: #f1f8f2;
            color: #2e7d32;
        }

        .contact-btn:hover {
            background: #dcedc8;
        }

    </style>

</head>

<body>

    <div class="success-box">

        <div class="success-icon">
            ✓
        </div>

        <h1>
            Message Sent Successfully!
        </h1>

        <p>
            Thank you for contacting Online Grocery.
            Your message has been received successfully.
            We will get back to you soon.
        </p>

        <div class="buttons">

            <a
                href="index.php"
                class="btn home-btn"
            >
                🏠 Back to Home
            </a>

            <a
                href="contact.php"
                class="btn contact-btn"
            >
                📩 Send Another Message
            </a>

        </div>

    </div>

</body>

</html>
