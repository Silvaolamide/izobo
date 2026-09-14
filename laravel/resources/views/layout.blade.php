<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title', 'IZOBO — Join the Movement')</title>
<meta name="description" content="Join the IZOBO movement in Owan East, Edo State. Register your interest in supporting the campaign.">
<meta name="robots" content="index,follow">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
<style>
:root{--green:#075b40;--deep:#043d2c;--bright:#0aa96a;--red:#ef3037;--cream:#f7f7f2;--ink:#10231c;--muted:#64736d;--line:#dce6e1}*{box-sizing:border-box}body{margin:0;background:var(--cream);color:var(--ink);font-family:'DM Sans',system-ui,sans-serif}a{text-decoration:none;color:inherit}.shell{width:min(1080px,calc(100% - 32px));margin:auto}.top{padding:22px 0}.brand{font:800 19px Manrope;color:var(--green)}.brand span{color:var(--red)}.card{background:#fff;border:1px solid rgba(16,35,28,.08);border-radius:24px;box-shadow:0 24px 70px rgba(5,56,40,.1)}.btn{border:0;border-radius:999px;background:var(--red);color:#fff;padding:15px 22px;font-weight:800;cursor:pointer}.field{margin-top:18px}.field label{display:block;font-size:13px;font-weight:800;margin-bottom:8px}.field input,.field select{width:100%;padding:14px 15px;border:1px solid var(--line);border-radius:12px;background:#fff;color:var(--ink);font:inherit;outline:none}.field input:focus,.field select:focus{border-color:var(--bright);box-shadow:0 0 0 4px rgba(10,169,106,.1)}.grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}.notice{padding:13px 15px;border-radius:12px;margin-bottom:20px;background:#edf8f2;color:#145b3f}.error{color:#a91f26;font-size:12px;margin-top:5px}.muted{color:var(--muted);line-height:1.7}@media(max-width:650px){.grid{grid-template-columns:1fr}.card{border-radius:18px}}
</style>
@stack('head')
</head><body>
<header class="top"><div class="shell"><a class="brand" href="/">IZOBO<span>.</span></a></div></header>
@yield('content')
@stack('scripts')
</body></html>
