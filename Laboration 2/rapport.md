# Analys och förbättringsförslag ur säkerhets- och prestandasynpunkt på applikationen Labby Message

Rapport skriven av Lisa Westlund (lw222gu).

Laboration 2 i kursen Webbteknik II, vid Linnéuniversitetet.

Rapporten är indelad i tre delar: Säkerhetsproblem, Prestandaproblem och Personliga reflektioner

---
## Säkerhetsproblem

### Problem 1: SQL Injections
Applikationen möjliggör för SQL Injections. T.ex. är det möjligt att logga in genom att ange ett existerande användarnamn, och som lösenord ange "' OR '1'='1". Du behöver alltså inte vara en auktoriserad användare, utan du kan komma åt innehållet genom att autentisera dig som en annan användare, och då utföra aktioner i dennes namn.

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

Förutom ovanstående, så lagras lösenorden i klartext.

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


### Problem 3: Cross-Site Scripting - XSS
Det är möjligt att skjuta in JavaScript i meddelanderutan. Ett exempel är att ange texten `>'>"><img src=x onerror=alert(0)>` som meddelande. I det fallet dyker en alertruta med texten `0` upp i samband med postningen av meddelandet. Byter jag ut 0 mot `document.cookie`, så att meddelandet blir `>'>"><img src=x onerror=alert(document.cookie)>` visar istället alertrutan sessions-id:t. Det betyder att det är möjligt att komma åt sessions-id:t, t.ex. genom att istället för att visa en alertruta redirecta användaren till en annan webbplats och skicka med sessions-id:t. På den webbsajten är det sedan möjligt att logga detta, och då till slut använda den inloggade användarens sessiona-id för att utföra egna requests.

#### Om Cross-Site Scripting
XSS är ett säkershetsfel som gör att det är möjligt att skjuta in skadlig JavaScript till applikationen. När JavaScripten sedan exekveras i användares webbläsare är det möjligt att stjäla data, utföra aktioner i användarens namn, etc. [Referens: https://www.google.com/about/appsecurity/learning/xss/#WhatIsIt, och OWASP 2013]

Säkerhetshål för XSS-attacker uppstår när applikationen lägger till användargenererad data till webbplatsen utan att först validera input-datan och ersätta eventuellt skadligt innehåll med något annat, t.ex. en tomsträng [Referens OWASP 2013].

#### Förhindra XXS-attacker
Ett sätt att minska risken för XXS-attacker är att escapea all input [Referens: https://www.google.com/about/appsecurity/learning/xss/#PreventingXSS och OWASP 2013]. Att escapea innebär att varje del av en inmatad sträng tolkas som en sträng i sig, inte som kod. Att skriva egen kod för att manuellt escapea inputs är väldigt svårt. Därför rekommenderar [Referens: https://www.google.com/about/appsecurity/learning/xss/#PreventingXSS] att man istället använder t.ex. ett ramverk som tillhandahåller innehållsmedveten auto-escape för detta.

Ytterligare ett sätt att motverka XSS-attacker är att validera inputs mot en whitelist - alltså, tillåt bara inputs bestående av vissa tecken. I vissa fall kan det dock vara svårt, då applikationer kan kräva att det är möjligt att använda specialtecken av olika slag. Om så är fallet är det extra viktigt att validera längd, tecken, etc. innan input-datan accepteras [OWASP 2013].

Det kan vara svårt att testa för XSS. Även om [OWASP 2013] anger att det är relativt enkelt att hitta de flesta XSS-säkerhetshålen, menar [Referens: https://www.google.com/about/appsecurity/learning/xss/#TestingXSS] att det inte alls finns något helgjutet sätt att hitta möjliga attackvägar. De menar att det bästa är att utföra tester i form av en kombination av
* manuella tester (testa att skjuta in JavaScript på alla input-fält som finns i applikationen),
* unit-tester (för att kontrollera korrekt escaping av viktiga delar),
* och att använda automatiska testverktyg för XXS.

#### Förändringar i applikationen
Applikationen måste se till att escapea all input-data, och validera den mot en whitelist för att se till att det inte är möjligt att skjuta in skadlig JavaScript någonstans i applikationen.


### Problem 4: Osäkra direkta objektreferenser
Applikationen har problem med osäkra direkt objektreferenser, genom att den visar meddelandens id-nummer i gömda fält i koden. Dessa id-nummer är dessutom exakt samma id som används i databasen. I min installation av applikationen fungerar det inte att radera meddelanden alls, men genom att studera koden tror jag att det är möjligt för en användare att genom att manipulera värdet för det dolda inputfältet radera ett annat meddelande än det meddelande som egentligen är knutet till raderalänken. Detta skulle då kunna leda till att en användare kan radera andra användares meddelanden, eftersom det inte, vad jag kan se, sker någon kontroll på att det verkligen är rätt användare som försöker radera ett meddelande.

#### Om osäkra direkta objektreferenser
En direkt objektreferens är en exponerad referens till ett internt objekt. Det kan t.ex. röra sig om databasnycklar [OWASP, T10]. Detta, i kombination med att åtkomstkontroller saknas på funktionsnivå (se problem 5), gör det möjligt för en användare att komma åt funktioner som den eventuellt inte är autkoriserad för.

#### Förhindra problem med osäkra direkta objektreferenser
Ett sätt att förhindra problem kring objektreferenser är att använda indirekta referenser på användar- eller sessionsnivå. Applikationen får sedan till uppgift att mappa de indirekta referenserna mot de verkliga referenserna i databasen. Dessutom måste åtkomstkontroll alltid ske för att säkerställa att användaren verkligen är auktoriserad för objektet som efterfrågas [OWASP A4].

### Problem 5: Saknad åtkomstkontroll på funktionsnivå
I applikationen är det möjligt att komma åt meddelanden i json-format utan att vara inloggad på sidan. Datan finns fritt tillgänglig om användaren besöker sidan `/message/data`. Det är dessutom fritt fram att ladda ner hela databasen genom att besöka sidan `/message/appModules/siteViews/static/message.db`.

#### Om saknad åtkomstkontroll på funktionsnivå
Saknad åtkomstkontroll på funktionsnivå innebär att anonyma användare kan komma åt privat funktionalitet, eller att vanliga användare kan komma åt funktioner som enbart ska vara tillgängliga för användare med högre behörigheter. Att detta möjliggörs beror på att det inte sker några åtkomstkontroller för dessa funktioner på servern. [OWASP 2013]

#### Förhindra problem kring åtkomstkontroll
För att förhindra problem kring att användare kan komma åt funktionalitet de inte är autoriserade för bör systemet bygga på att åtkomst i grund alltid nekas, men att för varje funktion ge explicit åtkomst för de roller som ska kunna använda funktionen [OWASP A7].


### Problem 6: Cross-Site Request Forgery - CSRF
Applikationen skyddas inte mot CSRF-attacker. Som tidigare nämnts finns det möjligheter att på olika vis skjuta in skadlig kod i applikationen. Detta tillsammans med att ingen unik token skickas med vid requests gör applikationen mycket känslig för denna typ av attacker.

#### Om Cross-Site Request Forgery - CSRF
CSRF är en typ av attack där t.ex. en opålitlig webbsida orsakar en användares webbläsare att utföra requests på den webbapplikation användaren är autentiserad, utan att användaren vet om det. Vad detta kan leda till beror mycket på vad den autentiserade användaren är auktoriserad att göra i applikationen. För den enskilda användaren som drabbas kan t.ex. en attack som lyckas ändra lösenordet leda till att man förlorar åtkomsten till sitt konto, och för en administratör kan en attack vara förödande för hela applikationen. [Referens: https://www.owasp.org/index.php/Cross-Site_Request_Forgery_(CSRF)_Prevention_Cheat_Sheet  ]_

#### Förhindra CSRF
Den generella rekommendationen för att förhindra CSRF-attacker är att använda sig av det som kallas Synchronizer Token Pattern. Detta bygger på att inkludera en unik och oförutsägbar token i ett dolt fält i ett formulär. [OWASP A8, och https://www.owasp.org/index.php/Cross-Site_Request_Forgery_(CSRF)_Prevention_Cheat_Sheet ]_ Denna skickas sedan med i HTTP-requesten och det är därefter upp till servern att verifiera denna token, och på så vis kunna anta med betydligt större säkerhet att användaren verkligen menade att utföra denna request. [https://www.owasp.org/index.php/Cross-Site_Request_Forgery_(CSRF)_Prevention_Cheat_Sheet ]_


---

## Prestandaproblem

### Problem 8: Många Http requests ökar laddningstiden
När applikationen laddas görs en rad olika Http requests. Det görs åtta requests direkt i head-elementet, vilka läser in fonter, css, och JavaScript.

#### Problem med många Http requests, och hur det förbättras
Enbart 10-20 procent av svarstiden för att ladda en applikation utgörs av att läsa in det efterfrågade HTML-dokumentet. Resten av tiden ägnas åt att läsa in övriga komponenter, så som css, JavaScript, etc.  Genom att minska antalet anrop kan också svarstiden minskas.[High Performance, s 10].

Trots att extern JavaScript och css ökar antalet requests är de bättre ut prestandasynpunkt än vad inbäddad eller rentav inline JavaScript och css är. Men, väljer man att följa de rekommendationer som finns om att dela in olika moduler i olika filer så ökar det antalat anrop och försämrar svarstiden [High Performance, s 15]. Idealt bör inte mer än en JavaScript-fil och en css-fil anropas [High Performance, s 16].


### Problem 9: Komponenter cacheas inte
När applikationen laddas in görs ingen cachening. Expiresheadern är satt till -1.

#### Om cachening och hur svarstiden kan förkortas
Om ingen cachening görs av komponenter som t.ex. JavaScript-filer, css och bilder måste dessa hämtas på nytt via nya Http requests för varje sida som besöks på applikationen, vilket försämrar svarstiden. För att cachea komponenter, och på så vis minska svarstiden, ska Expiresheadern sättas till en tidpunkt som talar om hur länge komponenterna kan anses vara up-to-date [High Performance, s 22].
Idealt skulle alla komponenter på en webbapplikaiton cacheas, men vanligtvis cacheas inte HTML-dokument då de ofta består av dynamiskt innehåll som kan ändras från varje gång en användare besöker en sida [High Performance, s 26].

Om komponenter uppdateras under tiden Expiresheadern fortfarande är giltig kommer inte användare som tidigare besökt sidan att ta del av ändringarna, eftersom filerna redan finns i deras cache. Ett sätt att komma runt detta är att döpa om filerna vid nya versioner, och därmed också förändra sökvägarna till dem - då kommer applikationen genomföra nya Http requests nästa gång användaren besöker sidan [High Performance, s 27].




### Onödiga resurser laddas in
Bilden...







### Onödig kod
Varför inkludera ett ramverk som Bootstrap när det ändå inte används?

### Css i html-filen
Lägg css i externa filer istället. Då kan de cacheas över sidor.

---

## Personliga reflektioner
I flera fall i rapporten har jag angett att jag spekulerar i frågan. Jag är ovan att läsa kod i node-projekt (detta är första gången), så det kan hända att jag missat något uppenbart. Jag väljer dock att reflektera kring problemen jag misstänker kan finnas i de fall jag inte är hundraprocentigt säker på att jag förstått koden rätt.
