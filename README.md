# Willkommen bei Smarthome #

**Smarthome** ist eine Web-Applikation zur Steuerung einer **Hausautomatisierung** (z.B. Homematic) verwendbar auf Smartphone, Tablet und Desktop.

![](http://rgies.de/smarthome/Smarthome.png)

Die Oberfläche basiert auf flexiblen Panels die beliebig benannt und mit Funktionen bestückt werden können. Die Funktionen sind sehr einfach durch Module erweiterbar (Wetter Informationen, Live-Bild WebCam, etc.). Die Panels können beliebig nach Räumen oder Gewerken organisiert werden.

## Funktionsumfang ##

**Smarthome** verwendet ein sehr stark reduziertes Benutzerinterface für eine sehr einfache Bedienung. Die Benutzeroberfläche passt sich automatisch an die Displaygröße verschiedener Geräte (Smartphone, Tablet, Desktop) an.

**Aktuell unterstützte Komponenten:**

- Homematic Lichtschalter
- Homematic Rollandenschalter
- Homematic Heizkörper Thermostate
- Homematic Zwischenstecker
- Homematic Service Meldungen auslesen
- Homematic Variablen auslesen
- WebCam einbinden

### Systemvorraussetzungen ###

Die Anwendung wird bei mir auf einem QNAP Nas auf einem Apache Webserver mit PHP Unterstützung betrieben. Die Anwendung ist in PHP geschrieben und verwendet [Twitter Bootstrap](http://getbootstrap.com) und [jQuery](http://jquery.com) für die Benutzeroberfläche.

- Webserver Apache, Nginx oder IIS mit PHP Module >=5.3.x
- Homematic CCU2 für Hausautomatisierung


> Es werden keine zusätzlichen Erweiterungen für die Homematic benötigt. Smarthome kommuniziert mit der Homematic CCU über die standard XmlRpc Schnittstelle.

## Installation ##

1. Stellen sie sicher, das PHP Dateien auf Ihrem Webserver ausführbar sind.
2. Kopieren Sie das Verzeichnis smarthome auf Ihren Webserver.
3. Kopieren Sie die Datei **/Config/Config-dist.xml** zu **/Config/Config.xml**.
4. Konfigurieren Sie dann in der **/Config/Config.xml** Datei Ihre Homematic Komponenten.
5. Rufen Sie über einen Webbrowser die ../Public/index.php auf.

> Smarthome besitzt keine eigene Benutzerauthentifizierung. Wenn Sie Smarthome im Internet freigeben möchten schützen Sie am besten den Zugriff über eine .htaccess Datei mit HTTP BASIC Authentication. Verschiedne NAS Systeme z.B. QNAP bieten über diesen Weg einen Anbindung an die vorhandene Benutzerverwaltung. 
