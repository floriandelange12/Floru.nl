# Wijzigbare bestanden

Deze map is bedoeld als centrale werkmap voor de bestanden die je in deze site hoort aan te passen.

## Wat hier gebeurt

De map `astra-child/` in deze map wordt niet gevuld met gewone kopieën, maar met gekoppelde verwijzingen naar de originele bestanden in `wp-content/themes/astra-child/`:
- mappen worden aangemaakt als junctions
- losse bestanden worden aangemaakt als hardlinks

Daardoor blijven wijzigingen twee kanten op hetzelfde:
- wijzig je iets in `wijzigbare bestanden/astra-child/`, dan wijzig je meteen het originele bestand
- wijzig je iets in het originele bestand, dan zie je dat ook hier terug

## Eerst uitvoeren

Start bij voorkeur dit bestand:
- `maak-links.cmd`

Alleen als dat niet werkt, gebruik dan:
- `maak-links.ps1`

Na het uitvoeren krijg je deze werkmap:
- `wijzigbare bestanden/astra-child/style.css`
- `wijzigbare bestanden/astra-child/functions.php`
- `wijzigbare bestanden/astra-child/footer.php`
- `wijzigbare bestanden/astra-child/assets/`
- `wijzigbare bestanden/astra-child/inc/`
- `wijzigbare bestanden/astra-child/template-parts/`
- `wijzigbare bestanden/astra-child/templates/`

## Belangrijk

Bewerk nog steeds alleen het child theme. WordPress core, `wp-admin/`, `wp-includes/` en het parent theme `wp-content/themes/astra/` horen hier niet in thuis.
