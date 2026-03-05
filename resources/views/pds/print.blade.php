<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Personal Data Sheet — CS Form No. 212</title>
<style>
/* =====================================================
   CS FORM 212 (Revised 2017) — Official CSC PDS
   Paper: 8.5" × 13" Legal
   ===================================================== */
* { box-sizing:border-box; margin:0; padding:0; }

body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 7.5pt;
    color: #000;
    background: #fff;
}

@page {
    size: 8.5in 13in portrait;
    margin: 8mm 8mm 8mm 8mm;
}

/* ── Page block ── */
.page {
    width: 100%;
    page-break-after: always;
    page-break-inside: avoid;
}
.page:last-child { page-break-after: auto; }

/* ── All tables ── */
table {
    border-collapse: collapse;
    width: 100%;
    table-layout: fixed;
}
td, th {
    border: 1px solid #000;
    vertical-align: middle;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 7.5pt;
    padding: 1px 3px;
    line-height: 1.2;
    overflow: hidden;
}

/* ── No-border ── */
.nb { border: none !important; }

/* ── Title ── */
.title {
    font-family: 'Arial Black', Arial, sans-serif;
    font-size: 18pt;
    font-weight: 900;
    text-align: center;
    letter-spacing: 1.5px;
}
.cs-label { font-size: 7.5pt; font-style: italic; }

/* ── Section header bar ── */
.sec {
    background: #969696;
    color: #000000;
    font-weight: bold;
    font-size: 8.5pt;
    padding: 3px 6px;
    border: 1px solid #000;
    margin-top: 4px;
    display: block;
}

/* ── Cell types ── */
.lc  { background: #C0C0C0; font-size: 7.5pt; }
.dc  { background: #fff;    font-size: 8pt; }
.hc  { background: #C0C0C0; font-weight: bold; font-size: 7.5pt; text-align: center; }
.num { background: #C0C0C0; font-size: 7.5pt; text-align: right; padding-right: 3px; white-space: nowrap; width: 20px; }

/* ── Sub-label inside cell ── */
.sub { font-size: 6.5pt; display: block; }

/* ── Checkbox ── */
.chkbox {
    display: inline-block;
    width: 9px; height: 9px;
    border: 1px solid #000;
    text-align: center;
    line-height: 8px;
    font-size: 9pt;
    font-weight: bold;
    vertical-align: middle;
    margin-right: 2px;
    background: #fff;
}
.ci { display: inline-block; margin-right: 6px; font-size: 8pt; white-space: nowrap; vertical-align: middle; }

/* ── Note / footer ── */
.note   { font-size: 7.5pt; font-style: italic; text-align: center; padding: 2px 0; }
.footer { font-size: 8pt; font-style: italic; text-align: center; margin-top: 5px; }

/* ── Warning text ── */
.warn { font-size: 7pt; line-height: 1.4; text-align: justify; margin: 1px 0; }

/* ── Sig box ── */
.sigbox {
    border: 1px solid #000;
    text-align: center;
    font-size: 8pt;
    vertical-align: middle;
}
</style>
</head>
<body>
@include('pds.C1')
@include('pds.C2')
@include('pds.C3')
@include('pds.C4')
</body>
</html>
