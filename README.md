# Webová aplikace

## Požadavky
- PHP >=7.1
- Angular 7
- Composer

## Nastavení PHP části
### Instalace composeru
Použijte instalační skript na této stránce
https://getcomposer.org/download/ --> _Command-line installation_. 
Vygeneruje se soubor "composer.phar". 
Poté zavoláš příkaz `php composer.phar install`, 
čímž se vygeneruje složka vendor.

### Nastavení VHosts během vývoje
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

## Nastavení Angularu
### Hostování angular aplikace
Ve složce `joga-frontend` zavoláš postupně příkazy: `npm install` a `ng serve`. Těmito příkazy
se nejdříve nainstalují veškeré závislosti a příkazem `serve` se začne hostovat výsledná aplikace.

### Sestavení angular aplikace
Ve složce `joga-frontend` zavoláš příkaz `ng build --prod --base-href="/public/"` 
čímž se vygeneruje složka _dist_ s výslednou aplikací. Vedle souboru **index.php** vytvoříš složku
_public_, do které nakopíruješ obsah složky _dist_. 
Tímto způsobem se bude hostovat angular aplikace pomocí PHP serveru.
