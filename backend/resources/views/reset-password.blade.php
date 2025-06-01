<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h1>Reset Your Password</h1>
    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" value="{{ request('email') }}" required>
        </div>
        <div>
            <label for="password">New Password:</label>
            <input type="password" name="password" required minlength="7" oninput="checkPasswordLength(this)">
            <span id="password-error" style="color: red; display: none;">Password must be at least 7 characters long.</span>
        </div>
        <div>
            <label for="password_confirmation">Confirm Password:</label>
            <input type="password" name="password_confirmation" required minlength="7" oninput="checkPasswordLength(this)">
        </div>
        <button type="submit">Reset Password</button>
    </form>

    <script>
        function checkPasswordLength(input) {
            const errorSpan = document.getElementById('password-error');
            if (input.value.length < 7) {
                errorSpan.style.display = 'inline';
            } else {
                errorSpan.style.display = 'none';
            }
        }
    </script>
</body>
</html> 