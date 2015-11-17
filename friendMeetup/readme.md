# Reflektionsfrågor, laboration 1

## Finns det några etiska aspekter vid webbskrapning. Kan du hitta något rättsfall?
Överlag gäller det att inte ställa till det för ägaren av de webbplatser man väljer att skrapa, och fundera kring frågor som:
* Hur mycket kommer min skrapa påverka trafiken till sidan? Kan den påverka det såpass att användare av sidan jag skrapar
kommer uppleva den som långsammare?
* Kan min skrapa öka trafiken såpass mycket att trafiken överstiger den mängd trafik som sidägaren har tillgång till, och
 på så vis leda till merkostnader för sidägaren?

Sen är det ju så att precis som med all data man väljer att tillgängliggöra måste man även med redan tillgänglig data tänka igenom i
vilken kontext man presenterar den. Bara för att data är offentlig är det inte säkert att det är etiskt försvarbart
att tillgängliggöra den ännu mer hur som helst, och i vilka sammanhang som helst.

När det kommer till rättsfall är "eBay v.s. Bidder´s Edge" ett välkänt exempel. Bidder´s Edge var en samlingssida som listade
olika auktioner på webben och gjorde det möjligt för användare att slippa besöka många olika auktionssidor. Bland annat hämtade de
data från eBay. eBay och Bidder´s Edge försökte komma överens om hur detta skulle ske rent tekniskt, men när det misslyckades
och Bidder´s Edge ändå till slut valde att fortsätta hämta, indexera och presentera data från eBay till sina egna användare
valde eBay att stämma Bidder´s Edge. Det hela slutade i en överenskommelse där Bidder´s Edge ersatte eBay och lovade att inte
använda eBays data mer. Samtidigt stängde Bidder´s Edge ner sin webbplats.

Ett annat rättsfall var det mellan Facebook och power.com. Power.com var en tjänst som skulle användas till att samla alla sina
sociala medier på ett och samma ställe. De skrapade därför bland annat Facebook för att ta del av användargenererad information,
och trots att Facebook inte äger rätten till enskilda användares information, menade de i sin stämning av power.com att power.com
använde sig av den information och data som var Facebooks och som fanns "runtom" det användargenererade innehållet.

---

## Finns det några riktlinjer för utvecklare att tänka på om man vill vara "en god skrapare" mot serverägarna?
* Undersök alltid om sidan du tänker skrapa har några Terms of Use som sätter upp riktlinjer kring skrapning, och se även
vad som anges i robots.txt.
* Tala alltid om vem du är som skrapar sidan och hur sidägaren kan nå dig.
* Tänk till när det kommer till upphovsrättsskyddat material - en sidägare som publicerat ett foto kanske inte ger dig rätten
att publicera det vidare.
* Tänk till kring de etiska aspekterna nämnda ovan.

---

## Begränsningar i din lösning- vad är generellt och vad är inte generellt i din kod?
Min lösning är generell på det sättet att den skulle fungera även om ytterligare personer lades till bland vännerna.
Den är också generell i det att filmerna kan förändras, både i antal och i titlar och ändå fungera. Filmerna är liksom
vännerna inte hårdkodade någonstans.
Lösningen är inte generell när det gäller vilka dagar som kan vara aktuella, utan skrapan fungerar enbart för fredag,
lördag och söndag, då dessa värden finns hårdkodade i lösningen. I kraven specificeras dock att dagarna aldrig kommer
att förändras, så det bör inte vara något som påverkar applikationen.

---

## Vad kan robots.txt spela för roll?
Med en robots.txt-fil kan man ge robotar instruktioner om sin sida. När en robot besöker en sida kontrollerar den
först robots.txt, där man som ägare av sidan har möjlighet att ange "Disallow: /" för att tala om för roboten
att den inte ska besöka några sidor på denna webbplats. (Viktigt att komma ihåg är att robotar kan ignorera detta.) 
Genom att ange olika mappar kan man också tala om för robotar att just de mapparna inte ska besökas.