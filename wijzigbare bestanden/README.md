# Wijzigbare bestanden

Deze map is de export- en werkmap voor het child theme.

## Wat hier standaard in staat

Standaard bevat `astra-child/` een volledige kopie van het actieve child theme uit:
- `wp-content/themes/astra-child/`

Daardoor kun je deze map direct gebruiken om bestanden te bekijken, te delen of in een ZIP te stoppen.

## Live gekoppeld werken

Wil je liever in een gekoppelde werkmap werken, zodat wijzigingen direct doorlopen naar het actieve theme?
Gebruik dan een van deze scripts:
- `maak-links.cmd`
- `maak-links.ps1`

Die vervangen `wijzigbare bestanden/astra-child/` door:
- hardlinks voor losse bestanden
- junctions voor mappen

## Inhoud van de werkmap

Na kopieren of linken hoort deze map minimaal te bevatten:
- `wijzigbare bestanden/astra-child/style.css`
- `wijzigbare bestanden/astra-child/functions.php`
- `wijzigbare bestanden/astra-child/footer.php`
- `wijzigbare bestanden/astra-child/single-floru_client.php`
- `wijzigbare bestanden/astra-child/assets/`
- `wijzigbare bestanden/astra-child/inc/`
- `wijzigbare bestanden/astra-child/template-parts/`
- `wijzigbare bestanden/astra-child/templates/`

## ZIP export

Het bestand `astra-child.zip` hoort een complete export van het child theme te zijn.
Gebruik `wp-content/themes/astra-child/` als bron van waarheid en bouw de ZIP opnieuw nadat daar wijzigingen zijn gedaan.

## Belangrijk

Bewerk alleen het child theme. WordPress core, `wp-admin/`, `wp-includes/` en het parent theme `wp-content/themes/astra/` horen hier niet in thuis.
