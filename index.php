<?php
/* =====================================================================
   Klausur-Archiv – Hauptnavigator
   ---------------------------------------------------------------------
   Scannt bei jedem Aufruf die Ordnerstruktur:
        /<Studienrichtung>/<Semester>/<Kurs>/index.html
   und baut daraus die Navigation. Neue Ordner werden automatisch
   erkannt – einfach Ordner + index.html ablegen, fertig.

   Ein Kurs gilt als "vorhanden", sobald er eine index.html enthaelt.
   Optional: liegt im Kursordner eine meta.json
        { "titel": "...", "beschreibung": "..." }
   werden Titel/Beschreibung daraus uebernommen.

   Design: gleicher Hausstil wie die Kurs-Guides
   (Pergament-Hintergrund, Indigo-Leitfarbe, Space Grotesk / Inter / JetBrains Mono).
   ===================================================================== */

declare(strict_types=1);

$ROOT = __DIR__;
$IGNORE = ['.', '..', 'assets', 'css', 'js', 'img', 'images', 'vendor', '.git', '.github', 'node_modules'];

function listDirs(string $path, array $ignore): array {
    if (!is_dir($path)) return [];
    $items = scandir($path) ?: [];
    $dirs = array_filter($items, function ($name) use ($path, $ignore) {
        if (in_array($name, $ignore, true)) return false;
        if ($name[0] === '.') return false;
        return is_dir($path . DIRECTORY_SEPARATOR . $name);
    });
    $dirs = array_values($dirs);
    natcasesort($dirs);
    return array_values($dirs);
}

function pretty(string $name): string {
    $s = str_replace('_', ' ', $name);
    $s = preg_replace('/(\d)\.(?=\S)/', '$1. ', $s);
    return trim($s);
}

function readMeta(string $kursPath): array {
    $metaFile = $kursPath . DIRECTORY_SEPARATOR . 'meta.json';
    if (is_file($metaFile)) {
        $data = json_decode((string)file_get_contents($metaFile), true);
        if (is_array($data)) return $data;
    }
    return [];
}

/* Rekursiver Scan ab Kurs-Ebene:
   - Ordner mit index.html  -> Seite (direkter Link)
   - Ordner mit Unterordnern -> Zweig (zum Aufklappen), beliebig tief
   - hat ein Ordner beides   -> Zweig + zusaetzliche "Uebersicht" (eigene index.html)
   Gibt null zurueck, wenn der Ordner (auch tief) nichts Anzeigbares enthaelt. */
function scanNode(string $abs, string $urlPrefix, array $ignore): ?array {
    $children = [];
    foreach (listDirs($abs, $ignore) as $child) {
        $node = scanNode($abs . "/$child", $urlPrefix . rawurlencode($child) . '/', $ignore);
        if ($node !== null) $children[] = $node;
    }
    $hasIndex = is_file($abs . '/index.html');
    if (!$children && !$hasIndex) return null;

    $meta = readMeta($abs);
    $node = [
        'name'         => basename($abs),
        'display'      => $meta['titel']        ?? pretty(basename($abs)),
        'beschreibung' => $meta['beschreibung'] ?? '',
    ];
    if ($children) {
        $node['type']     = 'branch';
        $node['children'] = $children;
        if ($hasIndex) $node['pfad'] = $urlPrefix . 'index.html';   // Uebersichtsseite des Zweigs
    } else {
        $node['type'] = 'page';
        $node['pfad'] = $urlPrefix . 'index.html';
    }
    return $node;
}

$tree = [];
foreach (listDirs($ROOT, $IGNORE) as $sr) {
    $srPath = $ROOT . "/$sr";
    $semesterListe = [];
    foreach (listDirs($srPath, $IGNORE) as $sem) {
        $semPath = $srPath . "/$sem";
        $kursListe = [];
        foreach (listDirs($semPath, $IGNORE) as $kurs) {
            $prefix = rawurlencode($sr) . '/' . rawurlencode($sem) . '/' . rawurlencode($kurs) . '/';
            $node = scanNode($semPath . "/$kurs", $prefix, $IGNORE);
            if ($node !== null) $kursListe[] = $node;
        }
        if ($kursListe) {
            $semesterListe[] = ['name' => $sem, 'display' => pretty($sem), 'kurse' => $kursListe];
        }
    }
    if ($semesterListe) {
        $tree[] = ['name' => $sr, 'display' => pretty($sr), 'semester' => $semesterListe];
    }
}

$anzahlKurse = 0;
foreach ($tree as $sr) foreach ($sr['semester'] as $sem) $anzahlKurse += count($sem['kurse']);
$anzahlRichtungen = count($tree);
$treeJson = json_encode($tree, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Klausur-Archiv</title>
<link rel="icon" href="favicon.svg" type="image/svg+xml">
<link rel="icon" href="favicon.ico" sizes="any">
<link rel="apple-touch-icon" href="apple-touch-icon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<style>
  :root{
    --ink:#191d2b; --ink-soft:#454b5e; --paper:#fbfaf6; --line:#e4e0d6;
    --indigo:#3a4cf0; --indigo-bg:#eef0ff; --indigo-edge:#c5ccff;
    --amber:#e08a1e; --green:#179a5c;
    --radius:14px; --maxw:980px;
  }
  *{box-sizing:border-box;margin:0;padding:0}
  html{-webkit-text-size-adjust:100%}
  body{
    background:var(--paper); color:var(--ink);
    font-family:'Inter',system-ui,sans-serif; font-size:16px; line-height:1.6;
    -webkit-font-smoothing:antialiased;
    min-height:100vh; display:flex; flex-direction:column;
  }
  a{color:inherit;text-decoration:none}

  /* ---- Toolbar / Pfad ---- */
  .toolbar{
    position:sticky; top:0; z-index:50;
    display:flex; align-items:center; gap:16px; flex-wrap:wrap;
    background:rgba(251,250,246,.88); backdrop-filter:blur(8px);
    border-bottom:1px solid var(--line);
    padding:12px clamp(16px,4vw,28px);
  }
  .brand{ font-family:'JetBrains Mono',monospace; font-size:12px; letter-spacing:.14em; text-transform:uppercase; color:var(--ink-soft); white-space:nowrap; }
  .crumbs{ font-family:'JetBrains Mono',monospace; font-size:12px; letter-spacing:.04em; display:flex; align-items:center; gap:.1rem; flex-wrap:wrap; margin-left:auto; }
  .crumbs button{ font:inherit; color:var(--ink-soft); background:none; border:0; cursor:pointer; padding:.2rem .4rem; border-radius:6px; }
  .crumbs button:hover{ color:var(--indigo); background:var(--indigo-bg); }
  .crumbs .sep{ color:var(--line); }
  .crumbs .here{ color:var(--indigo); padding:.2rem .4rem; }
  .vorlage-btn{ display:inline-flex; align-items:center; gap:7px; font-family:'JetBrains Mono',monospace; font-size:12px; letter-spacing:.06em; text-transform:uppercase; background:var(--ink); color:#fff; border-radius:8px; padding:8px 14px; transition:transform .12s ease, opacity .12s; }
  .vorlage-btn:hover{ transform:translateY(-1px); opacity:.9; }
  .vorlage-btn:focus-visible{ outline:3px solid var(--indigo); outline-offset:2px; }
  .vorlage-btn svg{ stroke:#fff; }

  main{ flex:1; width:100%; max-width:var(--maxw); margin:0 auto; padding:clamp(34px,5vw,56px) clamp(16px,4vw,28px); }

  .eyebrow{ font-family:'JetBrains Mono',monospace; font-size:12.5px; letter-spacing:.22em; text-transform:uppercase; color:var(--indigo); margin:0 0 14px; }
  h1{ font-family:'Space Grotesk',sans-serif; font-weight:700; font-size:clamp(38px,6.5vw,68px); line-height:.99; letter-spacing:-.02em; }
  h1 .sub{ display:block; color:var(--indigo); }
  .tag{ color:var(--ink-soft); font-size:18px; max-width:54ch; margin:16px 0 0; }
  .tag strong{ color:var(--ink); }

  .search{ position:relative; margin-top:26px; max-width:30rem; }
  .search input{ width:100%; font:inherit; color:var(--ink); background:#fff; border:1px solid var(--line); border-radius:999px; padding:.72rem 1rem .72rem 2.7rem; }
  .search input::placeholder{ color:var(--ink-soft); opacity:.7; }
  .search input:focus{ outline:3px solid var(--indigo); outline-offset:1px; border-color:transparent; }
  .search svg{ position:absolute; left:1rem; top:50%; transform:translateY(-50%); width:1.05rem; height:1.05rem; stroke:var(--ink-soft); }

  /* ---- Stufen ---- */
  .stage{ margin-top:38px; animation:rise .32s ease both; }
  .stage-label{ font-family:'JetBrains Mono',monospace; font-size:11.5px; letter-spacing:.18em; text-transform:uppercase; color:var(--indigo); margin-bottom:14px; }
  .grid{ display:grid; gap:14px; grid-template-columns:repeat(auto-fill,minmax(230px,1fr)); }

  .card{
    position:relative; display:block; text-align:left; cursor:pointer;
    background:#fff; border:1px solid var(--line); border-radius:var(--radius);
    padding:18px 20px; font:inherit; color:var(--ink);
    overflow:hidden; transition:border-color .16s, transform .16s, box-shadow .16s;
  }
  /* Indigo-Spine als Signatur (Echo der "blauen Linie" im Guide) */
  .card::before{ content:""; position:absolute; left:0; top:0; bottom:0; width:3px; background:var(--indigo); transform:scaleY(0); transform-origin:top; transition:transform .2s ease; }
  .card:hover{ border-color:var(--indigo-edge); transform:translateY(-2px); box-shadow:0 6px 22px -14px rgba(58,76,240,.5); }
  .card:hover::before{ transform:scaleY(1); }
  .card:focus-visible{ outline:3px solid var(--indigo); outline-offset:2px; }
  .card .title{ font-family:'Space Grotesk',sans-serif; font-weight:700; font-size:1.12rem; letter-spacing:-.01em; }
  .card .meta{ display:block; margin-top:8px; font-family:'JetBrains Mono',monospace; font-size:11px; letter-spacing:.06em; text-transform:uppercase; color:var(--ink-soft); }
  .card .desc{ display:block; margin-top:9px; font-size:.92rem; color:var(--ink-soft); line-height:1.5; }
  .card .arrow{ position:absolute; top:18px; right:18px; color:var(--line); transition:color .16s, transform .16s; }
  .card:hover .arrow{ color:var(--indigo); transform:translateX(3px); }
  .card .fic{ color:var(--indigo); vertical-align:-3px; margin-right:8px; }

  .empty{ color:var(--ink-soft); background:#fff; border:1px dashed var(--line); border-radius:var(--radius); padding:26px; text-align:center; }
  .empty code{ font-family:'JetBrains Mono',monospace; color:var(--indigo); font-size:.85rem; }

  footer{ border-top:1px solid var(--line); color:var(--ink-soft); font-family:'JetBrains Mono',monospace; font-size:11.5px; letter-spacing:.08em; text-transform:uppercase; padding:18px clamp(16px,4vw,28px); }
  footer code{ text-transform:none; letter-spacing:0; color:var(--indigo); }

  @keyframes rise{ from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:none} }
  @media (max-width:720px){ body{font-size:15.5px} .crumbs{margin-left:0; width:100%} }
  @media (prefers-reduced-motion:reduce){ *{animation:none!important;transition:none!important} }
</style>
</head>
<body>
  <header class="toolbar">
    <span class="brand">Klausur · Archiv</span>
    <a class="vorlage-btn" href="Vorlage.md" download title="Vorlage für neue Zusammenfassungen herunterladen">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14"/></svg>
      Vorlage
    </a>
    <nav class="crumbs" id="crumbs" aria-label="Pfad"></nav>
  </header>

  <main>
    <p class="eyebrow">Studienarchiv · von Studis für Studis</p>
    <h1>Finde die<span class="sub">richtige Zusammenfassung.</span></h1>
    <p class="tag">Erst Studienrichtung, dann Semester, dann Kurs. Aktuell
      <strong><?= $anzahlKurse ?></strong> Kurse in
      <strong><?= $anzahlRichtungen ?></strong> Richtungen.</p>

    <div class="search">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
      <input id="search" type="search" placeholder="Kurs suchen … z. B. Analysis" autocomplete="off" aria-label="Kurs suchen">
    </div>

    <div id="picker"></div>
  </main>

  <footer>
    Neuen Kurs hinzufügen: Ordner <code>/Studienrichtung/Semester/Kurs/</code> anlegen und eine <code>index.html</code> ablegen — erscheint automatisch. Enthält ein Kurs statt einer <code>index.html</code> Unterordner, wird dort weiter navigiert (beliebig tief).
  </footer>

<script>
const TREE = <?= $treeJson ?>;
const $crumbs = document.getElementById('crumbs');
const $picker = document.getElementById('picker');
const $search = document.getElementById('search');
let sel = { sr:null, sem:null, stack:[] };  // stack = aufgeklappte Unter-Zweige unter dem Semester

function el(tag, cls, html){ const e=document.createElement(tag); if(cls)e.className=cls; if(html!=null)e.innerHTML=html; return e; }
const arrow = '<svg class="arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
const folderIcon = '<svg class="fic" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>';

function renderCrumbs(){
  $crumbs.innerHTML='';
  const segs=[{label:'~/archiv', go:()=>{sel={sr:null,sem:null,stack:[]};}}];
  if(sel.sr)  segs.push({label:sel.sr.display,  go:()=>{sel.sem=null; sel.stack=[];}});
  if(sel.sem) segs.push({label:sel.sem.display, go:()=>{sel.stack=[];}});
  sel.stack.forEach((node,i)=>segs.push({label:node.display, go:()=>{sel.stack=sel.stack.slice(0,i+1);}}));
  segs.forEach((s,i)=>{
    if(i) $crumbs.appendChild(el('span','sep',' / '));
    if(i===segs.length-1){ $crumbs.appendChild(el('span','here',s.label)); }
    else { const b=el('button',null,s.label); b.onclick=()=>{s.go();render();}; $crumbs.appendChild(b); }
  });
}

function stage(labelText){ const s=el('section','stage'); s.appendChild(el('div','stage-label',labelText)); const g=el('div','grid'); s.appendChild(g); return {section:s,grid:g}; }

function makeBtn({title,meta}){ const c=el('button','card'); c.innerHTML='<span class="title">'+title+'</span>'+(meta?'<span class="meta">'+meta+'</span>':'')+arrow; return c; }
function makeLink({href,title,meta,desc}){ const a=document.createElement('a'); a.className='card'; a.href=href; a.innerHTML='<span class="title">'+title+'</span>'+(desc?'<span class="desc">'+desc+'</span>':'')+(meta?'<span class="meta">'+meta+'</span>':'')+arrow; return a; }
function makeBranch(node){
  const n=node.children.length;
  const c=el('button','card branch');
  c.innerHTML='<span class="title">'+folderIcon+node.display+'</span>'+(node.beschreibung?'<span class="desc">'+node.beschreibung+'</span>':'')+'<span class="meta">'+n+(n===1?' Eintrag':' Einträge')+'</span>'+arrow;
  c.onclick=()=>{ sel.stack.push(node); render(); };
  return c;
}
function appendNode(grid,node){
  if(node.type==='branch') grid.appendChild(makeBranch(node));
  else grid.appendChild(makeLink({href:node.pfad,title:node.display,desc:node.beschreibung,meta:'Zusammenfassung öffnen'}));
}

function render(){
  renderCrumbs(); $picker.innerHTML='';
  if(!sel.sr){
    if(!TREE.length){ $picker.appendChild(el('div','empty','Noch keine Kurse gefunden. Lege einen Ordner <code>/Studienrichtung/Semester/Kurs/index.html</code> an.')); return; }
    const {section,grid}=stage('Schritt 1 — Studienrichtung');
    TREE.forEach(sr=>{ const n=sr.semester.reduce((a,s)=>a+s.kurse.length,0); const c=makeBtn({title:sr.display,meta:n+' Kurse · '+sr.semester.length+' Semester'}); c.onclick=()=>{sel.sr=sr;sel.sem=null;sel.stack=[];render();}; grid.appendChild(c); });
    $picker.appendChild(section); return;
  }
  if(!sel.sem){
    const {section,grid}=stage('Schritt 2 — Semester · '+sel.sr.display);
    sel.sr.semester.forEach(sem=>{ const c=makeBtn({title:sem.display,meta:sem.kurse.length+' Kurse'}); c.onclick=()=>{sel.sem=sem;sel.stack=[];render();}; grid.appendChild(c); });
    $picker.appendChild(section); return;
  }
  // Schritt 3 und tiefer: Kurse, dann beliebig tiefe Unter-Zweige
  const inBranch = sel.stack.length>0;
  const top = inBranch ? sel.stack[sel.stack.length-1] : null;
  const liste = inBranch ? top.children : sel.sem.kurse;
  const label = inBranch ? ('Auswahl · '+top.display) : ('Schritt 3 — Kurs · '+sel.sr.display+' / '+sel.sem.display);
  const {section,grid}=stage(label);
  if(inBranch && top.pfad){ grid.appendChild(makeLink({href:top.pfad,title:'Übersicht',meta:'Seite zu '+top.display})); }
  liste.forEach(node=>appendNode(grid,node));
  $picker.appendChild(section);
}

function collectPages(nodes, trail, out){
  nodes.forEach(n=>{
    const t=trail.concat([n.display]);
    if(n.type==='page'){ out.push({pfad:n.pfad, name:n.name, display:n.display, trail:t}); }
    else { if(n.pfad) out.push({pfad:n.pfad, name:n.name, display:n.display+' · Übersicht', trail:t}); collectPages(n.children, t, out); }
  });
}

function renderSearch(q){
  $picker.innerHTML=''; $crumbs.innerHTML='';
  const home=el('button',null,'~/archiv'); home.onclick=()=>{$search.value='';sel={sr:null,sem:null,stack:[]};render();}; $crumbs.appendChild(home);
  $crumbs.appendChild(el('span','sep',' / ')); $crumbs.appendChild(el('span','here','Suche'));
  const treffer=[];
  TREE.forEach(sr=>sr.semester.forEach(sem=>{
    const pages=[]; collectPages(sem.kurse, [sr.display, sem.display], pages);
    pages.forEach(p=>{ if((p.display+' '+p.name).toLowerCase().includes(q)) treffer.push(p); });
  }));
  const {section,grid}=stage(treffer.length+' Treffer für „'+q+'“');
  if(!treffer.length){ $picker.appendChild(el('div','empty','Kein Kurs gefunden.')); return; }
  treffer.forEach(p=>{ grid.appendChild(makeLink({href:p.pfad,title:p.display,meta:p.trail.slice(0,-1).join(' / ')})); });
  $picker.appendChild(section);
}

$search.addEventListener('input',e=>{ const q=e.target.value.trim().toLowerCase(); if(q.length) renderSearch(q); else render(); });
render();
</script>
</body>
</html>
