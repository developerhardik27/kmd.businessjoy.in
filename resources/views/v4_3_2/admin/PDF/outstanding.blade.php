  @php
      if ($gardenNames->count() === 1) {
          $name = $gardenNames[0];
      } else {
          $name = $gardenNames->implode('-');
      }
      function formatINR($amount)
      {
          if (!$amount) {
              return '-';
          }
          $explrestunits = '';
          $amount = round($amount);
          $num = strlen($amount);
          if ($num > 3) {
              $lastthree = substr($amount, -3);
              $restunits = substr($amount, 0, -3);
              $restunits = strlen($restunits) % 2 == 1 ? '0' . $restunits : $restunits;
              $expunit = str_split($restunits, 2);
              foreach ($expunit as $key => $value) {
                  if ($key == 0) {
                      $explrestunits .= (int) $value . ',';
                  } else {
                      $explrestunits .= $value . ',';
                  }
              }
              $formatted = $explrestunits . $lastthree;
          } else {
              $formatted = $amount;
          }
          return $formatted;
      }
  @endphp
  <!DOCTYPE html>
  <html lang="en">

  <head>
      <meta charset="UTF-8">
      <title>{{ config('app.name') }} - {{ $name }} - Outstanding Report </title>
      <style>
          /* Modern reset and base styles */
          @page {
              margin-top: 30px;
              margin-right: 30px;
              margin-bottom: 45px;
              /* space for footer */
              margin-left: 30px;
          }


          body {
              font-family: 'Helvetica', 'Arial', sans-serif;
              font-size: 11px;
              color: #333;
              line-height: 1.4;
              margin: 0;
              padding: 0;
          }

          /* Typography */
          h2 {
              font-size: 18px;
              margin: 0;
              color: #000;
          }

          strong {
              color: #000;
          }

          /* Header Section */
          .header-table {
              width: 100%;
              margin-bottom: 20px;
          }

          .company-info {
              line-height: 1.6;
          }

          .report-title {
              text-align: center;
              font-size: 16px;
              font-weight: bold;
              text-transform: uppercase;
              padding: 10px 0;
              border-top: 1px solid #eee;

              margin: 15px 0;
              background-color: #f9f9f9;
          }

          /* Invoice Container */
          .invoice-box {
              margin-bottom: 35px;
              border: 1px solid #e0e0e0;
              padding: 15px;
              border-radius: 4px;
              page-break-inside: avoid;
          }

          .invoice-header {
              display: table;
              width: 100%;
              background: #f4f7f9;
              padding: 8px;
              font-weight: bold;
              font-size: 15px;
              page-break-inside: avoid;
          }

          .invoice-no,
          .grand-total {
              display: table-cell;
          }

          .grand-total {
              text-align: right;
          }

          .invoice-meta {
              padding: 8px 10px;
              background: #fff;
              font-size: 10px;
              color: #666;
              border-bottom: 1px solid #eee;
          }

          /* Table Styling */
          table.payment-table {
              width: 100%;
              border-collapse: collapse;
              margin-top: 10px;
          }

          table.payment-table th {
              background-color: #fcfcfc;
              color: #555;
              font-weight: bold;
              text-align: left;
              padding: 8px 5px;
              border-bottom: 1px solid #444;
              text-transform: uppercase;
              font-size: 9px;
          }

          table.payment-table td {
              padding: 8px 5px;
              border-bottom: 1px solid #eee;
          }

          .text-right {
              text-align: right;
          }

          .blue-text {
              color: green;
              font-weight: bold;
          }

          .pending-text {
              color: #d9534f;
              font-weight: bold;
          }

          /* Footer */
          footer {
              position: fixed;
              bottom: -30px;
              left: 0;
              right: 0;
              height: 30px;
              font-size: 9px;
              color: #999;
              border-top: 1px solid #eee;
          }

          .clearfix::after {
              content: "";
              clear: both;
              display: table;
          }
      </style>
  </head>

  <body>

      <table class="header-table">
          <tr>
              <td width="60%" class="company-info">
                  <h2>KMD TEA AND AGRO</h2>
                  Room No. 316, 3rd Floor, 32 Ezra Street<br>
                  Corp Address: Room No. 46, Nilhat House, 11 R, Kolkata<br>
                  West Bengal, Code: 19<br>
                  <strong>GSTIN:</strong> 19ABBFK5569Q1ZF | <strong>PAN:</strong> ABBFK5569Q
              </td>
              <td width="40%" align="right" style="vertical-align: top;">
                  Date: {{ date('d-m-Y') }}
              </td>
          </tr>
      </table>


      <div class="report-title">{{ $name }} - Outstanding Report</div>

      @foreach ($list as $invoice)
          <div class="invoice-box">
              <div class="invoice-header clearfix">
                  <span class="invoice-no">Invoice #{{ $invoice['invoice_no'] ?? '-' }}</span>
                  <span class="grand-total">Total Amount: {{ formatINR($invoice['grand_total'] ?? 0)}}</span>
              </div>

              <div class="invoice-meta">
                  <strong>Date:</strong> {{ $invoice['invoice_date'] ?? '-' }} &nbsp;|&nbsp;
                  <strong>IGST:</strong> {{ $invoice['igst'] ?? '-' }} &nbsp;|&nbsp;
                  <strong>Status:</strong> <span
                      style="text-transform: uppercase">{{ $invoice['status'] ?? '-' }}</span>
                  &nbsp;|&nbsp;
                  <strong>Period:</strong> {{ $invoice['from_date'] ?? '-' }} to {{ $invoice['to_date'] ?? '-' }}
              </div>

              <table class="payment-table">
                  <thead>
                      <tr>
                          <th>Date</th>
                          <th>Receipt No.</th>
                          <th>Transaction ID</th>
                          <th>Paid By</th>
                          <th>Type</th>
                          <th>Paid Amt</th>
                          <th class="text-right">Balance</th>
                      </tr>
                  </thead>
                  <tbody>
                      @foreach ($invoice['details'] as $detail)
                          @if ($detail['receipt_number'])
                              <tr>
                                  <td>
                                      {{ $detail['datetime'] ? \Carbon\Carbon::parse($detail['datetime'])->format('d-M-Y') : '-' }}
                                  </td>
                                  <td>{{ $detail['receipt_number'] ?? '-' }}</td>
                                  <td><small>{{ $detail['transaction_id'] ?? '-' }}</small></td>
                                  <td>{{ $detail['paid_by'] ?? '-' }}</td>
                                  <td>{{ $detail['paid_type'] ?? '-' }}</td>
                                  <td class=" blue-text">{{ formatINR($detail['paid_amount'] ?? 0) }}</td>
                                  <td class="text-right pending-text">{{ formatINR($detail['pending_amount'] ?? 0) }}</td>
                              </tr>
                          @else
                              <tr>
                                  <td colspan="7" class="text-center">No payments available</td>
                              </tr>
                          @endif
                      @endforeach
                  </tbody>
              </table>
          </div>
      @endforeach

      <footer>
          <table width="100%">
              <tr>
                  <td align="left">
                      This is a computer-generated document. No signature is required.
                  </td>
                  <td align="right">
                      Printed on: {{ date('d-M-Y, h:i A') }}
                  </td>
              </tr>
          </table>
      </footer>


  </body>

  </html>
