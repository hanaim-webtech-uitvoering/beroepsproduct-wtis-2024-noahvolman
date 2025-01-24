<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacyverklaring</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, h2 {
            color: #333;
        }
        p {
            line-height: 1.6;
        }
        .back-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Privacyverklaring</h1>
    <p>Wij hechten veel waarde aan de bescherming van uw persoonsgegevens. In deze privacyverklaring willen we heldere en transparante informatie geven over hoe wij omgaan met persoonsgegevens.</p>

    <h2>Verwerkingsdoeleinden</h2>
    <p>Wij verwerken uw persoonsgegevens voor de volgende doeleinden:</p>
    <ul>
        <li>Het afhandelen van uw betaling</li>
        <li>Verzenden van onze nieuwsbrief en/of reclamefolder</li>
        <li>U te kunnen bellen of e-mailen indien dit nodig is om onze dienstverlening uit te kunnen voeren</li>
        <li>Om goederen en diensten bij u af te leveren</li>
    </ul>

    <h2>Bewaartermijn</h2>
    <p>Wij bewaren uw persoonsgegevens niet langer dan strikt nodig is om de doelen te realiseren waarvoor uw gegevens worden verzameld.</p>

    <h2>Delen met derden</h2>
    <p>Wij verstrekken uw gegevens uitsluitend aan derden indien dit nodig is voor de uitvoering van onze overeenkomst met u of om te voldoen aan een wettelijke verplichting.</p>

    <h2>Cookies</h2>
    <p>Wij gebruiken alleen technische en functionele cookies. En analytische cookies die geen inbreuk maken op uw privacy.</p>

    <h2>Gegevens inzien, aanpassen of verwijderen</h2>
    <p>U heeft het recht om uw persoonsgegevens in te zien, te corrigeren of te verwijderen. Daarnaast heeft u het recht om uw eventuele toestemming voor de gegevensverwerking in te trekken of bezwaar te maken tegen de verwerking van uw persoonsgegevens door ons bedrijf.</p>

    <h2>Contact</h2>
    <p>Voor vragen over deze privacyverklaring of over onze verwerking van uw persoonsgegevens kunt u contact met ons opnemen via:</p>
    <p>Email: info@uwbedrijf.nl</p>
    <p>Telefoon: 012-3456789</p>

    <?php if (isset($_SERVER['HTTP_REFERER'])): ?>
        <a href="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER']); ?>" class="back-link">Ga terug</a>
    <?php endif; ?>
</body>
</html>