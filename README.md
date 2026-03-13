# kiosk-jop

Web-based self-service kiosk for Happy Herbivore. Includes customer ordering flow, kitchen display, admin analytics, and a PHP/MySQL API.

## Wat dit is

`kiosk-jop` is een touchscreen kiosk-app voor horeca:

- Klant start op het welkomscherm
- Klant kiest `eat-in` of `take-away`
- Klant bekijkt menu, voegt producten toe en rekent af
- Keuken ziet live orders en kan status updaten
- Admin ziet omzet en topverkopers

## Stack

- PHP (zonder framework)
- MySQL/MariaDB
- HTML/CSS/vanilla JavaScript
- XAMPP (Apache + MySQL)

## Belangrijkste Pagina's

- `index.php`: start-/screensaver-scherm
- `mode.php`: keuze eetmodus
- `menu.php`: categorieen, producten en winkelmand
- `checkout.php`: checkout + mock payment + orderopslag
- `kitchen.php`: keukenoverzicht (polling)
- `admin.php`: dashboard en analytics
- `api-test.html`: browserdashboard om API te testen

## Snel Starten (Lokaal)

1. Plaats het project in `c:\xampp\htdocs\kiosk-jop`.
2. Start Apache en MySQL in XAMPP.
3. Zet databasegegevens in `config/db.php`.
4. Zorg dat de database de tabellen bevat die de app gebruikt:
   - `categories`, `products`, `images`
   - `orders` met o.a. `order_status_id`, `price_total`, `pickup_number`, `datetime`
   - `order_product`
5. Optioneel: vul of corrigeer data via scripts:
   - `fix_db.php` voor product/image-data
   - `setup_upsells.php` voor cross-sell relaties
6. Open `http://localhost/kiosk-jop/index.php`.

## API

Base URL:

`http://localhost/kiosk-jop/api/`

Uitgebreide API docs:

- `api/README.md`

Veelgebruikte endpoints:

- `get_products.php`
- `create_order_fixed.php`
- `get_orders_fixed.php`
- `get_order.php`
- `update_order_fixed.php`
- `delete_order.php`

API testen in browser:

- `http://localhost/kiosk-jop/api-test.html`

## Let Op: Legacy vs Fixed Endpoints

In `api/` staan zowel legacy- als `*_fixed` varianten.

- Voor nieuwe koppelingen: gebruik bij voorkeur de `*_fixed` endpoints.
- Bestaande UI-schermen gebruiken deels nog legacy-routes (`save_order.php`, `get_orders.php`, `update_order.php`).

## Handige Onderhoudsscripts

- `inspect_db.php`: toont tabelstructuur
- `check_products.php`: checkt products + images
- `list_items.php`: snelle dump van categorieen en producten
- `auto_fix_images.php`, `fix_cat_names.php`, `fix_prices.php`: data/image-fixes

## Security Opmerking

Haal gevoelige databasecredentials uit versiebeheer (of gebruik een lokale override) voordat je dit project deelt of deployed.

## Licentie

Geen expliciete licentie aanwezig in deze repository.
