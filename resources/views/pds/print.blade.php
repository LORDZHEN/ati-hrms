<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Personal Data Sheet — CS Form No. 212 (Revised 2025)</title>
<style>
* { box-sizing:border-box; margin:0; padding:0; }

body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 7pt;
    color: #000;
    background: #fff;
}

@page {
    size: 8.5in 13in portrait;
    margin: 5mm 6mm 5mm 6mm;
}

.page {
    width: 100%;
    page-break-after: always;
    page-break-inside: avoid;
}
.page:last-child { page-break-after: auto; }

table {
    border-collapse: collapse;
    width: 100%;
    table-layout: fixed;
}
td, th {
    border: 1px solid #000;
    vertical-align: middle;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 7pt;
    padding: 1px 2px;
    line-height: 1.2;
    overflow: hidden;
}

.nb { border: none !important; }

.title {
    font-family: 'Arial Black', Arial, sans-serif;
    font-size: 15pt;
    font-weight: 900;
    text-align: center;
    letter-spacing: 1px;
}
.cs-label { font-size: 7pt; font-style: italic; }

.sec {
    background: #969696;
    color: #000;
    font-weight: bold;
    font-size: 7.5pt;
    padding: 2px 5px;
    border: 1px solid #000;
    margin-top: 3px;
    display: block;
}

.lc  { background: #C0C0C0; font-size: 7pt; }
.dc  { background: #fff;    font-size: 7pt; }
.hc  { background: #C0C0C0; font-weight: bold; font-size: 7pt; text-align: center; }
.num { background: #C0C0C0; font-size: 7pt; text-align: right; padding-right: 2px; white-space: nowrap; width: 16px; }

.sub { font-size: 5.5pt; display: block; }

.chkbox {
    display: inline-block;
    width: 8px; height: 8px;
    border: 1px solid #000;
    text-align: center;
    line-height: 7px;
    font-size: 8pt;
    font-weight: bold;
    vertical-align: middle;
    margin-right: 1px;
    background: #fff;
}
.ci { display: inline-block; margin-right: 4px; font-size: 7pt; white-space: nowrap; vertical-align: middle; }

.note   { font-size: 7pt; font-style: italic; text-align: center; padding: 1px 0; }
.footer { font-size: 7pt; font-style: italic; text-align: center; margin-top: 2px; }

.warn { font-size: 6.5pt; line-height: 1.3; text-align: justify; margin: 1px 0; }

.sigbox {
    border: 1px solid #000;
    text-align: center;
    font-size: 7pt;
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
