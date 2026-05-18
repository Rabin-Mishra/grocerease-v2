<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to eSewa...</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background-color: #f8f9fa; }
        .container { text-align: center; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .spinner { border: 4px solid rgba(0,0,0,0.1); width: 40px; height: 40px; border-radius: 50%; border-left-color: #61b15a; animation: spin 1s linear infinite; margin: 0 auto 20px; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        button { background-color: #61b15a; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; margin-top: 15px; font-weight: bold; }
        button:hover { background-color: #4a9144; }
    </style>
</head>
<body>
    <div class="container">
        <div class="spinner"></div>
        <h2>Redirecting to eSewa...</h2>
        <p>Please wait while we transfer you to the secure payment portal.</p>
        
        <form id="esewa-form" action="{{ $formData['url'] }}" method="POST">
            @foreach($formData as $key => $value)
                @if($key !== 'url')
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach
            <button type="submit" id="manual-btn" style="display: none;">Click here if not redirected</button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                document.getElementById('esewa-form').submit();
            }, 1000);
            
            setTimeout(function() {
                document.getElementById('manual-btn').style.display = 'inline-block';
            }, 3000);
        });
    </script>
</body>
</html>
