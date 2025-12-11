<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Print Judgment</title>
  <style>
    @page {
      size: A4;
      margin: 25mm 15mm 35mm 15mm; /* bottom margin increased for footer */
    }

    body {
      font-family: 'DejaVu Sans', sans-serif;
      font-size: 12px;
      margin: 0;
      padding: 0;
    }

    .page {
      position: relative;
      page-break-after: always;
      min-height: 100vh;
      margin-bottom: 40px; /* reserve space for footer */
    }

    /* Dompdf footer using table */
    .footer {
      width: 100%;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      border-top: 1px solid #ccc;
      font-size: 11px;
      color: #666;
      background: white;
    }

    .footer td {
      padding: 2px 5px;
    }

    /* Browser print enhancement (optional) */
    @media print {
      .footer {
        position: fixed;
        bottom: 0;
      }

      @page {
        @bottom-left {
          content: "{{ now()->format('d M, Y h:i A') }}";
          font-size: 11px;
          color: #666;
        }

        @bottom-center {
          content: "© {{ date('Y') }} Digital Bangladesh Legal Decisions";
          font-size: 11px;
          color: #666;
        }

        @bottom-right {
          content: "{{ auth('subscriber')->user()->name }} | {{ auth('subscriber')->user()->email }}";
          font-size: 11px;
          color: #666;
        }
      }
    }
  </style>
</head>

<body>

  <div class="page">
    @include('auth.subscribers.profile._legal_search_pdf')
  </div>

  {{-- Footer (Dompdf compatible with table) --}}
  @if(isset($metaData) && $metaData)
  <table class="footer">
    <tr>
      <td style="text-align:left; width:33%;">{{ now()->format('d M, Y h:i A') }}</td>
      <td style="text-align:center; width:34%;">© {{ date('Y') }} Digital Bangladesh Legal Decisions — bldlegalized.com</td>
      <td style="text-align:right; width:33%;">{{ auth('subscriber')->user()->name ?? 'Guest' }} | {{ auth('subscriber')->user()->email ?? '-' }}</td>
    </tr>
  </table>
  @endif

  @if(isset($metaData) && $metaData == false)
  <script>
    window.print();
  </script>
  @endif

</body>

</html>
