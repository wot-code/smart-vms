<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Security Audit Report</title>
    <style>
        /* PDF engines prefer base fonts like Helvetica or DejaVu Sans */
        body { 
            font-family: 'Helvetica', sans-serif; 
            font-size: 10px; 
            color: #333; 
            line-height: 1.4; 
            margin: 0; 
            padding: 0; 
        }
        
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 2px solid #d63031; 
            padding-bottom: 10px; 
        }
        
        .report-title { 
            font-size: 18px; 
            font-weight: bold; 
            color: #d63031; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
        }
        
        .meta { 
            margin-top: 5px; 
            font-size: 9px; 
            color: #555; 
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed; /* Prevents layout breaking */
        } 
        
        th { 
            background-color: #f1f1f1; 
            color: #222; 
            text-align: left; 
            padding: 8px; 
            border: 1px solid #dee2e6; 
            text-transform: uppercase; 
            font-size: 8px; 
        }
        
        td { 
            padding: 7px; 
            border: 1px solid #dee2e6; 
            vertical-align: top; 
            word-wrap: break-word; /* Critical for long URLs */
        } 
        
        .url-path { 
            font-family: 'Courier', monospace; 
            color: #d63031; 
            font-size: 9px; 
        }
        
        .ip-badge { 
            font-family: 'Courier', monospace; 
            color: #444; 
        }

        .action-label {
            font-weight: bold;
            color: #2d3436;
            text-transform: uppercase;
            font-size: 8px;
        }
        
        /* Zebra stripping */
        tr:nth-child(even) { background-color: #fafafa; }

        .footer { 
            position: fixed; 
            bottom: -30px; 
            left: 0px; 
            right: 0px; 
            height: 50px; 
            text-align: center; 
            font-size: 8px; 
            color: #999; 
            border-top: 1px solid #eee; 
            padding-top: 10px; 
        }
        
        .page-number:before { content: "Page " counter(page); }
    </style>
</head>
<body>

    <div class="header">
        <div class="report-title">Smart VMS Security Audit</div>
        <div class="meta">
            <strong>Incident Log Report</strong><br>
            Generated: {{ $date }}<br>
            Authored by: {{ $admin }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">Timestamp</th>
                <th width="20%">User Account</th>
                <th width="20%">Action</th>
                <th width="30%">Request Path</th>
                <th width="15%">IP Address</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>
                    <strong>{{ $log->created_at->format('d/m/Y') }}</strong><br>
                    <span style="color: #666;">{{ $log->created_at->format('H:i:s') }}</span>
                </td>
                <td>
                    <strong>{{ $log->user->name ?? 'Guest/System' }}</strong><br>
                    <span style="font-size: 8px; color: #777;">{{ $log->user->email ?? 'N/A' }}</span>
                </td>
                <td class="action-label">
                    {{ str_replace('_', ' ', $log->action) }}
                </td>
                <td class="url-path">
                    {{ parse_url($log->url, PHP_URL_PATH) ?: '/' }}
                </td>
                <td class="ip-badge">
                    {{ $log->ip_address }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 40px; color: #999;">
                    No security violations have been recorded in the database.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; text-align: left;">Smart VMS System Audit Report</td>
                <td style="border: none; text-align: right;" class="page-number"></td>
            </tr>
        </table>
    </div>

</body>
</html>