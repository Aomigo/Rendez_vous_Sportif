<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport d'activité</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #1f2937; line-height: 1.5; padding: 24px;">
    <h2>Bonjour {{ $report->ownerName }},</h2>

    <p>Hier, votre événement a suscité un fort intérêt :</p>

    <div style="background-color: #f3f4f6; border-radius: 8px; padding: 16px; margin: 20px 0;">
        <p style="margin: 0 0 8px 0;"><strong>Événement :</strong> {{ $report->eventName }}</p>
        <p style="margin: 0;"><strong>Nouvelles inscriptions confirmées ({{ $report->date->translatedFormat('d F Y') }}) :</strong> {{ $report->registrationsCount }}</p>
    </div>

    <p>L'équipe support</p>
</body>
</html>

