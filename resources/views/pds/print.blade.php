<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Personal Data Sheet</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        @page {
            margin: 15px;
        }

        .table-bordered {
            width: 100%;
            border-collapse: collapse;
        }

        .table-bordered td,
        .table-bordered th {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
        }

        .page-break {
            page-break-after: always;
        }

        .page-break:last-child {
            page-break-after: auto;
        }

        .checkbox {
            display: inline-block;
            width: 12px;
        }
    </style>
</head>

<body>

    @include('pds.C1')
    @include('pds.C2')
    @include('pds.C3')
    @include('pds.C4')

    {{-- ADMIN REMARKS (if any) --}}
    @if(!empty($pds->remarks))
        <div class="page-break">
            <h4>ADMIN REMARKS</h4>
            <p>{{ $pds->remarks }}</p>
        </div>
    @endif

</body>
</html>
