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

// quick register page (sends JSON to POST /auth/register)
$routes->get('auth/register', static function () {
	$html = <<<'HTML'
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Register</title>
<style>body{font-family:system-ui,Arial;padding:18px;background:#f7f8fb}form{max-width:560px;background:#fff;padding:18px;border-radius:8px;border:1px solid #e6e9ef}</style>
</head><body>
<h2>Register</h2>
<form id="registerForm">
<label>Email<br><input name="email" type="email" required></label><br><br>
<label>Name<br><input name="name" required></label><br><br>
<label>Password<br><input name="password" type="password" required></label><br><br>
<label>Phone<br><input name="phone"></label><br><br>
<label>Location<br><input name="location"></label><br><br>
<label>Role<br>
<select name="role" required>
  <option value="end_user">End User</option>
  <option value="service_provider">Service Provider</option>
  <option value="admin">Admin</option>
</select>
</label><br><br>
<button type="submit">Register</button>
</form>
<pre id="out"></pre>
<script>
document.getElementById('registerForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const f = e.target;
  const payload = {
    email: f.email.value,
    name: f.name.value,
    password: f.password.value,
    phone: f.phone.value || null,
    location: f.location.value || null,
    role: f.role.value
  };
  const out = document.getElementById('out');
  out.textContent = 'Sending...';
  try{
    const res = await fetch('/auth/register', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(payload)
    });
    const data = await res.json().catch(()=>({}));
    out.textContent = JSON.stringify({status: res.status, body: data}, null, 2);
  }catch(err){
    out.textContent = 'Network error: '+err;
  }
});
</script>
</body></html>
HTML;
	return $html;
});

// quick login page (sends JSON to POST /auth/login)
$routes->get('auth/login', static function () {
	$html = <<<'HTML'
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login</title>
<style>body{font-family:system-ui,Arial;padding:18px;background:#f7f8fb}form{max-width:420px;background:#fff;padding:18px;border-radius:8px;border:1px solid #e6e9ef}</style>
</head><body>
<h2>Login</h2>
<form id="loginForm">
<label>Email<br><input name="email" type="email" required></label><br><br>
<label>Password<br><input name="password" type="password" required></label><br><br>
<button type="submit">Log in</button>
</form>
<pre id="out"></pre>
<script>
document.getElementById('loginForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const f = e.target;
  const payload = { email: f.email.value, password: f.password.value };
  const out = document.getElementById('out');
  out.textContent = 'Sending...';
  try{
    const res = await fetch('/auth/login', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(payload)
    });
    const data = await res.json().catch(()=>({}));
    out.textContent = JSON.stringify({status: res.status, body: data}, null, 2);
  }catch(err){
    out.textContent = 'Network error: '+err;
  }
});
</script>
</body></html>
HTML;
	return $html;
});

$routes->post('auth/register', 'Auth::register');
$routes->post('auth/login',    'Auth::login');
