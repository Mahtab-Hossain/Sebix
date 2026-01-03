<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', static function () {
	$html = <<<'HTML'
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sebix.xyz — Home</title>
<style>
  body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;margin:0;padding:40px;color:#222;background:#f7f8fb}
  .wrap{max-width:900px;margin:0 auto;text-align:center}
  .card{background:#fff;border:1px solid #e6e9ef;border-radius:10px;padding:28px;margin-top:24px;box-shadow:0 6px 18px rgba(34,36,38,0.06)}
  h1{margin:0 0 8px;font-size:34px}
  p.lead{color:#555;margin:0 0 18px}
  .actions a{display:inline-block;margin:6px;padding:10px 16px;border-radius:6px;text-decoration:none;color:#fff;background:#ff6b3d}
  .actions a.secondary{background:#4a90e2}
</style>
</head>
<body>
  <div class="wrap">
    <h1>Sebix.xyz</h1>
    <p class="lead">Find and book local service providers quickly — plumbing, electrical, cleaning and more.</p>
    <div class="card">
      <h2>Get started</h2>
      <p>Sign up to request services or create a provider profile to get bookings.</p>
      <p class="actions">
        <a href="/auth/register">Sign up</a>
        <a class="secondary" href="/auth/login">Log in</a>
      </p>
    </div>
  </div>
</body>
</html>
HTML;
	return $html;
});

// redesigned register page: saves token and redirects to /dashboard
$routes->get('auth/register', static function () {
	$html = <<<'HTML'
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Register — Sebix.xyz</title>
<style>
  :root{--bg:#f3f6fb;--card:#ffffff;--accent:#0077cc;--muted:#6b7280}
  body{margin:0;font-family:Inter,system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif;background:var(--bg);color:#111}
  .container{max-width:920px;margin:40px auto;padding:20px}
  .grid{display:grid;grid-template-columns:1fr 420px;gap:28px;align-items:center}
  .hero{padding:28px}
  h1{margin:0 0 8px;font-size:28px}
  p.lead{margin:0;color:var(--muted)}
  .card{background:var(--card);border-radius:12px;padding:20px;box-shadow:0 8px 28px rgba(3,12,26,0.06)}
  input,select,button{width:100%;padding:10px;border-radius:8px;border:1px solid #e6eef8;font-size:14px}
  label{display:block;margin-bottom:6px;font-weight:600;color:#222}
  .field{margin-bottom:12px}
  button{background:var(--accent);color:#fff;border:none;cursor:pointer}
  .small{font-size:13px;color:var(--muted)}
  pre#out{background:#0b1220;color:#dbeafe;padding:10px;border-radius:8px;overflow:auto;max-height:180px}
  @media(max-width:880px){.grid{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="container">
  <div class="grid">
    <div class="hero">
      <h1>Create your account</h1>
      <p class="lead">Sign up as a customer, service provider, or admin. After registration you'll be taken to your dashboard.</p>
      <div class="card" style="margin-top:18px;">
        <form id="registerForm" autocomplete="off">
          <div class="field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required />
          </div>
          <div class="field">
            <label for="name">Full name</label>
            <input id="name" name="name" required />
          </div>
          <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required />
          </div>
          <div class="field">
            <label for="phone">Phone</label>
            <input id="phone" name="phone" />
          </div>
          <div class="field">
            <label for="location">Location</label>
            <input id="location" name="location" placeholder="City / Address" />
          </div>
          <div class="field">
            <label for="role">Role</label>
            <select id="role" name="role" required>
              <option value="end_user">End User</option>
              <option value="service_provider">Service Provider</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div class="field">
            <button type="submit">Create account</button>
          </div>
          <div class="small">Already registered? <a href="/auth/login">Log in</a></div>
        </form>
        <div style="margin-top:12px"><pre id="out" style="display:none"></pre></div>
      </div>
    </div>

    <div>
      <div class="card">
        <h3 style="margin-top:0">Why Sebix</h3>
        <p class="small">Quick bookings, vetted providers, in-app scheduling and history. Start by creating your account.</p>
        <hr />
        <ul class="small">
          <li>Role-based access</li>
          <li>Provider discovery by category & location</li>
          <li>Simple booking flow</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<script>
const out = document.getElementById('out');
document.getElementById('registerForm').addEventListener('submit', async function (e) {
  e.preventDefault();
  out.style.display = 'block';
  out.textContent = 'Sending...';
  const f = e.target;
  const payload = {
    email: f.email.value.trim(),
    name: f.name.value.trim(),
    password: f.password.value,
    phone: f.phone.value.trim() || null,
    location: f.location.value.trim() || null,
    role: f.role.value
  };

  try {
    const res = await fetch('/auth/register', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(payload)
    });
    const json = await res.json().catch(()=>null);
    if (res.status === 201) {
      // save user info and token to localStorage and go to dashboard
      const user = {
        id: json.id,
        email: json.email,
        role: json.role,
        api_token: json.api_token
      };
      localStorage.setItem('sebix_user', JSON.stringify(user));
      // short delay to show success then redirect
      out.textContent = 'Registration successful. Redirecting to dashboard...';
      setTimeout(()=> window.location.href = '/dashboard', 700);
      return;
    }

    // show validation / error messages
    out.textContent = JSON.stringify({status: res.status, body: json || {}}, null, 2);
  } catch (err) {
    out.textContent = 'Network error: ' + err;
  }
});
</script>
</body>
</html>
HTML;
	return $html;
});

// simple dashboard that reads client-side storage
$routes->get('dashboard', static function () {
	$html = <<<'HTML'
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Dashboard — Sebix</title>
<style>body{font-family:system-ui,Arial;padding:28px;background:#f7f8fb;color:#111} .card{background:#fff;padding:20px;border-radius:10px;max-width:820px;margin:20px auto;box-shadow:0 8px 24px rgba(2,6,23,0.06)} button{padding:8px 12px;background:#e53e3e;color:#fff;border:0;border-radius:6px;cursor:pointer}</style>
</head><body>
<div class="card">
  <h2 id="title">Dashboard</h2>
  <p id="meta">Loading...</p>
  <div id="content"></div>
  <div style="margin-top:14px"><button id="logout">Log out</button></div>
</div>
<script>
const user = JSON.parse(localStorage.getItem('sebix_user')||'null');
if (!user || !user.api_token) {
  // no session — redirect to login
  window.location.href = '/auth/login';
} else {
  document.getElementById('title').textContent = 'Welcome, ' + (user.email || 'user');
  document.getElementById('meta').textContent = 'Role: ' + (user.role || '—') + ' — ID: ' + (user.id || '—');
  document.getElementById('content').innerHTML = '<pre style="background:#0b1220;color:#dbeafe;padding:10px;border-radius:8px">api_token: '+user.api_token+'</pre>';
}
document.getElementById('logout').addEventListener('click', function(){
  localStorage.removeItem('sebix_user');
  window.location.href = '/';
});
</script>
</body></html>
HTML;
	return $html;
});

$routes->post('auth/register', 'Auth::register');
$routes->post('auth/login',    'Auth::login');
