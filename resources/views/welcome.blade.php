<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>eSacco API</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 40px;
            background: #f8fafc;
            color: #1e293b;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2563eb;
            margin-bottom: 20px;
        }
        .api-info {
            background: #f1f5f9;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .endpoint {
            background: #e0f2fe;
            padding: 15px;
            margin: 10px 0;
            border-radius: 6px;
            border-left: 4px solid #0284c7;
        }
        .method {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-right: 10px;
        }
        .get { background: #dcfce7; color: #166534; }
        .post { background: #fef3c7; color: #92400e; }
        code {
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Monaco', 'Menlo', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏦 eSacco API</h1>
        
        <div class="api-info">
            <h3>API Status: <span style="color: #059669;">✅ Active</span></h3>
            <p>Welcome to the eSacco Management System API. This is a clean Laravel backend-only setup.</p>
        </div>

        <h2>Available Endpoints</h2>
        
        <div class="endpoint">
            <span class="method get">GET</span>
            <code>/api/dashboard</code> - Dashboard data
        </div>
        
        <div class="endpoint">
            <span class="method get">GET</span>
            <code>/api/transactions</code> - List transactions
        </div>
        
        <div class="endpoint">
            <span class="method get">GET</span>
            <code>/api/transactions/{id}</code> - Get transaction details
        </div>
        
        <div class="endpoint">
            <span class="method post">POST</span>
            <code>/api/transactions</code> - Create transaction
        </div>
        
        <div class="endpoint">
            <span class="method post">POST</span>
            <code>/api/transactions/{id}/approve</code> - Approve transaction
        </div>
        
        <div class="endpoint">
            <span class="method post">POST</span>
            <code>/api/transactions/{id}/reject</code> - Reject transaction
        </div>

        <h2>Authentication</h2>
        <p>All API endpoints require authentication using Laravel Sanctum. Include the token in the Authorization header:</p>
        <code>Authorization: Bearer {your-token}</code>

        <h2>Next Steps</h2>
        <p>You can now implement your frontend using any technology you prefer:</p>
        <ul>
            <li>React/Vue/Angular SPA</li>
            <li>Mobile app (React Native, Flutter, etc.)</li>
            <li>Desktop application</li>
            <li>Or any other frontend framework</li>
        </ul>
    </div>
</body>
</html>


