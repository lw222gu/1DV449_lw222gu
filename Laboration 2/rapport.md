# Analys och förbättringsförslag ur säkerhets- och prestandasynpunkt på applikationen Labby Message

Rapport skriven av Lisa Westlund (lw222gu).

Laboration 2 i kursen Webbteknik II, vid Linnéuniversitetet.

Rapporten är indelad i tre delar: Säkerhetsproblem, Prestandaproblem och Personliga reflektioner

---
## Säkerhetsproblem

### Problem 1: SQL Injections
Applikationen möjliggör för SQL Injections. T.ex. är det möjligt att logga in genom att ange ett existerande användarnamn, och som lösenord ange "' OR '1'='1". Du behöver alltså inte vara en autentiserad användare, utan du kan komma åt innehållet via en annan användares användarnamn, och då utföra aktioner i dennes namn.

Utan att veta vad applikationen har för rättigheter i databasen spekulerar jag i att det är möjligt att i kombination med ovanstående skicka in skadlig kod i databasen, t.ex. att radera den. Detta är fullt möjligt om användarens rättigheter inte är begränsade, så som applikationen är byggd i nuläget.

#### Om SQL Injections
SQL Injections i stort innebär skjuta in skadlig kod via input-fält i en applikation [Referens: https://technet.microsoft.com/en-us/library/ms161953(v=SQL.105).aspx]. Detta kan göras för att få ta del av databasen, autentisera sig som en annan användare, radera databasen, manipulera innehållet i databasen, etc. [Referens till https://en.wikipedia.org/wiki/SQL_injection]. Injections anses av [OWASP 2013] vara den vanligaste säkerhetsbristen i webbapplikationer. [OWASP 2013] pekar också på att ett resultat av injections som leder till förlust av data, manipulation av data, nekad åtkomst till applikationen, etc. kan påverka applikationens och applikationsägarens rykte. Detta är givetvis delvis beroende av datans värde i sig.

#### Förhindra SQL Injections
SQL Injections förhindras enklast genom att använda parameteriserade frågor med validerad data vid kontakt med databasen, i stället för att låta användarinput användas direkt i SQL-satserna. Det är också möjligt att använda sig av en teknik som benämns som "escaping". Escaping innebär att tecken som kan ha en särskild betydelse i SQL-frågor översätts till något annat. Genom att ta reda på vilka tecken som kan anses ha en särskild betydelse för just den databas som används är det möjligt att svartlista dessa tecken, och på så vis skydda sig mot SQL Injections. Att svartlista något innebär dock en betydande risk för att missa något viktigt, och på så vis ändå öppna för attacker. [Referens till https://en.wikipedia.org/wiki/SQL_injection]

Applikationer bör också ha begränsade rättigheter till databasen i fråga, och då enbart ha rättighet att utföra det som krävs för applikationen. På så vis kan man undvika att t.ex. göra det möjligt att vid en attack radera hela databasen. [Referens till https://en.wikipedia.org/wiki/SQL_injection]

Det är också av största vikt att alltid validera all användargenererad data. Några punkter att kontrollera listas nedan:
[Referens: https://technet.microsoft.com/en-us/library/ms161953(v=SQL.105).aspx]
* Validera alltid datatyp och storlek på input-data, mot begränsningar som är rimliga för din applikation.
* Validera innehållet i strängar, och neka användaren att ange tecken du inte anser vara ok. Detta kan t.ex. vara tecken som `;`, `'`, `--`, `/*...*/`, etc.
* Validera användarinputs även i lagrade procedurer.
* Validering ska ske i alla lager av applikationen, såväl på klienten som på servern och i datalagret.


#### Ändringar i applikationen beträffande SQL Injections
Applikationen bör förändras genom att använda sig av parameteriserade frågor till databasen via lagrade procedurer, i stället för att som i login-fallet konkatenera ihop strängar av SQL-frågor och användarinput. Applikationen bör också validera den data som användare kan posta.


### Problem 2: Hijacking p.g.a. dålig autentiserings- och sessionshantering
När en användare valt att logga ut är det möjligt att t.ex. genom webbläsarens bakåtknapp, eller genom att känna till url:en som används i inloggat läge, hamna i inloggat läge igen. I de fall en användare t.ex. använder sig av en publik dator, tror att den loggat ut korrekt, och sedan lämnar datorn igång utan att stänga webbläsaren korrekt (och på så sätt döda sessionen), är det möjligt för nästa person att enbart klicka på tillbakaknappen, eller ange url:en http://localhost:3000/message för att åter vara inloggad i den första användarens namn. Så länge sessionen är i liv är detta möjligt. Detta beror på att sessions-id:n inte ändras efter inlogg och att sessionen inte avslutas vid utlogg.

All information skickas okrypterad via http istället för via https.

Förutom ovanstående, så har jag en stark anledning att tro att lösenord lagras i klartext. Detta antagande baseras på att när inloggningen sker görs en kontroll mot databasen med den input som användaren själv anger. Det sker ingen verifiering mot hur det angivna lösenordet skulle se ut om det var hashat (eller krypterat).

Lösenordshanteringen och hanteringen av sessioner är starka orsaker till att applikationen är känslig för hijacking. [OWASP 2013] benämner problemet som Broken Authentication and Session Management, vilket också anges vara den näst vanligaste säkerhetsbristen i webbapplikationer.

#### Om hijacking
Hijacking innebär att en person kan komma över en användares konto, och utföra aktioner i dennes namn. En stark anledning till att detta är så vanligt förekommande är att många utvecklare väljer att skriva egna autentiseringsfunktioner, och då missar viktiga delar kring lösenordshantering, utloggning, sessionshantering m.m. Om en person kommer över en användares konto kan den utföra allt som användaren kan utföra, vilket gör att användare med mycket rättigheter i applikationen ofta är mer utsatta för denna typ av attack. [OWASP 2013]

#### Förhindra hijacking
För att undvika att användares konton hijackas finns några viktiga punkter att följa [OWASP 2013]:
* Se till att hasha användaruppgifter när de lagras persistent.
* Användaruppgifter ska inte vara lätta att gissa.
* Funktioner som att ändra lösenord, beställa nytt lösenord etc. måste även de vara implementerade med fokus på säkerhet.
* Sessions-id:n ska inte skickas med i url:er.
* Sessions-id:n ska time-outas, och förstöras efter utlogg.
* Sessions-id:n ska ändras efter en lyckad inloggning.
* Lösenord, sessions-id:n m.m. ska enbart skickas över krypterade anslutningar, t.ex. https.

I många fall kommer man långt på att använda redan existerande funktioner för att logga in, som t.ex. OAuth2, istället för att skriva egna autentiseringsfunktioner. Dessa är väl testade och har högre säkerhet än vad många utvecklare klarar att implementera på egen hand.

#### Ändringar i applikationen beträffande hijacking
* Applikationen bör hasha lösenord.
* Applikationen bör rotera sessions-id:n vid lyckad login och förstöra dem vid logout.
* Applikationen bör skicka känslig information via krypterade anslutningar, och då använda https istället för http.


### Problem 3:





### Kan skicka in html-taggar
Det är möjligt att som användare skriva html-kod i meddelanderutan.

---

## Prestandaproblem
### Onödig kod
Varför inkludera ett ramverk som Bootstrap när det ändå inte används?

### Css i html-filen
Lägg css i externa filer istället. Då kan de cacheas över sidor.

---

## Personliga reflektioner
I flera fall i rapporten har jag angett att jag spekulerar i frågan. Jag är ovan att läsa kod i node-projekt (detta är första gången), så det kan hända att jag missat något uppenbart. Jag väljer dock att reflektera kring problemen jag misstänker kan finnas i de fall jag inte är hundraprocentigt säker på att jag förstått koden rätt.
