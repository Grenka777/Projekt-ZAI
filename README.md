# Projekt-ZAI
1. Uruchomienie projektu na XAMPP:
1.	Pobranie i instalacja XAMPP:
o	Pobierz XAMPP z oficjalnej strony.
o	Zainstaluj XAMPP zgodnie z instrukcjami.
2.	Konfiguracja XAMPP:
o	Uruchom XAMPP Control Panel.
o	Włącz moduły Apache i MySQL.
3.	Przygotowanie projektu:
o	Rozpakuj plik ZIP z projektem.
o	Skopiuj rozpakowane pliki do folderu htdocs w katalogu instalacyjnym XAMPP (np. C:\xampp\htdocs\loginsystem).
4.	Konfiguracja bazy danych:
o	Uruchom phpMyAdmin przez przeglądarkę, wchodząc na http://localhost/phpmyadmin.
o	Utwórz nową bazę danych.
o	Zaimportuj plik SQL z danymi bazy do utworzonej bazy danych.
5.	Konfiguracja plików projektu:
o	Edytuj plik konfiguracyjny projektu (np. config.php), aby wskazywał na utworzoną bazę danych.
6.	Uruchomienie projektu:
o	Otwórz przeglądarkę i wpisz http://localhost/loginsystem, aby zobaczyć działający projekt.
2. Uruchomienie projektu na Apache na Linuxie:
1.	Instalacja Apache i MySQL:
o	Zainstaluj Apache: sudo apt-get install apache2.
o	Zainstaluj MySQL: sudo apt-get install mysql-server.
2.	Instalacja PHP:
o	Zainstaluj PHP i niezbędne moduły: sudo apt-get install php libapache2-mod-php php-mysql.
3.	Konfiguracja Apache:
o	Skopiuj pliki projektu do katalogu /var/www/html/loginsystem
4.	Konfiguracja bazy danych:
o	Uruchom MySQL: sudo service mysql start.
o	Utwórz bazę danych i użytkownika:
bash
sudo mysql -u root -p
CREATE DATABASE loginsystem.sql;
CREATE USER 'użytkownik'@'localhost' IDENTIFIED BY 'hasło';
GRANT ALL PRIVILEGES ON loginsystem.sql* TO 'użytkownik'@'localhost';
FLUSH PRIVILEGES;
o	Zaimportuj plik SQL:
bash
mysql -u użytkownik -p loginsystem.sql < /ścieżka/do/pliku.sql
5.	Konfiguracja plików projektu:
o	Edytuj plik konfiguracyjny projektu, aby wskazywał na utworzoną bazę danych.
6.	Uruchomienie projektu:
o	Otwórz przeglądarkę i wpisz http://localhost/loginsystem.
3. Uruchomienie projektu przez Docker:
1.	Instalacja Docker:
o	Pobierz i zainstaluj Docker z oficjalnej strony.
2.	Przygotowanie plików Docker:
o	Upewnij się, że posiadasz plik Dockerfile oraz docker-compose.yml w katalogu projektu.
3.	Uruchomienie kontenera:
o	Przejdź do katalogu projektu i uruchom Docker Compose:
bash
docker-compose up -d
4.	Import bazy danych do kontenera:
o	Znajdź nazwę kontenera bazy danych:
bash
docker ps
o	Skopiuj plik SQL do kontenera:
bash
docker cp /ścieżka/do/pliku.sql nazwa_kontenera:/tmp/pliku.sql
o	Wejdź do kontenera:
bash
docker exec -it nazwa_kontenera bash
o	Zaimportuj bazę danych:
bash
mysql -u użytkownik -p TwojaBaza < /tmp/pliku.sql
5.	Konfiguracja plików projektu:
o	Upewnij się, że pliki konfiguracyjne projektu wskazują na bazę danych w kontenerze.
6.	Uruchomienie projektu:
o	Otwórz przeglądarkę i wpisz http://localhost:port, gdzie port to port skonfigurowany w docker-compose.yml.
Źródła:
•	Dokumentacja XAMPP
•	Dokumentacja Apache
•	Dokumentacja MySQL
•	Dokumentacja Docker

 
4.Dane do logowania: 
User: gh222@gmail.com
Password: Student@12345 lub zarejestrować się 
Admin: admin
Password: Student@12345
