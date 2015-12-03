# Analys och förbättringsförslag ur säkerhets- och prestandasynpunkt på applikationen Labby Message

Rapport skriven av Lisa Westlund (lw222gu).

Laboration 2 i kursen Webbteknik II vid Linnéuniversitetet.

---
## Säkerhetsproblem

### Problem 1: SQL Injections
Applikationen möjliggör för SQL Injections. T.ex. är det möjligt att logga in genom att ange ett existerande användarnamn, och som lösenord ange `"' OR '1'='1"`. Du behöver alltså inte vara en auktoriserad användare, utan du kan komma åt innehållet genom att autentisera dig som en annan användare, och då utföra aktioner i dennes namn.

Utan att veta vad applikationen har för rättigheter i databasen spekulerar jag i att det är möjligt att i kombination med ovanstående skicka in skadlig kod i databasen, t.ex. att radera den. Detta är fullt möjligt om användarens rättigheter inte är begränsade, så som applikationen är byggd i nuläget.

#### Om SQL Injections
SQL Injections i stort innebär skjuta in skadlig kod via input-fält i en applikation [1]. Detta kan göras för att få ta del av databasen, autentisera sig som en annan användare, radera databasen, manipulera innehållet i databasen, etc. [2]. Injections anses av OWASP vara den vanligaste säkerhetsbristen i webbapplikationer [3, s. 4]. OWASP pekar också på att ett resultat av injections som leder till förlust av data, manipulation av data, nekad åtkomst till applikationen, etc. kan påverka applikationens och applikationsägarens rykte. Detta är givetvis delvis beroende av datans värde i sig [3, s. 7].

#### Förhindra SQL Injections
SQL Injections förhindras enklast genom att använda parameteriserade frågor med validerad data vid kontakt med databasen, i stället för att låta användarinput användas direkt i SQL-satserna [2]. Det är också möjligt att använda sig av en teknik som benämns som "escaping". Escaping innebär att tecken som kan ha en särskild betydelse i SQL-frågor översätts till något annat [2]. Genom att ta reda på vilka tecken som kan anses ha en särskild betydelse för just den databas som används är det möjligt att svartlista dessa tecken, och på så vis skydda sig mot SQL Injections. Att svartlista något innebär dock en betydande risk för att missa något viktigt, och på så vis ändå öppna för attacker [2].

Applikationer bör också ha begränsade rättigheter till databasen i fråga, och då enbart ha rättighet att utföra det som krävs för applikationen. På så vis kan man undvika att t.ex. göra det möjligt att vid en attack radera hela databasen [2].

Det är av största vikt att alltid validera all användargenererad data. Microsoft lista några punkter att kontrollera enligt nedan [1]:

* Validera alltid datatyp och storlek på input-data, mot begränsningar som är rimliga för din applikation.
* Validera innehållet i strängar, och neka användaren att ange tecken du inte anser vara ok. Detta kan t.ex. vara tecken som `;`, `'`, `--`, `/*...*/`, etc.
* Validera användarinputs även i lagrade procedurer.
* Validering ska ske i alla lager av applikationen, såväl på klienten som på servern och i datalagret.

##### Förändringar i applikationen
Applikationen bör förändras genom att använda sig av parameteriserade frågor till databasen via lagrade procedurer, i stället för att som i login-fallet konkatenera ihop strängar av SQL-frågor och användarinput. Applikationen bör också validera den data som användare kan posta.

### Problem 2: Hijacking p.g.a. dålig autentiserings- och sessionshantering
När en användare valt att logga ut är det möjligt att t.ex. genom webbläsarens bakåtknapp, eller genom att känna till url:en som används i inloggat läge, hamna i inloggat läge igen. I de fall en användare t.ex. använder sig av en publik dator, tror att den loggat ut korrekt, och sedan lämnar datorn igång utan att stänga webbläsaren korrekt (och på så sätt döda sessionen), är det möjligt för nästa person att enbart klicka på tillbakaknappen, eller ange url:en `/message` för att åter vara inloggad i den första användarens namn. Så länge sessionen är i liv är detta möjligt. Detta beror på att sessions-id:n inte förstörs vid utlogg. Sessions-id:t är dessutom inställt på att överleva ett år om det inte förstörs.

All information skickas okrypterad via http istället för via https.

Förutom ovanstående, så lagras lösenorden i klartext.

Lösenordshanteringen och hanteringen av sessioner är starka orsaker till att applikationen är känslig för hijacking. OWASP benämner problemet som Broken Authentication and Session Management, vilket anses vara den näst vanligaste säkerhetsbristen i webbapplikationer [3, s. 8].

#### Om hijacking
OWASP beskriver hijacking som att en person kan komma över en användares konto, och utföra aktioner i dennes namn. En stark anledning till att detta är så vanligt förekommande är att många utvecklare väljer att skriva egna autentiseringsfunktioner, och då missar viktiga delar kring lösenordshantering, utloggning, sessionshantering m.m. [3, s. 8]. Om en person kommer över en användares konto kan den utföra allt som användaren kan utföra, vilket gör att användare med hög auktoriseringsgrad i applikationen ofta är mer utsatta för denna typ av attack [3, s. 8].

#### Förhindra hijacking
För att undvika att användares konton hijackas finns några viktiga punkter att följa enligt OWASP [3, s. 8]. Se nedan.
* Se till att hasha lösenord när de lagras persistent. Det är bättre att hasha lösenord än att kryptera dem, då krypterade lösenord kan dekrypteras, vilket hashade lösenord inte kan [4].
* Användaruppgifter ska inte vara lätta att gissa.
* Funktioner som att ändra lösenord, beställa nytt lösenord etc. måste även de vara implementerade med fokus på säkerhet.
* Sessions-id:n ska inte skickas med i url:er.
* Sessions-id:n ska time-outas, och förstöras efter utlogg.
* Sessions-id:n ska ändras efter en lyckad inloggning.
* Lösenord, sessions-id:n m.m. ska enbart skickas över krypterade anslutningar, t.ex. https.

I många fall kommer man långt på att använda redan existerande funktioner för att logga in, som t.ex. OAuth2, istället för att skriva egna autentiseringsfunktioner. Dessa är väl testade och har högre säkerhet än vad många utvecklare klarar att implementera på egen hand.

##### Förändringar i applikationen
* Applikationen bör hasha lösenord.
* Applikationen bör rotera sessions-id:n vid lyckad login och förstöra dem vid logout.
* Applikationen bör skicka känslig information via krypterade anslutningar, och då använda https istället för http.

### Problem 3: Cross-Site Scripting - XSS
Det är möjligt att skjuta in JavaScript i meddelanderutan. Ett exempel är att ange texten `>'>"><img src=x onerror=alert(0)>` som meddelande. I det fallet dyker en alertruta med texten `0` upp i samband med postningen av meddelandet. Byter jag ut `0` mot `document.cookie`, så att meddelandet blir `>'>"><img src=x onerror=alert(document.cookie)>` visar istället alertrutan sessions-id:t. Det betyder att det är möjligt att komma åt sessions-id:t, t.ex. genom att i stället för att visa en alertruta redirecta användaren till en annan webbplats och skicka med sessions-id:t. På den webbsajten är det sedan möjligt att logga detta, och då till slut använda den inloggade användarens sessions-id för att utföra egna requests.

#### Om Cross-Site Scripting - XSS
XSS är ett säkerhetsfel som gör att det är möjligt att skjuta in skadlig JavaScript till applikationen. När JavaScripten sedan exekveras i användares webbläsare är det möjligt att stjäla data, utföra aktioner i användarens namn, etc. [5] [3, s. 9].

Säkerhetshål för XSS-attacker uppstår när applikationen lägger till användargenererad data till webbplatsen utan att först validera input-datan och ersätta eventuellt skadligt innehåll med något annat, t.ex. en tomsträng [3, s. 9].

#### Förhindra XXS-attacker
Ett sätt att minska risken för XXS-attacker är att escapea all input [5] [3, s.9]. Att escapea innebär att varje del av en inmatad sträng tolkas som en sträng i sig, inte som kod. Att skriva egen kod för att manuellt escapea inputs är väldigt svårt. Därför rekommenderar Google att man i stället använder ett ramverk för detta [5].

Ytterligare ett sätt att motverka XSS-attacker är att validera inputs mot en whitelist - alltså, tillåt bara inputs bestående av vissa tecken. I vissa fall kan det dock vara svårt, då applikationer kan kräva att det är möjligt att använda specialtecken av olika slag. Om specialtecken är nödvändiga är det extra viktigt att validera längd, tecken, etc. innan input-datan accepteras och används vidare [3, s. 9].

Det kan vara svårt att testa för XSS. Även om OWASP anger att det är relativt enkelt att hitta de flesta XSS-säkerhetshålen [3, s. 9], menar Google att det inte alls finns något helgjutet sätt att hitta möjliga attackvägar [5]. De anger vidare att det bästa är att utföra tester i form av en kombination av
* manuella tester (testa att skjuta in JavaScript via alla input-fält som finns i applikationen),
* unit-tester (för att kontrollera korrekt escaping av kritiska delar),
* och att använda automatiska testverktyg för XXS.

##### Förändringar i applikationen
Applikationen måste se till att escapea all input-data, och validera den mot en whitelist för att se till att det inte är möjligt att skjuta in skadlig JavaScript någonstans i applikationen.

### Problem 4: Osäkra direkta objektreferenser
Applikationen har problem med osäkra direkta objektreferenser, genom att den visar meddelandens id-nummer i dolda fält i koden. Dessa id-nummer är dessutom exakt samma id-nummer som används i databasen. I min installation av applikationen fungerar det inte att radera meddelanden alls, men genom att studera koden tror jag att det är möjligt för en användare att genom att manipulera värdet för det dolda inputfältet radera ett annat meddelande än det meddelande som egentligen är knutet till raderalänken. Detta skulle då kunna leda till att en användare kan radera andra användares meddelanden, eftersom det inte, vad jag kan se, sker någon kontroll på att det verkligen är rätt användare som försöker radera ett meddelande.

#### Om osäkra direkta objektreferenser
En direkt objektreferens är en exponerad referens till ett internt objekt. Det kan t.ex. röra sig om databasnycklar [3, s. 6]. Detta, i kombination med att åtkomstkontroller saknas på funktionsnivå (se problem 5), gör det möjligt för en användare att komma åt funktioner som den eventuellt inte är auktoriserad för.

#### Förhindra problem med osäkra direkta objektreferenser
Ett sätt att förhindra problem kring objektreferenser är att använda indirekta referenser på användar- eller sessionsnivå [3, s. 10]. Applikationen får sedan till uppgift att mappa de indirekta referenserna mot de verkliga referenserna i databasen. Dessutom måste åtkomstkontroll alltid ske för att säkerställa att användaren verkligen är auktoriserad för objektet som efterfrågas [3, s. 10].

### Problem 5: Saknad åtkomstkontroll på funktionsnivå
I applikationen är det möjligt att komma åt meddelanden i json-format utan att vara inloggad på sidan. Datan finns fritt tillgänglig om användaren besöker sidan `/message/data`. Det är dessutom fritt fram att ladda ner hela databasen genom att besöka sidan `/message/appModules/siteViews/static/message.db`.

#### Om saknad åtkomstkontroll på funktionsnivå
Saknad åtkomstkontroll på funktionsnivå innebär att anonyma användare kan komma åt privat funktionalitet, eller att vanliga användare kan komma åt funktioner som de inte ska ha behörighet till [3, s. 13]. Att detta möjliggörs beror på att det inte sker några åtkomstkontroller för dessa funktioner på servern [3, s. 13].

#### Förhindra problem kring åtkomstkontroll
För att förhindra problem kring att användare kan komma åt funktionalitet de inte är auktoriserade för bör åtkomstkontrollen bygga på att åtkomst i grund alltid nekas, men att för varje funktion ge explicit åtkomst för de roller som ska kunna använda funktionen [3, s. 13].

### Problem 6: Cross-Site Request Forgery - CSRF
Applikationen skyddas inte mot CSRF-attacker. Som tidigare nämnts finns det möjligheter att på olika vis skjuta in skadlig kod i applikationen. Detta tillsammans med att ingen unik token skickas med vid requests gör applikationen mycket känslig för denna typ av attacker.

#### Om Cross-Site Request Forgery - CSRF
CSRF är en typ av attack där t.ex. en opålitlig webbsida orsakar en användares webbläsare att utföra requests på den webbapplikation användaren är autentiserad, utan att användaren vet om det [6]. Vad detta kan leda till beror mycket på vad den autentiserade användaren är auktoriserad att göra i applikationen. För den enskilda användaren som drabbas kan t.ex. en attack som lyckas ändra lösenordet leda till att man förlorar åtkomsten till sitt konto, och för en administratör kan en attack vara förödande för hela applikationen [6].

#### Förhindra CSRF
Den generella rekommendationen för att förhindra CSRF-attacker är att använda sig av det som kallas Synchronizer Token Pattern. Detta bygger på att inkludera en unik och oförutsägbar token i ett dolt fält i ett formulär. [3, s. 14] [6]. Denna skickas sedan med i HTTP-requesten och det är därefter upp till servern att verifiera denna token, och på så vis kunna anta att användaren verkligen menade att utföra denna request [6].

---

## Prestandaproblem

### Problem 8: Många Http-requests ökar svarstiden
När applikationen laddas görs en rad olika Http-requests. Det görs åtta requests direkt i head-elementet, vilka läser in fonter, css, och JavaScript.

#### Problem med många Http-requests, och hur det kan förbättras
Enbart 10-20 procent av svarstiden för att ladda en applikation utgörs av att läsa in det efterfrågade HTML-dokumentet. Resten av tiden ägnas åt att läsa in övriga resurser, så som css, JavaScript, etc. Genom att minska antalet anrop kan också svarstiden minskas [7, s. 10].

Trots att extern JavaScript och css ökar antalet requests är de bättre ur prestandasynpunkt än vad inbäddad eller rentav inline JavaScript och css är. Men, väljer man att följa de rekommendationer som finns om att dela in olika moduler i olika filer så ökar antalet anrop och svarstiden försämras [7, s. 15]. Idealt bör inte mer än en JavaScript-fil och en css-fil anropas [7, s. 16]. Detta kan dock ifrågasattas i och med HTTP/2 där multiplexing introduceras. Multiplexing innebär att klienten ska kunna använda samma TCP-anslutning för att hantera parallella förfrågningar och svar [8].

### Problem 9: Komponenter cachas inte
När applikationen laddas in görs ingen cachening. Expires-headern är satt till -1, och Cache-Control är satt till private, no-cache, no-store, must-revalidate.

#### Om cachening och hur svarstiden kan förkortas
Om ingen cachening görs av resurser som t.ex. JavaScript-filer, css och bilder måste dessa hämtas på nytt via nya Http-requests för varje sida som besöks på applikationen, vilket försämrar svarstiden. För att cacha resurser, och på så vis minska svarstiden, ska Expires-headern sättas till en tidpunkt som talar om hur länge resurserna kan anses vara up-to-date [7, s. 22], och Cache-Control-headern ska ha ett värde för max-age [7, s. 23]. Expires-headern används i webbläsare som inte stödjer HTTP/1.1, och skrivs över om webbläsaren stödjer nyare versioner av HTTP i samband med att Cache-Control-headern har ett värde för max-age [7, s. 23].

Idealt skulle alla komponenter på en webbapplikation cachas, men vanligtvis cachas inte HTML-dokument då de ofta består av dynamiskt innehåll som kan ändras från varje gång en användare besöker en sida [7, s. 26].

Om komponenter uppdateras under tiden Expires-headern eller Cache-Control-headern fortfarande är giltig kommer inte användare som tidigare besökt sidan få ta del av ändringarna, eftersom filerna redan finns i deras cache. Ett sätt att komma runt detta är att döpa om filerna vid nya versioner, och därmed också förändra sökvägarna till dem - då kommer applikationen genomföra nya Http-requests nästa gång användaren besöker sidan [7, s. 27].

### Problem 10: Ingen komprimering sker
Inget i applikationen komprimeras i samband med att den skickas till webbläsaren. Detta kan ses genom att Http-requesten saknar en header i stil med Content-Encoding: gzip. Flera av textfilerna är så pass stora att en komprimering är värdefull ur prestandasynpunkt.

#### Om att komprimera och hur det påverkar prestandan
Den vanligaste metoden för att komprimera är gzip. Med gzip är det möjligt att komprimera textfiler. Bildfiler, pdf:er, etc.  är redan komprimerade format, och att försöka komprimera dessa slösar bara kraft och kan ibland resultera i större filer [7, s. 30]. Att komprimera textfiler kostar det också, så även om man tjänar på att filstorlekar minskas, måste det tas i beaktning om det är värt att komprimera filerna. Filer som understiger 1-2 kb finns det sällan någon anledning att komprimera [7, s. 30].

I den här applikationen finns det dock flertalet textfiler som applikationen skulle tjäna på att de komprimerades. Att gzip:a filer reducerar nämligen oftast storleken med omkring 70 procent [7, s. 31].

### Problem 11: Dålig placering, hantering och inläsning av statiska resurser
Applikationen läser in resurser på alla möjliga olika sätt. Ibland finns css och JavaScript inbäddat i html-dokumenten, och ibland läses de in från externa filer via Http-requests. JavaScript läses flertalet gånger in i head-elementet, utan attribut som talar om att filerna ska laddas först när sidan är laddad, vilket gör att sidan tar onödigt lång tid att ladda.

#### Om att placera, hantera och läsa in statiska resurser - hur det bör göras
Stilmallar ska placeras i toppen av dokument, närmare bestämt i head-elementet. Genom att göra så kan sidan laddas progressivt, och på så vis ge feedback till användaren och undvika tomma vita skärmar [7, s. 41].

När det gäller scripter är det tvärtom. Dessa ska laddas så sent som möjligt i ett dokument. Scripter gör nämligen så att de förhindrar inläsning av allt innehåll nedanför scriptet, tills det att scriptet har laddat klart [7, s. 45]. Det är också fullt möjligt att läsa in scripter redan i head-elementet, men då är det viktigt att ange attribut som talar om att de ska laddas först när sidan laddat klart.

Css och JavaScript bör dessutom placeras i externa filer, då det innebär att de kan cachas och därmed inte behöva laddas in för varje sida användaren besöker [7, s. 57].

### Problem 12: Ominifierade filer
JavaScript-filerna är inte minifierade i applikationen, vilket gör att de är onödigt stora. Onödigt stora filer tar onödigt lång tid att ladda.

#### Om minifiering
Minifiering handlar om att ta bort icke nödvändiga tecken från en fil. Dessa tecken är t.ex. kommentarer och whitespaces så som mellanslag, ny rad och tabbar. Eftersom ingen kompilering görs i JavaScript tas inte dessa bort automatiskt, utan det är upp till utvecklaren att göra detta [7, s. 69].

Att minifiera css tjänar en applikation sällan lika mycket på som att minifiera JavaScript. Detta beror på att css oftast har färre kommentarer och whitespaces än vad JavaScript-kod har [7, s. 75]. För att tjäna i filstorlek på stilmallar är det därför viktigare att optimera css:en, genom att slå ihop identiska klasser, ta bort dubbletter och oanvända klasser. Det finns också optimeringsmöjligheter när det kommer till css som att korta ner onödigt långa strängar som t.ex. `0px`, som lika gärna kan skrivas som `0` [7, s. 75].

### Problem 13: Övrigt
Överlag finns ytterligare en del att önska av applikationen, vilket listas i korthet under rubrikerna nedan.

#### Hantering av bilder
Bilden `b.jpg` visas såvitt jag kan se enbart för användaren om det finns väldigt många skrivna meddelanden, då den syns som en bakgrund. Detta hör ihop med att bodyns höjd är satt till 4000px (vilket i sig saknar mening). När den blir högre än 4000px visas bilden som bakgrund istället för den blå färgen. Bilden repeteras dock hela tiden i bakgrunden, men syns inte för användaren. Detta är slöseri med resurser och den bör plockas bort. Om den ska synas i vissa fall har den ett sådant mönster att det skulle vara möjligt att använda en mindre fil och repetera den istället.

En favicon som är 691 x 257 px är onödigt stor, och dessutom inte kvadratisk. I en webbläsare räcker det att en favicon är 32 x 32 px. För att visas så bra som möjligt på högupplösta läsplattor är den största storleken som behövs 192 x 192 px [9].

`delete.png` och `clock.png` skulle kunna kombineras till en bild, för att sedan använda tekniker som Image maps, eller Css sprites. Detta skulle minska Http-anropen från två till ett, och på så vis påverka svarstiden för sidan [7, s. 10]. Dock är det inte troligt att denna justering i sig skulle innebära något större för just denna applikation, men skulle applikationen växa i framtiden och innefatta fler ikoner är detta bra tekniker att ha tillhands.

#### Användandet av Bootstrap
Ramverket Bootstrap läses in som en resurs i applikationen, men så vitt jag kan se används det inte särskilt mycket. Att läsa in så mycket kod, som faktiskt genereras med ramverket, till en så liten applikation kan ifrågasättas. Om vissa delar av Bootstrap ska användas bör filerna också optimeras som så att oanvänd kod raderas [7, s. 75].

#### Paginering
Det är alltid värt att fundera på om inte paginering vore ett bra alternativ. Just nu visas alla poster i en lång lista, istället för att visas sidvis, och på så sätt göra det möjligt att enbart läsa ut de poster från databasen som faktiskt ska visas - i stället för att hämta alla. I det här fallet, när det gäller en todo-applikation går jag inte in på det djupare, då jag tror att det går att förutsätta att det aldrig rör sig om så många meddelanden att det blir ett större prestandaproblem.

---

## Personliga reflektioner
I något fall i rapporten har jag angett att jag spekulerar kring problemet. Jag är ovan att läsa kod i node-projekt (detta är första gången), så det kan hända att jag missat något uppenbart. Jag väljer dock att reflektera kring problemen jag misstänker kan finnas i de fall jag inte är hundraprocentigt säker på att jag förstått koden rätt.

När det gäller prestandan blir många av förändringarna som föreslås i denna rapport utan märkbar effekt. Jag har valt att ändå redogöra för dessa för att visa på vad som kan bli prestandabovar om applikationen skulle växa.

Att gå igenom en applikation på det här viset är väldigt givande. Jag är hundra procent säker på att jag missat flera viktiga säkerhets- och prestandaproblem, men jag vet också att nästa gång jag skriver en applikation kommer jag ha betydligt fler saker i åtanke ur dessa perspektiv. En del av problemen ovan är sådant jag känt till sedan tidigare, och ibland valt att strunta i eftersom "det är ju ändå bara ett så litet projekt", men när jag radat upp problemen så här så inser jag hur småfel kan ge stora konsekvenser, både gällande säkerhet och prestanda.

Som utvecklare bär man ett stort ansvar gentemot användaren av en applikation. Allt från att se till att användaruppgifter inte läcker ut, till att det inte är möjligt att stjäla identiteter och utföra saker i användares namn. Då fungerar det inte att se mellan fingrarna när det gäller säkerhetsproblem. Man bär också detta ansvar gentemot webbplatsägaren (kunden), som självklart ska kunna känna sig lugn i att t.ex. känslig data behålls privat. Mot kunden har man som utvecklare också ett ansvar i att se till att optimera webbapplikationen så mycket som möjligt ur prestandasynpunkt - kunden vill ju att användare av tjänsten ska vara nöjda, inte uppleva applikationen som seg och vilja komma tillbaka. Uppgiften har verkligen fått mig att tänka till på dessa punkter.

---

## Referenser

[1] "SQL Injection," _Microsoft_, november 2015 [Online] Tillgänglig: https://technet.microsoft.com/en-us/library/ms161953(v=SQL.105).aspx. [Hämtad: 2 december, 2015].

[2] "SQL Injection," _Wikipedia_, december 2015 [Online] Tillgänglig: https://en.wikipedia.org/wiki/SQL_injection. [Hämtad: 2 december, 2015].

[3] "OWASP Top 10 - 2013: The Ten Most Critical Web Application Security Risks," _OWASP The Open Web Application Security Project_, juni 2013 [Pdf] Tillgänglig: http://owasptop10.googlecode.com/files/OWASP%20Top%2010%20-%202013.pdf. [Hämtad: 23 november, 2015].

[4] W. Jackson, "Why salted hash is as good for passwords as for breakfast," _GCN_, december 2013 [Online] Tillgänglig: https://gcn.com/articles/2013/12/02/hashing-vs-encryption.aspx. [Hämtad: 2 december, 2015].

[5] "Cross-site scripting," _Google_ [Online] Tillgänglig:  https://www.google.com/about/appsecurity/learning/xss/. [Hämtad: 2 december, 2015].

[6] "Cross-Site Request Forgery (CSRF) Prevention Cheat Sheet," _OWASP The Open Web Application Security Project_, november 2015 [Online] Tillgänglig: https://www.owasp.org/index.php/Cross-Site_Request_Forgery_(CSRF)_Prevention_Cheat_Sheet. [Hämtad: 3 december, 2015].

[7] S. Souders, _High Performance Web Sites_. Sebastopol: O’Reilly Media, Inc., 2007.

[8] "HTTP/2," _Wikipedia_, december 2015 [Online] Tillgänglig: https://en.wikipedia.org/wiki/HTTP/2. [Hämtad: 3 december, 2015].

[9] "Favicon," _Wikipedia_, november 2015 [Online] Tillgänglig: https://en.wikipedia.org/wiki/Favicon. [Hämtad: 3 december, 2015].
