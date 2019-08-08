# Webová aplikace

## Požadavky
- PHP >=7.1
- Angular 7
- Composer

## Instalace composeru
Použijte instalační skript na této stránce
https://getcomposer.org/download/ --> _Command-line installation_. 
Vygeneruje se soubor "composer.phar". 
Poté zavoláš příkaz `php composer.phar install`, 
čímž se vygeneruje složka vendor.

## Nastavení VHosts během vývoje
Silně doporučuji nastavit si vhost, aby jsi neměl problémy s překladem adres.
Pro správnou funkčnost je potřeba mít 
správně nakonfigurovaný soubor `apache\apache2.4.35\conf\httpd.conf`:

- LoadModule vhost_alias_module modules/mod_vhost_alias.so
- Include conf/extra/httpd-vhosts.conf

Doporučené nastavení souboru `apache\apache2.4.35\conf\extra\httpd-vhosts.conf`
```
<VirtualHost *:80>
  ServerName joga.lh
  ServerAlias joga.lh
  DocumentRoot "sem_vloz_absolutni_cestu_ke_korenovemu_adresari_projektu"
  <Directory "/">
    Deny from all
    Allow from 127.0.0.1
    AllowOverride All
    Require local
  </Directory>
</VirtualHost>
```
Dále musíš upravit soubor `c:\Windows\System32\drivers\etc\hosts` kam přidáš tento záznam:
```
127.0.0.1 joga.lh
```
Nakonec restartuješ Apache a měl by jsi mít funkční vhost.

## Nastavení konfigurace serveru
- Ve složce `app` vytvoř složku `cache`.
- Ve složce `app/config` založ soubor `hidden_config.php` 
podle následující kostry a vyplň ji odpovídajícímí údaji:
```
<?php

define('DATABASE_HOST', 'vypln');
define('DATABASE_LOGIN', 'vypln');
define('DATABASE_PASS', 'vypln');
define('DATABASE_SCHEME', 'vypln');
define('JWT_ISSUER', 'vypln');
```
- Založ novou složku `public` v kořeni backend projektu.
- Vygeneruj si veřejný privátní klíč například pomocí nástroje `puTTY Key Generator`.
  - Veřejný klíč ulož do souboru `public/public.key`.
  - Privání klíč ulož do souboru `app/config/private.key`.
  
  Nyní nasimuluj jeden požadavek na server. Požadavek dopadne špatně, 
  ale ve složce `app/cache` se vytvoří soubor `map.php`. 
  
  Od této chvíle by měl být server schopný provozu.
  
  ## Tipy & triky
  Kdykoliv založíš novou třídu, je bohužel nutné zavolat v příkazovém řádku: 
  `composer update` pro aktualizování cache autoloaderu.
  
## REST-API

API disponuje standartními metodami:
GET, POST, PUT, UPDATE, DELETE 

### Odpovědi
Pro každou metodu se může lišit výsledek volání:

| Metoda/úspěšnost | úspech | neúspěch |
|:----------------:|:------:|:--------:|
|        GET       |   200  |    404   |
|       POST       |   204  |    400   |
|        PUT       |   201  |    400   |
|      UPDATE      |   204  |    404   |
|      DELETE      |   204  |    404   |