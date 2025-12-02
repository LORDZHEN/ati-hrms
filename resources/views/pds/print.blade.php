<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        @page { margin: 15px; }
        .page-break { page-break-after: always; }
    </style>
</head>

<body>

    @include('pds.C1')
    <div class="page-break"></div>

    @include('pds.C2')
    <div class="page-break"></div>

    @include('pds.C3')
    <div class="page-break"></div>

    @include('pds.C4')

</body>
</html>
