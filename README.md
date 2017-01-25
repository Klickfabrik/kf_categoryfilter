kf_categoryfilter
---------------------

|         |                       |
|---------|-----------------------|
| Author: | Marc Finnern          |
| E-Mail: | typo3@klickfabrik.net |
| Status: | in Entwicklung        |


#### Allgemeines
Das Plugin: categoryfilter ist dafür gedacht, dass man die Systemkategorien als Verlinkungen verwenden kann.

#### Besonderheiten
+ Erweiterungen der Systemkategorien um weitere Felder (link & class)
+ Erweiterungen der Backendansicht für das Modul

#### Übersicht der Modul-Eigenschaft

| Modus                 | Erklärung                                                                  |
|-----------------------|----------------------------------------------------------------------------|
| List                  | Ansicht der Kategorien, nach Auswahl                                       |
| List (aktuelle Seite) | Ansicht der Kategorien der aktuellen Seite, keine Auswahl nötig            |
| Seiten                | Ansicht der Seiten bestimmter Kategorien, nach Auswahl                     |
| Isotope               | Ansicht der Kategorien mit Filter und Bild(er) aus den Seiteneigenschaften |

| Option          | Erklärung                                                                                            |
|-----------------|------------------------------------------------------------------------------------------------------|
| Limit           | Anzahl der Ausgaben                                                                                  |
| Sortierung      | Auf und absteigend sortieren                                                                         |
| Sortierung nach | Default: page.uid  Optionen: uid, name, sorting (Seitenbaum sortierung)                              |
| Rekursiv        | Diese option schaut nach allen Elementen von der Auswahl. Die Auswahl selber wird nicht dargestellt. |
| Debug           | Zeigt <f:debug>{_all}</f:debug>      