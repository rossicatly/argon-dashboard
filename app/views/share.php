<!DOCTYPE html>
<html>
<head>
    <title>Share File</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Share File</h1>
        <form method="post">
            <div class="form-group">
                <label for="password">Password (optional)</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <div class="form-group">
                <label for="expires_at">Expiration Date (optional)</label>
                <input type="datetime-local" name="expires_at" id="expires_at" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Generate Link</button>
        </form>
    </div>
</body>
</html>
