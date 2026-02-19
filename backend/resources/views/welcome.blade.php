<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SchoolHub API</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 0; padding: 40px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 10px; }
        p { color: #666; line-height: 1.6; }
        .version { background: #e8f5e9; color: #2e7d32; padding: 4px 12px; border-radius: 4px; font-size: 14px; display: inline-block; }
        .endpoints { margin-top: 30px; }
        .endpoint { background: #f8f9fa; padding: 15px; border-radius: 4px; margin-bottom: 10px; }
        .method { font-weight: bold; color: #1976d2; }
        .path { color: #333; }
        code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>SchoolHub API</h1>
        <p class="version">Laravel v{{ App::VERSION }}</p>
        <p>Welcome to the SchoolHub School Management System API. This API provides endpoints for managing students, teachers, parents, assessments, results, and more.</p>
        
        <div class="endpoints">
            <h2>Quick Start</h2>
            <div class="endpoint">
                <span class="method">POST</span> <span class="path">/api/auth/register</span>
            </div>
            <div class="endpoint">
                <span class="method">POST</span> <span class="path">/api/auth/login</span>
            </div>
            <div class="endpoint">
                <span class="method">GET</span> <span class="path">/api/health</span>
            </div>
        </div>
        
        <p>For full documentation, please refer to the API documentation.</p>
    </div>
</body>
</html>
