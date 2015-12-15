Projektet finns live på [skola.lisawestlund.se](http://skola.lisawestlund.se/TrafficMessagesMap/).

# Reflektionsfrågor, laboration 3

## Vad finns det för krav du måste anpassa dig efter i de olika API:erna?
### Sveriges Radios öppna API
Sveriges Radios öppna API får inte användas på ett sätt som kan skada Sveriges Radios oberoende eller trovärdighet. API:t har inga begränsningar i antal anrop, men de ber användare av API:t att göra så få anrop som möjligt.

### OpenStreetMap
OpenStreetMap är licensierat under Creative Commons-licensen CC BY-SA 2.0. Därmed krävs att applikationen talar om copyright-information så som var datan kommer från.

## Hur och hur länge cachar du ditt data för att slippa anropa API:erna i onödan?
Den json jag hämtar ut från SR:s API sparar jag till en json-fil på servern. Om en förfrågan görs inom fem minuter från att datan senast hämtades från SR så visas den redan lagrade informationen. Förfrågningar som görs efter fem minuter hämtar ny information.

Att jag valt just fem minuter är för att det ändå är relativt viktigt att få denna information så uppdaterad som möjligt, men inom rimlighetens gräns. Är jag på resande fot vill jag kunna se aktuell information för att kunna välja en väg eller en annan.

## Vad finns det för risker kring säkerhet och stabilitet i din applikation?
Riskerna ligger framför allt i att jag inte har kontroll över API:erna. Går något API ner så blir applikationen inte särskilt användbar. Jag har inte heller kontroll över den data som skickas till mig, vilket självklart möjliggör för att jag skulle kunna ta emot skadlig kod via dem. Skulle SR inte returnera någon data visas ett felmeddelande tillsammans med tidigare hämtad data, och tidpunkt för när den hämtades.

## Hur har du tänkt kring säkerheten i din applikation?
Eftersom applikationen varken hanterar användarinput eller loginfunktionalitet har jag inte tänkt så mycket kring säkerheten. All data som används är öppen data, och det är ingen känslig data som hanteras.

## Hur har du tänkt kring optimeringen i din applikation?
* Datan från SR:s API lagras i fem minuter, vilket gör att den finns snabbt tillgänglig för de flesta, och det är bara den användare som först besöker sidan efter att en femminutersperiod avslutats som får vänta på att datan hämtas.
* Eftersom trafikmeddelandena sorteras efter datum gör jag denna sortering direkt i serversidekoden när jag hämtat datan. På så vis behöver detta bara göras en gång var femte minut, och inte för varje användare som besöker sidan när datan redan finns lagrad.
* Applikationen Gzip:as. Storleken minskas med omkring 70 procent enligt test via [GIDNetwork](http://www.gidnetwork.com/tools/gzip-test.php).
* Jag läser inte in onödiga resurser:
   * Jag använder ramverket Foundation, men enbart för den responsiva gridden. Därför har jag valt att enbart ladda ner css för just gridden, och inte för hela ramverket. Jag använder Foundations minifierade css.
   * Mina ikoner finns samlade i en svg-sprite. Då jag har 24 ikoner som har exakt samma storlek var det lätt att fixa en sprite för dessa 24 och därmed minska http-anropen från 24 till 1.
   * Jag har försökt att till så stor del som möjligt skriva css:en kompakt, men här finns självklart förbättringsmöjligheter.
   * Css läses in i head-elementet, och JavaScript läses in sist i bodyn.
   * Jag använder minifierade versioner av min egna JavaScript- och css-filer.
   * Vid laddning av sidan zoomar kartan in till att visa mellansverige, inklusive Stockholm, då jag gissar att merparten av användarna kommer befinna sig där. Det gör att onödiga kartbilder inte behöver läsas in. Vid laddning av sidan är det också möjligt att ge applikationen tillgång till din plats, vilket resulterar i att den zoomar in där du befinner dig.
   * Det är inte möjligt att zooma ut kartan mer än att Sverige får plats på höjden. Det går förvisso att sen flytta sig runt i hela världen, men det undviker åtminstone att användare zoomar ut till hela världen av misstag, och på så sätt hämtar onödiga kartbilder.

### Förbättringar
Jag är medveten om att ett par saker skulle kunna göras bättre i min applikation:
* När man filtrerar på kategorier raderar min applikation alla markers och meddelanden, för att sedan skriva ut de som ska visas på nytt igen. Detta är såklart onödigt. Leafletjs har någon funktion där man kan lägga till markers till olika lager, och sedan välja att visa eller dölja lagren. Men det hjälper mig dock inte när det kommer till meddelandena eftersom jag valt att även filtrera dessa.
* Listan med meddelanden visar alla meddelanden som finns i respektive kategori. Mer användarvänligt vore det såklart om den listade några stycken, med en knapp för att visa fler. Det skulle också spara på antalet element som behöver renderas ut.
