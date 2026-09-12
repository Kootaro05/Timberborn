# Vorlage: Kurs-Zusammenfassung -> `index.html` (Hausstil „Klausur-Guide")

**Zweck:** Aus einer fertigen Zusammenfassung eine Kurs-Seite bauen, die optisch
**identisch** zu den bestehenden Guides im Archiv aussieht (heller Pergament-Stil,
Indigo-Leitfarbe, Space Grotesk / Inter / JetBrains Mono) und sauber als **PDF**
gedruckt werden kann.

**So benutzt du diese Datei:** Kopiere alles ab der Linie **„PROMPT START"** bis zum
Ende in einen beliebigen KI-Chat, haenge deine fertige Zusammenfassung darunter an,
schick es ab. Die KI gibt dir eine fertige `index.html` zurueck.

Die Datei legst du in den Kursordner: `/Studienrichtung/Semester/Kurs/index.html`
-> der Kurs erscheint dann automatisch im Archiv.

---

## ============== PROMPT START ==============

Du bekommst weiter unten eine **Klausur-Zusammenfassung**. Baue daraus **eine einzige,
vollstaendige `index.html`** im unten definierten **Hausstil**. Gib **nur den HTML-Code**
aus, ohne Erklaerung, ohne Markdown-Backticks.

### Harte Regeln
1. **Eine einzige Datei**, alles inline. Keine lokalen Assets. Muss in jedem Unterordner
   funktionieren.
2. **Stil-Geruest unten 1:1 uebernehmen** — Schriften, Farben (`:root`), alle CSS-Klassen,
   das MathJax-Setup **und** das Auto-Fit-Skript fuer Formeln. Nichts davon weglassen oder
   umbenennen; nur den Inhalt einsetzen.
3. **Inhalt vollstaendig** uebernehmen — nichts erfinden, nichts streichen. Nur in die
   vorhandenen Bausteine einordnen.
4. **Mathe** mit MathJax: inline `$ ... $`, abgesetzt `$$ ... $$`.
5. Saubere Ueberschriften-Hierarchie (`h1` -> `h2` -> `h3` -> `h4`), sichtbarer Fokus,
   responsive (Breakpoint 720px ist im CSS schon angelegt).

### Bausteine (welche Box wofuer)
- **`.erklaer`** (amber, mit Emoji-`.ico`): lockere Einleitung „Was mache ich hier und warum?".
- **`.ref`** (weisse Karte, gestrichelte Trenner): Formel-/Regelsammlung. Pro Regel ein
  `.rule` mit `.rname` (Kurzname in Mono) + Formel.
- **`.grid`** mit **`.weg`** (Rechenweg, breitere Spalte) **+ `.safe`** (Indigo-Kasten
  „Klausur-Safe": exakt das, was aufs Klausurblatt muss). Im Druck stapeln sich beide
  automatisch untereinander.
- **`.beispiel`** (gruen): komplett durchgerechnete Aufgabe. Einzelschritte als `.schritt`
  mit `.stitle`; Endergebnis in `.result`.
- **`.achtung`** (rot): kurze Warnung / typische Fehler.
- Jede Aufgabe ist eine `.aufgabe`-`<section>` mit Kopf `.a-head` (`.a-num` = Nummernkreis,
  `.a-titles` = Eyebrow + `h2`). Die Indigo-Linie am linken Rand ist die „Leitlinie" durch
  die Klausur.

### Pflicht-Struktur (Body-Skelett — fuell die Platzhalter)
```html
<body>
<div class="toolbar">
  <a class="back" href="../../../index.php">&larr; Archiv</a>
  <span class="brand">KURS &middot; Klausur-Guide</span>
  <button class="printbtn" onclick="window.print()">Als PDF speichern / drucken</button>
</div>

<div class="wrap">
  <header class="cover">
    <p class="eyebrow">EYEBROW &middot; z. B. von Studis fuer Studis</p>
    <h1>TITEL<span class="sub">UNTERTITEL</span></h1>
    <p class="tag">Ein, zwei Saetze, worum es geht.</p>
  </header>

  <div class="legend">
    <div class="chip a"><span class="lbl"><span class="dot"></span>Erklaerkasten</span><p>...</p></div>
    <div class="chip k"><span class="lbl"><span class="dot"></span>Klausur-Safe</span><p>...</p></div>
    <div class="chip b"><span class="lbl"><span class="dot"></span>Beispiel</span><p>...</p></div>
  </div>

  <section class="vorwort"><h2>Vorwort</h2><p>...</p></section>
  <hr class="div">

  <section class="aufgabe" id="a1">
    <div class="a-head">
      <div class="a-num">1</div>
      <div class="a-titles"><div class="ey">Aufgabe 1</div><h2>THEMA</h2></div>
    </div>

    <div class="erklaer"><div class="ico">&#129504;</div><div class="body"><p>...</p></div></div>

    <h3>Regeln</h3>
    <div class="ref"><div class="rule"><span class="rname">1 &middot; Name</span>$Formel$</div></div>

    <div class="grid">
      <div class="weg"><h3>Rechenweg</h3><p>...</p><div class="result">$Ergebnis$</div></div>
      <div class="safe"><div class="safe-head"><span>&#9998;</span> Klausur-Safe</div>
        <div class="safe-body"><ol><li>...</li></ol></div></div>
    </div>

    <div class="beispiel"><div class="b-head"><span>&#9656;</span> Beispielaufgabe</div>
      <div class="b-body">
        <div class="schritt"><div class="stitle">Schritt 1 &middot; ...</div><p>...</p></div>
        <div class="result">Antwort: ...</div>
      </div>
    </div>

    <div class="achtung"><b>Achtung:</b> typischer Fehler ...</div>
  </section>
  <!-- weitere <section class="aufgabe"> ... -->

  <footer><span>KURS &middot; Klausur-Guide</span><span>Von Studis fuer Studis &middot; ohne Gewaehr</span></footer>
</div>
</body>
```

---

## *** REGELN ZUR PDF-ERSTELLUNG (sehr wichtig) ***

Das PDF entsteht ueber **Browser -> Drucken -> „Als PDF speichern"**, kein PDF-Generator.
Die folgenden Regeln **stecken bereits im `@media print`-Block des Stil-Geruests** und
**muessen erhalten bleiben**:

- `@page { size:A4; margin:14mm 14mm 16mm; }`, im Druck **weisser Hintergrund**.
- **Toolbar** (inkl. „Archiv"-Link & PDF-Button) wird ausgeblendet — landet nie im PDF.
- **Zweispaltiges `.grid` wird im Druck einspaltig** -> Rechenweg und Klausur-Safe nutzen
  die volle Breite, nichts wird abgeschnitten.
- **Jede `.aufgabe` beginnt auf einer neuen Seite** (`break-before:page`), die erste nicht.
- **Boxen werden nie mitten durchgeschnitten** (`break-inside:avoid` fuer
  `.erklaer .ref .achtung .chip .schritt .beispiel .safe`); passt eine Box nicht mehr aufs
  Seitenende, rutscht sie komplett auf die naechste Seite.
- **Farbige Kopfzeilen** (`.b-head .safe-head`) bleiben mit ihrem Inhalt zusammen
  (`break-after:avoid`) — keine verwaisten Koepfe am Seitenende.
- **Keine Waisen-/Hurenkinder**: `p, li { orphans:3; widows:3; }`.
- **Formeln** werden per Auto-Fit-Skript an die A4-Breite skaliert, statt abgeschnitten zu
  werden — das Skript `__fitMath` daher unbedingt mit uebernehmen.

**Selbsttest vor der Ausgabe:** weisser Grund, keine abgeschnittenen Formeln/Kaesten, jede
Aufgabe auf eigener Seite, keine einsame Kopfzeile am Seitenende, kein Button im PDF.

---

## HIER DAS STIL-GERUEST — 1:1 in den `<head>` uebernehmen
(Fonts + MathJax-Setup + Auto-Fit-Skript + komplettes `<style>` inkl. Druckregeln)

```html
<script>
  window.MathJax = {
    tex: {
      inlineMath: [['$', '$']],
      displayMath: [['$$', '$$']],
      packages: {'[+]': ['ams']}
    },
    chtml: { scale: 1.0 },
    startup: {
      ready() {
        MathJax.startup.defaultReady();
        MathJax.startup.promise.then(function () {
          if (window.__fitMath) window.__fitMath(false);
        });
      }
    }
  };
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/3.2.2/es5/tex-mml-chtml.js" id="MathJax-script" async></script>

<script>
  /* Passt jede Formel an die verfügbare Breite an:
     - am Bildschirm an die Breite ihrer Spalte/ihres Kastens
     - im Druck an die A4-Spaltenbreite
     Zu breite Formeln werden proportional verkleinert, statt zu scrollen oder abgeschnitten zu werden. */
  (function () {
    var PRINT_W = 680; // nutzbare Breite einer A4-Spalte in px (210mm - 2x14mm Rand)
    function fitMath(forPrint) {
      var nodes = document.querySelectorAll('mjx-container');
      for (var i = 0; i < nodes.length; i++) {
        var c = nodes[i];
        c.style.fontSize = ''; // erst zurücksetzen, dann frisch messen
        var math = c.querySelector('mjx-math') || c;
        var natural = math.getBoundingClientRect().width; // Eigenbreite der Formel
        if (!natural) continue;
        var avail;
        if (forPrint) {
          avail = PRINT_W;
        } else {
          var p = c.parentElement;
          avail = p ? p.clientWidth : natural; // tatsächliche Breite der Spalte/des Kastens
        }
        if (avail > 0 && natural > avail) {
          var scale = Math.max(0.5, (avail / natural) * 0.96);
          c.style.fontSize = (scale * 100).toFixed(1) + '%';
        }
      }
    }
    window.__fitMath = fitMath;

    // Nach jedem Layout-Wechsel am Bildschirm neu anpassen (Fenstergröße, Spaltenumbruch bei 720px)
    var t;
    window.addEventListener('resize', function () {
      clearTimeout(t);
      t = setTimeout(function () { fitMath(false); }, 120);
    });
    window.addEventListener('load', function () { fitMath(false); });

    // Druck: an A4 anpassen, danach wieder an den Bildschirm
    window.addEventListener('beforeprint', function () { fitMath(true); });
    window.addEventListener('afterprint', function () { fitMath(false); });
  })();
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

<style>
  :root{
    --ink:#191d2b;
    --ink-soft:#454b5e;
    --paper:#fbfaf6;
    --line:#e4e0d6;

    --amber:#e08a1e;
    --amber-bg:#fdf3e1;
    --amber-edge:#f3d9a8;

    --indigo:#3a4cf0;
    --indigo-bg:#eef0ff;
    --indigo-edge:#c5ccff;

    --green:#179a5c;
    --green-bg:#e9faf0;
    --green-edge:#b5e8cd;

    --red:#d4452f;
    --red-bg:#fdecea;
  }

  *{box-sizing:border-box;}
  html{ -webkit-text-size-adjust:100%; }
  body{
    margin:0;
    background:var(--paper);
    color:var(--ink);
    font-family:'Inter',system-ui,sans-serif;
    font-size:16px;
    line-height:1.6;
    -webkit-font-smoothing:antialiased;
    word-wrap: break-word;
  }

  .wrap{ max-width:980px; margin:0 auto; padding:48px 28px 90px; }

  .toolbar{
    position:sticky; top:0; z-index:50;
    display:flex; justify-content:space-between; align-items:center;
    gap:16px; flex-wrap:wrap;
    background:rgba(251,250,246,.88);
    backdrop-filter:blur(8px);
    border-bottom:1px solid var(--line);
    padding:12px 28px;
  }
  .toolbar .brand{ font-family:'JetBrains Mono',monospace; font-size:12px; letter-spacing:.14em; text-transform:uppercase; color:var(--ink-soft); }
  .printbtn{
    font-family:'JetBrains Mono',monospace; font-size:12px; letter-spacing:.06em;
    background:var(--ink); color:#fff; border:none; border-radius:8px;
    padding:9px 16px; cursor:pointer; transition:transform .12s ease, opacity .12s;
  }
  .printbtn:hover{ transform:translateY(-1px); opacity:.9; }
  .printbtn:focus-visible{ outline:3px solid var(--indigo); outline-offset:2px; }

  .cover{ padding:18px 0 8px; }
  .eyebrow{ font-family:'JetBrains Mono',monospace; font-size:12.5px; letter-spacing:.22em; text-transform:uppercase; color:var(--indigo); margin:0 0 14px; }
  h1{
    font-family:'Space Grotesk',sans-serif; font-weight:700;
    font-size:clamp(40px,7vw,76px); line-height:.98; letter-spacing:-.02em;
    margin:0 0 6px;
  }
  h1 .sub{ display:block; color:var(--indigo); }
  .tag{ color:var(--ink-soft); font-size:18px; max-width:54ch; margin:14px 0 0; }

  .legend{ display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin:30px 0 8px; }
  .chip{ border:1px solid var(--line); border-radius:14px; padding:16px 16px 15px; background:#fff; }
  .chip .lbl{ font-family:'JetBrains Mono',monospace; font-size:11px; letter-spacing:.12em; text-transform:uppercase; font-weight:700; display:inline-flex; align-items:center; gap:8px; }
  .chip .dot{ width:11px; height:11px; border-radius:3px; display:inline-block; }
  .chip p{ margin:9px 0 0; font-size:13.5px; color:var(--ink-soft); line-height:1.45; }
  .chip.a .lbl{ color:var(--amber); } .chip.a .dot{ background:var(--amber); }
  .chip.k .lbl{ color:var(--indigo); } .chip.k .dot{ background:var(--indigo); }
  .chip.b .lbl{ color:var(--green); } .chip.b .dot{ background:var(--green); }

  .vorwort{ margin-top:34px; }
  .vorwort h2{ font-family:'Space Grotesk',sans-serif; font-size:13px; letter-spacing:.2em; text-transform:uppercase; color:var(--ink-soft); font-weight:600; margin:0 0 12px; }
  .vorwort p{ margin:0 0 14px; }

  hr.div{ border:none; border-top:1px solid var(--line); margin:46px 0; }

  .aufgabe{
    position:relative;
    border-left:3px solid var(--indigo);
    padding:6px 0 8px 34px;
    margin:0 0 8px;
  }
  .aufgabe + .aufgabe{ margin-top:0; }
  .a-head{ display:flex; align-items:center; gap:18px; margin:18px 0 18px -52px; }
  .a-num{
    flex:none;
    width:54px; height:54px; border-radius:50%;
    background:var(--indigo); color:#fff;
    display:flex; align-items:center; justify-content:center;
    font-family:'Space Grotesk',sans-serif; font-weight:700; font-size:24px;
    box-shadow:0 0 0 6px var(--paper);
  }
  .a-titles .ey{ font-family:'JetBrains Mono',monospace; font-size:11px; letter-spacing:.18em; text-transform:uppercase; color:var(--indigo); }
  .a-titles h2{ font-family:'Space Grotesk',sans-serif; font-weight:700; font-size:30px; letter-spacing:-.01em; margin:2px 0 0; }

  h3{ font-family:'Space Grotesk',sans-serif; font-weight:600; font-size:19px; margin:26px 0 10px; }
  h4{ font-family:'Inter',sans-serif; font-weight:700; font-size:15px; margin:18px 0 6px; }
  p{ margin:0 0 12px; }
  .muted{ color:var(--ink-soft); }
  strong{ font-weight:700; }

  .erklaer{
    background:var(--amber-bg);
    border:1px solid var(--amber-edge);
    border-left:5px solid var(--amber);
    border-radius:14px;
    padding:18px 20px 18px 20px;
    margin:6px 0 8px;
    display:flex; gap:14px;
  }
  .erklaer .ico{ font-size:24px; line-height:1.2; flex:none; }
  .erklaer .body{ flex:1; }
  .erklaer p:last-child{ margin-bottom:0; }

  .grid{ display:grid; grid-template-columns:1.35fr 1fr; gap:22px; margin:18px 0; align-items:start; }
  
  .weg, .safe { min-width: 0; } 
  
  .weg h3:first-child, .weg h4:first-child{ margin-top:0; }

  .safe{
    background:var(--indigo-bg);
    border:1px solid var(--indigo-edge);
    border-radius:14px;
    position:relative;
    background-image:
      linear-gradient(var(--indigo-bg),var(--indigo-bg)),
      linear-gradient(rgba(58,76,240,.07) 1px, transparent 1px),
      linear-gradient(90deg, rgba(58,76,240,.07) 1px, transparent 1px);
    background-size:100% 100%, 22px 22px, 22px 22px;
    background-blend-mode:normal;
  }
  .safe .safe-head{
    background:var(--indigo); color:#fff;
    border-radius: 13px 13px 0 0;
    font-family:'JetBrains Mono',monospace; font-size:11px; letter-spacing:.14em; text-transform:uppercase; font-weight:700;
    padding:9px 16px; display:flex; align-items:center; gap:8px;
  }
  .safe .safe-body{ 
    padding:14px 16px 14px; 
    overflow:visible; 
  }
  .safe ol{ margin:2px 0 12px; padding-left:20px; }
  .safe li{ margin:0 0 10px; }
  .safe p{ margin:0 0 10px; }
  .safe p:last-child { margin-bottom: 0; }
  .safe .note{ font-size:13.5px; color:var(--ink-soft); }

  .ref{
    background:#fff; border:1px solid var(--line); border-radius:14px;
    padding:6px 20px; margin:16px 0;
    overflow:visible;
  }
  .ref .rule{ padding:11px 0; border-bottom:1px dashed var(--line); }
  .ref .rule:last-child{ border-bottom:none; }
  .ref .rname{ font-family:'JetBrains Mono',monospace; font-size:11px; letter-spacing:.1em; text-transform:uppercase; color:var(--indigo); display:block; margin-bottom:3px; }

  .achtung{
    background:var(--red-bg); border:1px solid #f3c4bc; border-left:5px solid var(--red);
    border-radius:12px; padding:13px 16px; margin:14px 0; font-size:14.5px;
  }
  .achtung b{ color:var(--red); }

  .beispiel{
    background:var(--green-bg);
    border:1px solid var(--green-edge);
    border-radius:16px;
    margin:22px 0 6px;
  }
  .beispiel .b-head{
    background:var(--green); color:#fff;
    border-radius: 15px 15px 0 0;
    font-family:'JetBrains Mono',monospace; font-size:11.5px; letter-spacing:.14em; text-transform:uppercase; font-weight:700;
    padding:10px 20px; display:flex; align-items:center; gap:9px;
  }
  .beispiel .b-body{ 
    padding:6px 22px 14px; 
    overflow:visible; 
  }
  .beispiel .b-body h4{ color:#0d6b3f; }
  .schritt{ margin:14px 0; }
  .schritt .stitle{ font-family:'JetBrains Mono',monospace; font-weight:700; font-size:12px; letter-spacing:.05em; color:#0d6b3f; text-transform:uppercase; margin-bottom: 6px; }
  .result{
    background:#fff; border:1px dashed var(--green); border-radius:10px;
    padding:8px 16px; margin:8px 0; font-weight:600;
  }

  mjx-container{ 
    overflow:visible !important; 
    max-width: 100%; 
    padding: 6px 0; 
  }
  mjx-container[display="true"]{ margin:10px 0 !important; }

  .cases{ 
    background:#fff; border:1px solid var(--line); border-radius:10px; 
    padding:8px 16px; margin:10px 0; 
    overflow:visible; 
  }

  footer{ margin-top:60px; padding-top:18px; border-top:1px solid var(--line); font-family:'JetBrains Mono',monospace; font-size:11.5px; letter-spacing:.1em; text-transform:uppercase; color:var(--ink-soft); display:flex; justify-content:space-between; flex-wrap:wrap; gap:8px; }

  @media (max-width:720px){
    .wrap{ padding:30px 16px 70px; }
    .legend{ grid-template-columns:1fr; }
    .grid{ grid-template-columns:1fr; }
    .aufgabe{ padding-left:22px; }
    .a-head{ margin-left:-40px; gap:12px; }
    .a-num{ width:44px; height:44px; font-size:20px; }
    .a-titles h2{ font-size:24px; }
    body{ font-size:15.5px; }
  }

  /* ---------- PRINT OPTIMIERUNGEN ---------- */
  @media print{
    @page{ size:A4; margin:14mm 14mm 16mm; }
    html,body{ background:#fff; font-size:10.5pt; line-height:1.4; }
    .toolbar{ display:none !important; }
    .wrap{ max-width:none; padding:0; }
    *{ -webkit-print-color-adjust:exact; print-color-adjust:exact; }

    /* Jede Aufgabe beginnt auf einer neuen Seite */
    .aufgabe{
      break-before:page; page-break-before:always;
      padding:4px 0 6px 28px;          /* weniger Einzug, damit nichts in den Rand läuft */
      border-left:2px solid var(--indigo);
    }
    #a1{ break-before:auto; page-break-before:auto; }

    /* Aufgaben-Kopf: Kreis nicht in den Seitenrand schieben (sonst angeschnitten) */
    .a-head{ margin:14px 0 14px -28px; gap:12px; }
    .a-num{ width:40px; height:40px; font-size:20px; box-shadow:0 0 0 4px #fff; }
    .a-titles h2{ font-size:22px; }

    /* Deckblatt etwas kompakter */
    h1{ font-size:34pt; line-height:1; }
    .tag{ font-size:11.5pt; }
    .legend{ gap:10px; margin:18px 0 6px; }

    /* KERN-FIX: Das zweispaltige Layout im PDF aufheben ->
       Rechenweg und Klausur-Safe stehen untereinander und nutzen die VOLLE Seitenbreite.
       Dadurch sind die Kästen nicht mehr zu schmal und Formeln werden nicht abgeschnitten. */
    .grid{ grid-template-columns:1fr; gap:12px; margin:12px 0; }
    .safe .safe-body{ font-size:10pt; }

    /* Boxen NICHT mitten durchschneiden -> möglichst komplett auf EINER Seite halten.
       Passt eine Box nicht aufs Seitenende, rutscht sie als Ganzes auf die nächste Seite. */
    .erklaer,.ref,.achtung,.chip,.schritt,.cases,.result,.beispiel,.safe{
      break-inside:avoid; page-break-inside:avoid;
    }
    /* Notfall-Ventil: Nur falls eine Box höher als eine ganze Seite ist, darf ihr Innenteil
       umbrechen — dann aber sauber zwischen ganzen Schritten (Schritte bleiben unzerteilt). */
    .grid,.weg,.b-body,.safe-body{ break-inside:auto; }

    /* Farbige Kopfzeilen NIE allein am Seitenende stehen lassen ->
       Kopf bleibt mit dem Anfang seines Inhalts zusammen (keine verwaisten Köpfe mehr) */
    .b-head,.safe-head{ break-after:avoid; page-break-after:avoid; }
    .b-body,.safe-body{ break-before:avoid; page-break-before:avoid; }
    .beispiel .b-body .schritt:first-child,
    .b-body > p:first-child,
    .safe-body > *:first-child{ break-before:avoid; page-break-before:avoid; }

    .a-head,h3,h4{ break-after:avoid; }
    /* Keine einzelnen Zeilen am Seitenanfang/-ende */
    p,li{ orphans:3; widows:3; }

    /* MathJax für den Druck zähmen; zu breite Formeln werden zusätzlich per Skript passgenau skaliert */
    mjx-container{ font-size:90%; }
    mjx-container[display="true"]{ margin:8px 0 !important; }

    /* Keine Scrollbereiche im PDF abschneiden */
    .safe .safe-body, .beispiel .b-body, .ref, mjx-container, .cases{
      overflow:visible !important;
      white-space:normal !important;
    }

    a{ color:inherit; text-decoration:none; }
  }
</style>
```

### Optional: `meta.json` neben die `index.html` (schoenerer Titel im Archiv)
```json
{ "titel": "Analysis", "beschreibung": "Kurz, worum es geht" }
```

## ============== PROMPT ENDE ==============

**Hier folgt meine Zusammenfassung:**

> *(Hier deine fertige Zusammenfassung einfuegen.)*
