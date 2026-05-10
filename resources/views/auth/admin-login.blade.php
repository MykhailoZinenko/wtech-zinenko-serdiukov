<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login — White Wolf Emporium</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Crimson+Text:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

    @vite(['resources/css/admin.css'])
</head>
<body class="admin-login-body">

    <div class="login-card">
        <div class="login-card__top">
            <div class="login-card__icon"><i class="bi bi-shield-lock-fill"></i></div>
            <div class="login-card__title">Admin Access</div>
            <div class="login-card__sub">White Wolf Emporium — Control Panel</div>
        </div>

        <div class="login-card__body">
            @if ($errors->any())
                <div style="margin-bottom: 18px; padding: 12px 14px; border: 1px solid rgba(255, 80, 80, 0.35); border-radius: 12px; background: rgba(120, 20, 20, 0.18); color: #ffd7d7;">
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="post">
                @csrf

                <div class="form-field">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="admin@whitewolf.nv"
                        autocomplete="username"
                        value="{{ old('email') }}"
                        required
                    />
                </div>

                <div class="form-field">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                    />
                </div>

                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;margin-top:2px;line-height:1;">
                    <label class="form-check-wwe" style="margin:0;line-height:1;font-size:.82rem;">
                        <input type="checkbox" name="remember" />
                        Remember me
                    </label>

                    <a
                        href="#"
                        style="font-size:.82rem;line-height:1;color:var(--clr-text-dim);text-decoration:none;transition:color var(--transition);"
                        onmouseover="this.style.color='var(--clr-gold)'"
                        onmouseout="this.style.color='var(--clr-text-dim)'"
                    >
                        Forgot password?
                    </a>
                </div>

                <button type="submit" class="btn-base btn-gold">
                    <i class="bi bi-box-arrow-in-right"></i> Sign In
                </button>
            </form>

            <a href="{{ route('login') }}" class="login-back">
                <i class="bi bi-arrow-left"></i> Back to user login
            </a>
        </div>
    </div>

</body>
</html>