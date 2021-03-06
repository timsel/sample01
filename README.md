Feladat
=======
A jogszabályszövegek elemzése során szükséges felderíteni milyen tartalmak fordulnak elő a jogszabályokban. A cél elérése több lépcsős feladatsorban történik, az egyes feladatsorok egymás adatait fogják használni. Az ön feladata egy rész-elemzés megoldása, melyet úgy kell elkészítenie, hogy annak eredményét más fejlesztők által készített programok könnyen használni tudják.

Fontos, hogy a feladatban meghatározott eljárásról azt kell feltételeznie, hogy éles üzemben az összes jogszabály szöveganyagára el kell végeznie a feldolgozást. Ez 300 000 dokumentum feldolgozását jelenti. Ezért a feladat pontos végrehajtás mellett elsődleges fontosságú a program futási idejének minimalizálása.

Alapfeladat
-----------
Szótárat készíteni a jogszabályokban szereplő szavakról. 

Feladat-1
---------
1.) A program töltse le az alábbi jogszabályok HTML fájljait a Nemzeti Jogszabálytár oldaláról:

Magyarország Alaptörvénye (http://njt.hu/cgi_bin/njt_doc.cgi?docid=140968)

Ptk. (http://njt.hu/cgi_bin/njt_doc.cgi?docid=159096)
1004/2016. (I. 18.)
 
Korm. határozat (http://njt.hu/cgi_bin/njt_doc.cgi?docid=193687)

Legyen könnyen paraméterezhető, hogy milyen jogszabályokat töltsön le. 
(Figyelem, az njt.hu oldal meghajtásvédett, így kísérletezésre nem alkalmazza!) 

2.) Írjon programot, ami a HTML fájlokból kiszedi a szöveges tartalmat és készít egy szótárat, melyben minden szó egyszer szerepel. Ezenkívül el kell tárolni az adott jogszabályban hányszor szerepel a szó. 

3.) Válassza meg a szótár formátumát annak megfelelően, hogy a későbbiekben e szótárat majd más programoknak rendezni kell, a szótárhoz további elemeket (szavakat) kell adni, vagy azokat törölni kell.

4.) Az elkészült szótárat fel kell tölteni egy SQL alapú adatbázisba. Amennyiben ez a dokumentum már szerepelt az adatbázisban, akkor frissíteni kell a hozzá tartozó adatokat. Meg kell valósítani a CRUD műveleteket.

5.) Válasszon fejlesztőeszközt, melynek segítségével a feladatot hatékonyan meg tudja oldani. A végterméknek Windows környezetben, parancssorban futtatható programnak kell lennie.

6.) Figyeljen az elkészült kód minőségére, újrahasznosíthatóságára.


Feladat-2
---------

WEB-Service készítése az előző feladat végeredményének felhasználásához
Az előző feladat kimeneteként elkészült  SQL (MySql vagy PostgreSQL) adatbázishoz írjon egy REST API hívással elérhető WEB-Service-t az alábbi funkciók megvalósításához:

1. Az összes előforduló szó lekérdezése.
2. Egy adott szóhoz tartozó jogszabályok lekérdezése.
3. Egy adott jogszabályhoz tartozó szavak lekérdezése. A válaszban szerepeljen az előfordulási szám is.
4. A leggyakoribb 10 szó lekérdezése.
A válaszok JSON formátumban érkezzenek.

Feladat-3
---------
Írjon a Feladat-2 ben meghatározott interfészek közül legalább kettőhöz automata tesztet.



Válasz, megjegyzések, todo-k
----------------------------
Telepítés folyamata:
- a .zip kibontása után composer install paranccsal lehet feltelepíteni a környezetet a program számára (symfony, phpunit, stb).
- az adatbázis hozzáférések a config/parameters.yml file-ban állíthatóak be (mysql-el teszteltem)
- a lekérdezni kívánt url-ek listája a config/config_dev.yml file parameters.law_urls paraméterében állíthatóak be
- a hozzáférések beállítása után a php bin/console doctrine:database:create paranccsal lehet létrehozni az adatbázist
- a php bin/console doctrine:schema:update --force paranccsal pedig a táblákat
- a szótár összeállítását az /src/AppBundle/Command/ParseCommand.php file indításával lehet kezdeményezni (parancs: php bin/console app:parse -v)
- a REST funkciók teszteléséhez nem szükséges apache, a php bin/console server:run paranccsal elindítható a php beépített webszervere
- a REST funkciókat az src/AppBundle/DefaultController.php tartalmazza
- a tesztelhető url-ek, amikről GET-el lehet adatot lekérni:

http://127.0.0.1:8000/allwords

http://127.0.0.1:8000/searchlawsbyword/{keresendő szó}

http://127.0.0.1:8000/searchwordsbylaw/{jogszabályszöveg ID}

http://127.0.0.1:8000/top10

- a unittesztek a ./vendor/bin/simple-phpunit paranccsal futtathatóak


Néhány megjegyzés ("ezeket amúgy máshogy csinálnám" :) :
- A repository-ban query-ket használtam, ezek helyett szebb lett volna querybuilder-t alkalmazni (ill. valószínűleg az ORM annotációkkal is meg lehetett volna oldani a feladatot)
- a html file-ok parse-olását a command-ban rövidre zártam; nyilván, ha egy adott site-ról kell adatot lekérnem, jobban testreszabható a dolog
- a unittesztek az adatbázisban található adatok alapján dolgoznak; ez nem szép megoldás, a pontos tesztelés érdekében előre meghatározott értékkészlettel kellene dolgozniuk
- a teljes CRUD működést is érdemes lehet a REST API-n keresztül megvalósítani, akár jogosultságok kezelésével (oauth, stb).
- a routing bejegyzéseket érdemesebb kivezetni külön config file-ba
- ha "szabad kezet kapok", valószínűleg nem mysql-el kerestem volna, hanem valamilyen full-text search engine-el (pl.: elasticsearch)

