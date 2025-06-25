<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SACCO Analytics Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #6b7280;
            font-size: 14px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .metric-row {
            display: table-row;
        }
        .metric-cell {
            display: table-cell;
            padding: 10px;
            border: 1px solid #e5e7eb;
            text-align: center;
        }
        .metric-header {
            background-color: #f9fafb;
            font-weight: bold;
        }
        .metric-value {
            font-size: 20px;
            font-weight: bold;
            color: #059669;
        }
        .metric-change {
            font-size: 14px;
        }
        .positive { color: #059669; }
        .negative { color: #dc2626; }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th,
        .data-table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #f9fafb;
            font-weight: bold;
        }
        .footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
            margin-top: 40px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
        .two-column {
            display: table;
            width: 100%;
        }
        .column {
            display: table-cell;
            width: 50%;
            padding-right: 20px;
            vertical-align: top;
        }
        .column:last-child {
            padding-right: 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">SACCO Analytics Report</div>
        <div class="subtitle">
            Period: {{ ucfirst($period) }} | Generated: {{ $generated_at->format('F j, Y \a\t g:i A') }}
        </div>
    </div>

    <!-- Overview Metrics -->
    <div class="section">
        <div class="section-title">Key Performance Indicators</div>
        <div class="metrics-grid">
            <div class="metric-row">
                <div class="metric-cell metric-header">Active Members</div>
                <div class="metric-cell metric-header">Total Assets</div>
                <div class="metric-cell metric-header">Active Loans</div>
                <div class="metric-cell metric-header">Portfolio Performance</div>
            </div>
            <div class="metric-row">
                <div class="metric-cell">
                    <div class="metric-value">{{ number_format($overview['active_members']['value']) }}</div>
                    <div class="metric-change {{ $overview['active_members']['trend'] == 'up' ? 'positive' : 'negative' }}">
                        {{ $overview['active_members']['trend'] == 'up' ? '+' : '' }}{{ $overview['active_members']['change'] }}%
                    </div>
                </div>
                <div class="metric-cell">
                    <div class="metric-value">KSh {{ number_format($overview['total_assets']['value'], 2) }}</div>
                    <div class="metric-change {{ $overview['total_assets']['trend'] == 'up' ? 'positive' : 'negative' }}">
                        {{ $overview['total_assets']['trend'] == 'up' ? '+' : '' }}{{ $overview['total_assets']['change'] }}%
                    </div>
                </div>
                <div class="metric-cell">
                    <div class="metric-value">{{ number_format($overview['active_loans']['value']) }}</div>
                    <div class="metric-change {{ $overview['active_loans']['trend'] == 'up' ? 'positive' : 'negative' }}">
                        {{ $overview['active_loans']['trend'] == 'up' ? '+' : '' }}{{ $overview['active_loans']['change'] }}%
                    </div>
                </div>
                <div class="metric-cell">
                    <div class="metric-value">{{ $overview['portfolio_performance']['value'] }}%</div>
                    <div class="metric-change {{ $overview['portfolio_performance']['trend'] == 'up' ? 'positive' : 'negative' }}">
                        {{ $overview['portfolio_performance']['trend'] == 'up' ? '+' : '' }}{{ $overview['portfolio_performance']['change'] }}%
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Analytics -->
    <div class="section">
        <div class="section-title">Financial Analytics</div>
        <div class="two-column">
            <div class="column">
                <h4>Loan Portfolio Analysis</h4>
                <table class="data-table">
                    <tr>
                        <th>Metric</th>
                        <th>Amount (KSh)</th>
                    </tr>
                    <tr>
                        <td>Total Disbursed</td>
                        <td>{{ number_format($financial['loan_portfolio']['total_disbursed'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Active Loans</td>
                        <td>{{ number_format($financial['loan_portfolio']['active_loans'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Completed Loans</td>
                        <td>{{ number_format($financial['loan_portfolio']['completed_loans'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Defaulted Loans</td>
                        <td>{{ number_format($financial['loan_portfolio']['defaulted_loans'], 2) }}</td>
                    </tr>
                </table>
            </div>
            <div class="column">
                <h4>Revenue Streams</h4>
                <table class="data-table">
                    <tr>
                        <th>Source</th>
                        <th>Amount (KSh)</th>
                    </tr>
                    <tr>
                        <td>Loan Interest</td>
                        <td>{{ number_format($financial['revenue_streams']['loan_interest'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Service Fees</td>
                        <td>{{ number_format($financial['revenue_streams']['fees'], 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total Revenue</strong></td>
                        <td><strong>{{ number_format($financial['revenue_streams']['loan_interest'] + $financial['revenue_streams']['fees'], 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Account Distribution -->
    <div class="section">
        <div class="section-title">Account Distribution</div>
        <table class="data-table">
            <tr>
                <th>Account Type</th>
                <th>Number of Accounts</th>
                <th>Total Balance (KSh)</th>
            </tr>
            @foreach($financial['account_distribution'] as $account)
            <tr>
                <td>{{ ucfirst(str_replace('_', ' ', $account->account_type)) }}</td>
                <td>{{ number_format($account->count) }}</td>
                <td>{{ number_format($account->total_balance, 2) }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <!-- Member Analytics -->
    <div class="section">
        <div class="section-title">Member Analytics</div>
        <div class="two-column">
            <div class="column">
                <h4>Member Engagement</h4>
                <table class="data-table">
                    <tr>
                        <th>Metric</th>
                        <th>Count</th>
                    </tr>
                    <tr>
                        <td>Active Savers</td>
                        <td>{{ number_format($members['engagement']['active_savers']) }}</td>
                    </tr>
                    <tr>
                        <td>Loan Members</td>
                        <td>{{ number_format($members['engagement']['loan_members']) }}</td>
                    </tr>
                    <tr>
                        <td>Budget Users</td>
                        <td>{{ number_format($members['engagement']['budget_users']) }}</td>
                    </tr>
                    <tr>
                        <td>Goal Setters</td>
                        <td>{{ number_format($members['engagement']['goal_setters']) }}</td>
                    </tr>
                </table>
            </div>
            <div class="column">
                <h4>Member Status Distribution</h4>
                <table class="data-table">
                    <tr>
                        <th>Status</th>
                        <th>Count</th>
                    </tr>
                    @foreach($members['demographics']['by_status'] as $status)
                    <tr>
                        <td>{{ ucfirst($status->membership_status ?? 'Unknown') }}</td>
                        <td>{{ number_format($status->count) }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>

    <!-- Operational Analytics -->
    <div class="section">
        <div class="section-title">Operational Analytics</div>
        <div class="two-column">
            <div class="column">
                <h4>Processing Efficiency</h4>
                <table class="data-table">
                    <tr>
                        <th>Metric</th>
                        <th>Value</th>
                    </tr>
                    <tr>
                        <td>Success Rate</td>
                        <td>{{ number_format($operations['efficiency']['success_rate'], 1) }}%</td>
                    </tr>
                    <tr>
                        <td>Avg Processing Time</td>
                        <td>{{ number_format($operations['efficiency']['avg_processing_time'] ?? 0, 1) }} minutes</td>
                    </tr>
                </table>
            </div>
            <div class="column">
                <h4>Service Utilization</h4>
                <table class="data-table">
                    <tr>
                        <th>Service</th>
                        <th>Usage Count</th>
                    </tr>
                    <tr>
                        <td>Loan Applications</td>
                        <td>{{ number_format($operations['service_utilization']['loan_applications']) }}</td>
                    </tr>
                    <tr>
                        <td>Account Openings</td>
                        <td>{{ number_format($operations['service_utilization']['account_openings']) }}</td>
                    </tr>
                    <tr>
                        <td>Budget Creations</td>
                        <td>{{ number_format($operations['service_utilization']['budget_creations']) }}</td>
                    </tr>
                    <tr>
                        <td>Goal Settings</td>
                        <td>{{ number_format($operations['service_utilization']['goal_settings']) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This report was generated automatically by the SACCO Analytics System</p>
        <p>For questions or support, please contact your system administrator</p>
    </div>
</body>
</html> 