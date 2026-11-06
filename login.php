<?php
if(session_status()==PHP_SESSION_NONE) session_start();
if(isset($_SESSION['user_id']) && ($_SESSION['user_role']??'')==='admin'){header('Location: index.php');exit();}
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Connexion - Tajmil Admin</title><link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"><style>
:root{--bg:#0a0a0c;--surface:#111114;--surface2:#18181c;--border:rgba(255,255,255,0.06);--border2:rgba(255,255,255,0.1);--em:#10b981;--em2:#34d399;--text:#f8f8f8;--muted:#6b7280;--muted2:#9ca3af;--danger:#ef4444}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Outfit',sans-serif;background:var(--bg);min-height:100vh;display:flex;align-items:center;justify-content:center;color:var(--text)}
.wrap{width:100%;max-width:420px;padding:24px;animation:up .6s}
@keyframes up{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.logo-row{display:flex;align-items:center;gap:12px;margin-bottom:40px}
.logo-icon{width:40px;height:40px;background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.25);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--em);font-size:17px}
.logo-text{font-family:'Syne',sans-serif;font-size:20px;font-weight:700;letter-spacing:-.3px}
.logo-text em{color:var(--em);font-style:normal}
.logo-badge{margin-left:auto;font-size:10px;background:rgba(16,185,129,0.1);color:var(--em);border:1px solid rgba(16,185,129,0.2);padding:3px 8px;border-radius:6px}
.card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:36px;position:relative;overflow:hidden}
.card::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(16,185,129,0.4),transparent)}
.card-title{font-family:'Syne',sans-serif;font-size:22px;font-weight:700;margin-bottom:6px}
.card-sub{font-size:13px;color:var(--muted2);margin-bottom:28px;line-height:1.5}
.field{margin-bottom:18px}
.field label{display:block;font-size:11px;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:var(--muted);margin-bottom:8px}
.input-wrap{position:relative}
.input-wrap i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:14px;pointer-events:none}
.field input{width:100%;padding:12px 14px 12px 40px;background:var(--surface2);border:1px solid var(--border2);border-radius:10px;color:var(--text);font-family:'Outfit',sans-serif;font-size:14px;outline:none;transition:border .2s,box-shadow .2s}
.field input:focus{border-color:rgba(16,185,129,0.5);box-shadow:0 0 0 3px rgba(16,185,129,0.08)}
.field input:focus~i,.input-wrap:focus-within i{color:var(--em)}
.btn-login{width:100%;padding:13px;background:var(--em);border:none;border-radius:10px;color:#0a0a0c;font-family:'Syne',sans-serif;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .2s}
.btn-login:hover{background:var(--em2);transform:translateY(-1px);box-shadow:0 8px 24px rgba(16,185,129,0.25)}
.err{background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);color:#fca5a5;padding:12px 14px;border-radius:10px;font-size:13px;margin-bottom:20px;display:flex;align-items:center;gap:8px}
.foot{text-align:center;margin-top:20px;font-size:12px;color:var(--muted)}
.foot a{color:var(--muted2);text-decoration:none;transition:color .2s}
.foot a:hover{color:var(--em)}
</style>
</head>
<body>
<div class="wrap">
<div class="logo-row"><div class="logo-icon"><i class="fas fa-spa"></i></div><div class="logo-text">Tajmil<em>.</em></div><span class="logo-badge">Administration</span></div>
<div class="card"><div class="card-title">Connexion</div><div class="card-sub">Espace réservé aux administrateurs</div>
<?php if(isset($_GET['error'])):?><div class="err"><i class="fas fa-shield-alt"></i> Identifiants incorrects.</div><?php endif;?>
<form method="POST" action="auth.php">
<div class="field"><label>Adresse email</label><div class="input-wrap"><input type="email" name="email" placeholder="admin@tajmil.ma" required autocomplete="email"><i class="fas fa-envelope"></i></div></div>
<div class="field"><label>Mot de passe</label><div class="input-wrap"><input type="password" name="password" placeholder="••••••••" required autocomplete="current-password"><i class="fas fa-lock"></i></div></div>
<button type="submit" class="btn-login"><i class="fas fa-arrow-right-to-bracket"></i> Accéder</button>
</form></div>
<div class="foot"><a href="../pages/home.php">← Retour à Tajmil Lwajh</a></div>
</div>
<script>document.querySelectorAll('.field input').forEach(e=>{e.addEventListener('focus',()=>e.closest('.input-wrap').querySelector('i').style.color='var(--em)');e.addEventListener('blur',()=>e.closest('.input-wrap').querySelector('i').style.color='');});</script>
</body>
</html>