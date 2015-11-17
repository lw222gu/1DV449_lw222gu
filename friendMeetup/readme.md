# Reflektionsfrågor, laboration 1

## Finns det några etiska aspekter vid webbskrapning. Kan du hitta något rättsfall?

## Finns det några riktlinjer för utvecklare att tänka på om man vill vara "en god skrapare" mot serverägarna?
Man bör alltid tala om vem man är som skrapar sidan.

## Begränsningar i din lösning- vad är generellt och vad är inte generellt i din kod?
Min lösning är generell på det sättet att den skulle fungera även om ytterligare personer lades till bland vännerna.
Den är också generell i det att filmerna kan förändras, både i antal och i titlar och ändå fungera. Filmerna är liksom
vännerna inte hårdkodade någonstans.
Lösningen är inte generell på det viset att den inte fungerar om flera restauranger skulle
vara aktuella, och så även flera biografer. Den är inte heller generell när det gäller vilka dagar som kan vara
aktuella, utan skrapan fungerar enbart för fredag, lördag och söndag, då dessa värden finns hårdkodade i lösningen.
I kraven specificeras dock att dagarna aldrig kommer att förändras, så det bör inte vara något som påverkar applikationen.

## Vad kan robots.txt spela för roll?