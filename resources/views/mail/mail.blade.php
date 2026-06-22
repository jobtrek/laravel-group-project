<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Progress Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        .header {
            background-color: #2563eb;
            padding: 32px 40px;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .body {
            padding: 36px 40px;
            color: #374151;
            line-height: 1.7;
            font-size: 15px;
        }
        .body p {
            margin: 0 0 16px;
        }
        .cta {
            display: inline-block;
            margin-top: 8px;
            padding: 12px 28px;
            background-color: #2563eb;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
        }
        .footer {
            padding: 20px 40px;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Project Progress Reminder</h1>
        </div>
        <div class="body">
            <p>Dear {{ $name }},</p>

            <p>
                We hope this message finds you well. This is a friendly reminder to log any recent progress
                on your assigned projects within the project management platform.
            </p>

            <p>
                Keeping your project status up to date helps the whole team stay aligned, ensures accurate
                reporting, and allows project leads to provide support where it is needed most.
            </p>

            <p>
                If your project has seen any developments — milestones reached, tasks completed, blockers
                identified, or next steps planned — please take a moment to record them at your earliest
                convenience.
            </p>

            <p>
                If you have any questions or need assistance, do not hesitate to reach out to your project
                lead or team coordinator.
            </p>

            <p>Thank you for your continued efforts and dedication.</p>

            <p>
                Warm regards,<br>
                <strong>The Project Management Team</strong>
            </p>
        </div>
        <div class="footer">
            This is an automated reminder. Please do not reply directly to this email.
        </div>
    </div>
</body>
</html>
