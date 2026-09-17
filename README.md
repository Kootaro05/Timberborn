# Timberborn — Klausur- & Lern-Archiv

Ein gemeinsam gepflegtes Archiv für Zusammenfassungen, Cheatsheets, Altklausuren
und Quizze rund ums Studium. Jeder Kurs ist eine eigenständige, druckbare Seite im
einheitlichen Hausstil. Die Navigation baut sich **automatisch** aus der
Ordnerstruktur auf — neuen Ordner mit `index.html` ablegen, committen, fertig.

**Live:** [timberborn.de](https://timberborn.de)

---

## Inhalt

- [Wofür ist das gedacht?](#wofür-ist-das-gedacht)
- [Wie es funktioniert](#wie-es-funktioniert)
- [Für Nutzer:innen — die Seite verwenden](#für-nutzerinnen--die-seite-verwenden)
- [Für Mitwirkende — Inhalte hinzufügen](#für-mitwirkende--inhalte-hinzufügen)
- [Konventionen (Referenz)](#konventionen-referenz)
- [Deployment](#deployment)

---

## Wofür ist das gedacht?

Das Archiv sammelt an einem Ort, was man zum Lernen und zur Klausurvorbereitung
braucht — pro Kurs gebündelt und einheitlich aufbereitet:

- **Zusammenfassungen / Cheatsheets** als kompakte, druckbare Kursseiten
- **Altklausuren** samt Lösungswegen
- **Quizze** zum Selbsttest
- **Lernpfade** durch ein Thema

Es ist ein **statisches Archiv** ohne Login, ohne Datenbank. Alles sind schlichte
HTML-Seiten, die dauerhaft erreichbar bleiben und sich sauber als PDF drucken lassen.
Gepflegt wird es gemeinsam von den Kommiliton:innen über Git.

---

## Wie es funktioniert

Der Kern ist eine feste Ordner-Konvention:

```
/<Studienrichtung>/<Semester>/<Kurs>/index.html
```

Beispiel aus dem aktuellen Bestand:

```
Informatik/
└── 2.Semester/
    ├── Analysis/            index.html         → Kursseite
    ├── Python/              index.html         → Kursseite
    ├── Datenverarbeitung/
    │   └── Lernpfad/        index.html         → Unterseite
    └── Digitaltechnik/      index.html         → Kursseite (Übersicht)
        ├── Altklausuren/
        │   └── Juni_2026/   index.html         → Unterseite
        └── Quiz/            index.html         → Unterseite
```

Der Navigator `index.php` **scannt bei jedem Build die Ordner** und erzeugt daraus die
Navigationsseite. Dabei gilt:

- Ein Ordner mit `index.html` wird zu einer **Seite** (direkter Link).
- Ein Ordner mit Unterordnern wird zu einem **Zweig** (aufklappbar) — beliebig tief.
- Hat ein Ordner **beides** (eigene `index.html` **und** Unterordner), wird er ein Zweig
  mit zusätzlicher Übersichtsseite (so wie `Digitaltechnik` oben).
- Ordner ohne irgendeine `index.html` (auch tief) tauchen gar nicht erst auf.

Wichtig: `index.php` läuft **nicht** live auf dem Server, sondern nur einmal beim
Deploy. Es rendert die komplette Navigation als statisches HTML (der Ordnerbaum wird
als JSON in die Seite eingebettet, die Aufklapp-Logik läuft danach clientseitig in
JavaScript). Besucher:innen bekommen also reines HTML ohne PHP — deshalb kann das
Ganze kostenlos auf GitHub Pages liegen.

---

## Für Nutzer:innen — die Seite verwenden

Einfach [timberborn.de](https://timberborn.de) öffnen. Auf der Startseite:

1. **Studienrichtung** wählen (z. B. *Informatik*).
2. **Semester** aufklappen.
3. **Kurs** anklicken — die Kursseite öffnet sich.
4. Innerhalb eines Kurses ggf. weitere Zweige aufklappen (z. B. *Altklausuren → Juni 2026*).

Jede Kursseite ist in sich abgeschlossen und **druckbar**: über den Druckdialog des
Browsers (`Strg`/`Cmd` + `P`) → „Als PDF speichern" bekommst du eine saubere PDF-Version.

---

## Für Mitwirkende — Inhalte hinzufügen

Neue Inhalte kommen per Git dazu. Der Ablauf im Überblick: Ordner anlegen →
`index.html` reinlegen → committen → pushen. Das Deployment passiert automatisch.

### 1. Repo klonen

```bash
git clone git@github.com:kootaro05/timberborn.git
cd timberborn
```

*(Repo-Namen ggf. anpassen, falls er bei euch anders heißt.)*

### 2. Lokale Vorschau (optional, empfohlen)

Wenn du PHP installiert hast, kannst du die Navigation lokal genau so sehen wie live:

```bash
php -S localhost:8000
```

Dann [http://localhost:8000](http://localhost:8000) öffnen. `index.php` scannt deine
Ordner live mit — neue Kurse erscheinen sofort nach dem Neuladen. Ohne PHP kannst du
alternativ die einzelne Kursseite direkt im Browser öffnen (`index.html` doppelklicken).

### 3. Neuen Kurs anlegen

Ordnerpfad nach der Konvention erstellen und eine `index.html` hineinlegen:

```bash
mkdir -p "Informatik/2.Semester/Lineare_Algebra"
# deine fertige Seite dort als index.html speichern
```

Für die Kursseite selbst gibt es die Vorlage **`Vorlage.md`** im Repo-Wurzelverzeichnis:
Sie enthält einen fertigen KI-Prompt, mit dem aus einer beliebigen Zusammenfassung eine
`index.html` im exakten Hausstil entsteht. So geht's:

1. `Vorlage.md` öffnen, alles ab **„PROMPT START"** bis zum Ende kopieren.
2. In einen KI-Chat einfügen und deine fertige Zusammenfassung darunterhängen.
3. Die zurückgegebene `index.html` in den Kursordner legen.

Alternativ kannst du eine bestehende Kursseite (z. B. `Informatik/2.Semester/Python/index.html`)
als Startpunkt kopieren und inhaltlich anpassen.

### 4. Namen richtig wählen

- **Keine Leerzeichen** in Ordnernamen — nimm Unterstriche. `Juni_2026` wird in der
  Anzeige automatisch zu „Juni 2026".
- Semester nach dem Muster `1.Semester`, `2.Semester`, … — der Punkt wird in der Anzeige
  automatisch zu „1. Semester".
- Reservierte Namen meiden (siehe [Ignore-Liste](#konventionen-referenz)): ein Ordner
  namens `assets`, `js`, `img` o. Ä. wird **nicht** als Kurs angezeigt.

### 5. Optional: Titel & Beschreibung überschreiben

Standardmäßig wird der Anzeigename aus dem Ordnernamen abgeleitet. Willst du davon
abweichen, leg eine `meta.json` in den Kurs- (oder Zweig-)Ordner:

```json
{
  "titel": "Lineare Algebra I",
  "beschreibung": "Vektorräume, Matrizen, Eigenwerte"
}
```

### 6. Committen & pushen

```bash
git add .
git commit -m "Neuer Kurs: Lineare Algebra (2. Semester)"
git push
```

Nach dem Push baut GitHub die Navigation neu und veröffentlicht automatisch. Nach
~1 Minute ist der neue Kurs auf [timberborn.de](https://timberborn.de) sichtbar. Für
größere Änderungen empfiehlt sich ein Branch + Pull Request, damit jemand kurz
drüberschaut.

---

## Konventionen (Referenz)

**Anzeigenamen** werden aus Ordnernamen abgeleitet:

| Ordnername        | Anzeige            | Regel                                    |
|-------------------|--------------------|------------------------------------------|
| `Juni_2026`       | Juni 2026          | Unterstrich → Leerzeichen                |
| `2.Semester`      | 2. Semester        | Ziffer + Punkt → Leerzeichen dahinter    |
| `Lineare_Algebra` | Lineare Algebra    | beides kombiniert                        |

**Sortierung:** natürlich und ohne Groß-/Kleinschreibungs-Unterschied
(`2.Semester` vor `10.Semester`).

**Ignorierte Ordnernamen** (tauchen nie als Kurs auf):
`assets`, `css`, `js`, `img`, `images`, `vendor`, `node_modules`, `.git`, `.github`
sowie alles, was mit einem Punkt beginnt.

**`meta.json`** (optional, pro Ordner):

| Feld           | Wirkung                                  |
|----------------|------------------------------------------|
| `titel`        | überschreibt den angezeigten Namen       |
| `beschreibung` | Kurztext unter dem Kurs                   |

**Hausstil der Kursseiten:** heller Pergament-Hintergrund, Indigo als Leitfarbe,
Schriften *Space Grotesk* / *Inter* / *JetBrains Mono*. Jede Seite ist self-contained
(CSS inline, nur Google Fonts extern) und PDF-druckbar. Als verbindliche Vorlage dient
`Vorlage.md`.

---

## Deployment

Gehostet auf **GitHub Pages** unter der Custom Domain `timberborn.de`.

Bei jedem Push auf den Standard-Branch läuft ein GitHub-Actions-Workflow, der

1. `php index.php > index.html` ausführt (Navigation statisch rendern) und
2. das Ergebnis samt aller Kursordner auf GitHub Pages deployt.

Dadurch braucht der Live-Betrieb **kein PHP** — es genügt zum lokalen Vorschauen. Der
Workflow liegt unter `.github/workflows/deploy.yml`.

---

*Beiträge willkommen — jede Zusammenfassung hilft dem ganzen Jahrgang.* 🌲
