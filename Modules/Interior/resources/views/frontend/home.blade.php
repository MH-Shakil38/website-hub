@php
    if (!isset($t)) {
        $t = app(\App\Services\ModuleThemeDataService::class)->getFrontendData('Interior');
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>{{ $t['meta']['page_title'] }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<style>
:root{
  --bg:{{ $t['colors']['bg'] }};
  --cream:{{ $t['colors']['cream'] }};
  --card:{{ $t['colors']['card'] }};
  --olive:{{ $t['colors']['olive'] }};
  --olive-dark:{{ $t['colors']['olive_dark'] }};
  --clay:{{ $t['colors']['clay'] }};
  --text:{{ $t['colors']['text'] }};
  --muted:{{ $t['colors']['muted'] }};
  --line:{{ $t['colors']['line'] }};
  --serif:'Cormorant Garamond', Georgia, 'Times New Roman', serif;
  --sans:'Jost', 'Segoe UI', Arial, sans-serif;
}
*{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
body{background:var(--bg);color:var(--text);font-family:var(--sans);font-weight:300;font-size:15px;line-height:1.6;overflow-x:hidden}
img{max-width:100%;display:block}
a{color:inherit;text-decoration:none}
.container{width:min(1180px,92%);margin:0 auto}
.serif{font-family:var(--serif)}
em{font-style:italic}
.eyebrow{font-size:11px;letter-spacing:.18em;text-transform:uppercase;color:var(--clay);font-weight:500}
.btn{display:inline-block;background:var(--olive);color:#fff;font-size:11px;letter-spacing:.14em;text-transform:uppercase;padding:13px 26px;border-radius:40px;font-weight:500;transition:background .25s}
.btn:hover{background:var(--olive-dark)}
.btn-light{background:#f8f2e9;color:var(--olive-dark)}
.btn-light:hover{background:#fff}
.link{font-size:11px;letter-spacing:.14em;text-transform:uppercase;font-weight:500;color:var(--olive-dark)}
.link span{margin-left:6px}
.ph{background:var(--card)}
.ph img{width:100%;height:100%;object-fit:cover}

/* ===== Header ===== */
header{position:absolute;top:0;left:0;right:0;z-index:20;padding:16px 0}
.nav{display:flex;align-items:center;justify-content:space-between;gap:20px}
.brand{display:flex;align-items:center;gap:10px}
.brand img{width:58px;height:58px;object-fit:contain}
.brand-text{line-height:1}
.brand-text b{display:block;font-family:var(--serif);font-weight:500;font-size:26px;color:var(--olive-dark);letter-spacing:.02em}
.brand-text small{display:block;font-size:9px;letter-spacing:.3em;color:var(--olive-dark);margin-top:3px}
.menu{display:flex;gap:30px;list-style:none}
.menu a{font-size:11px;letter-spacing:.14em;text-transform:uppercase;font-weight:500;padding-bottom:5px;border-bottom:1.5px solid transparent}
.menu a.active,.menu a:hover{border-color:var(--olive)}
.burger{display:none;background:none;border:0;cursor:pointer;width:40px;height:40px}
.burger span{display:block;width:24px;height:2px;background:var(--olive-dark);margin:5px auto;transition:.3s}

/* ===== Hero ===== */
.hero{position:relative;min-height:680px;background:var(--cream);overflow:hidden;display:flex;align-items:center}
.hero-bg{position:absolute;inset:-30px;will-change:transform;transition:transform .6s ease-out}
.hero-bg img{width:100%;height:100%;object-fit:cover;object-position:center;animation:kenburns 22s ease-in-out infinite alternate}
.hero-bg::after{content:"";position:absolute;inset:0;background:radial-gradient(ellipse 46% 60% at 50% 46%,rgba(248,242,233,.96) 0%,rgba(248,242,233,.86) 45%,rgba(248,242,233,.35) 75%,rgba(248,242,233,.1) 100%)}
.hero-content{position:relative;z-index:4;padding:170px 0 170px;width:100%}
.hero-content .inner{max-width:640px;margin:0 auto;text-align:center}
.hero h1{font-family:var(--serif);font-weight:400;font-size:clamp(40px,5.4vw,68px);line-height:1.08;margin:16px 0 20px;color:var(--text)}
.hero p{color:var(--muted);max-width:420px;margin:0 auto 30px}
.hero .eyebrow{color:var(--muted)}
.hero .line{display:block;overflow:hidden;padding-bottom:.06em}
.hero .line > span{display:inline-block;transform:translateY(110%);animation:lineUp 1.1s cubic-bezier(.2,.8,.2,1) forwards}
.hero .line:nth-child(2) > span{animation-delay:.18s}
.hero .line:nth-child(3) > span{animation-delay:.36s}
.hero .eyebrow{opacity:0;letter-spacing:.5em;animation:eyebrowIn 1.4s .1s cubic-bezier(.2,.8,.2,1) forwards}
.hero p,.hero .hero-actions{opacity:0;transform:translateY(24px);animation:fadeUp 1s cubic-bezier(.2,.8,.2,1) forwards}
.hero p{animation-delay:.7s}
.hero .hero-actions{animation-delay:.9s;display:flex;gap:14px;justify-content:center;flex-wrap:wrap}
.hero .btn{position:relative;overflow:hidden}
.hero .btn::after{content:"";position:absolute;top:0;left:-60%;width:40%;height:100%;background:linear-gradient(100deg,transparent,rgba(255,255,255,.45),transparent);transform:skewX(-20deg);animation:shine 4s 2.2s ease-in-out infinite}
.btn-ghost{background:transparent;color:var(--olive-dark);border:1px solid var(--olive)}
.btn-ghost:hover{background:var(--olive);color:#fff}
.hero em{position:relative;color:var(--olive-dark)}
.hero h1 em::after{content:"";position:absolute;left:0;right:0;bottom:.08em;height:2px;background:var(--clay);transform:scaleX(0);transform-origin:left;animation:underline .9s 1.3s cubic-bezier(.2,.8,.2,1) forwards}
.leaves{position:absolute;inset:0;z-index:3;pointer-events:none;overflow:hidden}
.leaf{position:absolute;top:-40px;width:22px;height:22px;opacity:0;animation:fall linear infinite}
.leaf svg{width:100%;height:100%;animation:sway 3s ease-in-out infinite alternate}
.hero-light{position:absolute;z-index:2;top:-20%;left:-10%;width:60%;height:140%;background:linear-gradient(105deg,transparent 30%,rgba(255,247,228,.55) 50%,transparent 70%);animation:lightSweep 9s ease-in-out infinite;pointer-events:none;mix-blend-mode:soft-light}
.hero-ring{position:absolute;z-index:4;right:5%;bottom:120px;width:120px;height:120px;opacity:0;animation:fadeIn 1s 1.4s forwards}
.hero-ring svg{width:100%;height:100%;animation:spin 18s linear infinite}
.hero-ring .dot{position:absolute;inset:40px;border-radius:50%;background:var(--olive);display:flex;align-items:center;justify-content:center}
.hero-ring .dot svg{width:22px;height:22px;animation:none;stroke:#fff;fill:none;stroke-width:1.4}
.scroll-cue{position:absolute;z-index:5;left:50%;bottom:78px;transform:translateX(-50%);width:24px;height:38px;border:1.5px solid var(--olive);border-radius:14px;opacity:0;animation:fadeIn 1s 1.6s forwards}
.scroll-cue::after{content:"";position:absolute;left:50%;top:7px;width:3px;height:8px;margin-left:-1.5px;border-radius:2px;background:var(--olive);animation:scrollDot 1.8s ease-in-out infinite}
@keyframes kenburns{from{transform:scale(1)}to{transform:scale(1.09) translate(-1%,-1%)}}
@keyframes lineUp{to{transform:translateY(0)}}
@keyframes eyebrowIn{to{opacity:1;letter-spacing:.18em}}
@keyframes fadeUp{to{opacity:1;transform:none}}
@keyframes fadeIn{to{opacity:1}}
@keyframes underline{to{transform:scaleX(1)}}
@keyframes shine{0%,70%{left:-60%}100%{left:130%}}
@keyframes fall{0%{transform:translate(0,0) rotate(0);opacity:0}10%{opacity:.85}90%{opacity:.7}100%{transform:translate(var(--dx),110vh) rotate(var(--rot));opacity:0}}
@keyframes sway{from{transform:rotate(-25deg) translateX(-8px)}to{transform:rotate(25deg) translateX(8px)}}
@keyframes lightSweep{0%,100%{transform:translateX(-30%);opacity:.4}50%{transform:translateX(120%);opacity:1}}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes scrollDot{0%{transform:translateY(0);opacity:1}80%{transform:translateY(14px);opacity:0}100%{opacity:0}}
.hero-wave{position:absolute;left:0;right:0;bottom:-1px;z-index:3;width:100%;height:110px}

/* ===== Section heads ===== */
section{position:relative}
.sec{padding:70px 0}
.head-center{text-align:center;margin-bottom:46px}
.head-center h2,.h2{font-family:var(--serif);font-weight:400;font-size:clamp(30px,3.4vw,40px);line-height:1.15;margin-top:8px}
.leaf-deco{position:absolute;opacity:.55;pointer-events:none}

/* ===== Services ===== */
.services{display:grid;grid-template-columns:repeat(4,1fr);gap:22px}
.svc{background:var(--card);border-radius:14px;padding:12px 12px 22px;text-align:center;position:relative;margin-top:28px}
.svc .ph{height:150px;border-radius:10px;overflow:hidden}
.icon-circle{width:56px;height:56px;border-radius:50%;background:var(--olive);display:flex;align-items:center;justify-content:center;border:4px solid var(--bg);position:absolute;top:-28px;left:50%;transform:translateX(-50%);z-index:2}
.icon-circle svg{width:24px;height:24px;stroke:#fff;fill:none;stroke-width:1.4;stroke-linecap:round;stroke-linejoin:round}
.svc h3{font-family:var(--serif);font-weight:500;font-size:20px;margin:18px 0 6px}
.svc p{font-size:13px;color:var(--muted);max-width:210px;margin:0 auto 14px}

/* ===== Projects ===== */
.proj-head{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:24px;gap:20px}
.projects{display:grid;grid-template-columns:repeat(5,1fr);gap:16px}
.proj .ph{height:170px;border-radius:8px;overflow:hidden}
.proj h4{font-family:var(--serif);font-weight:600;font-size:16px;margin-top:10px}
.proj small{font-size:12px;color:var(--muted)}

/* ===== Founder ===== */
.founder-wrap{background:var(--cream);padding:70px 0;overflow:hidden}
.founder{display:grid;grid-template-columns:1.05fr 1fr .9fr;gap:40px;align-items:center}
.f-photo{position:relative;padding:20px 0 20px 40px}
.f-photo .olive-blob{position:absolute;left:0;top:0;width:62%;height:78%;background:var(--olive);border-radius:58% 42% 46% 54% / 50% 46% 54% 50%}
.f-photo .photo{position:relative;z-index:2;border-radius:42% 58% 48% 52% / 46% 40% 60% 54%;overflow:hidden;aspect-ratio:1/1;border:6px solid var(--bg)}
.f-photo .photo img{width:100%;height:100%;object-fit:cover;object-position:center 20%}
.f-photo .clay-dot{position:absolute;z-index:3;left:18px;bottom:30px;width:74px;height:74px;border-radius:50%;background:var(--clay);display:flex;align-items:center;justify-content:center;border:5px solid var(--cream)}
.f-photo .clay-dot svg{width:32px;height:32px;stroke:#fff;fill:none;stroke-width:1.3}
.f-text p{color:var(--muted);margin:16px 0}
.f-sign{border-left:2px solid var(--clay);padding-left:14px;margin:4px 0 20px}
.f-sign b{display:block;font-family:var(--serif);font-weight:600;font-size:22px;color:var(--olive-dark);line-height:1.2}
.f-sign small{font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--muted)}
.f-deco{border-radius:56% 44% 40% 60% / 52% 48% 52% 48%;overflow:hidden;aspect-ratio:1/1.05}
.f-deco img{width:100%;height:100%;object-fit:cover}

/* ===== Process ===== */
.process-wrap{display:grid;grid-template-columns:1fr 290px;gap:30px;align-items:center}
.steps{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;position:relative;margin-top:10px}
.steps::before{content:"";position:absolute;top:26px;left:10%;right:10%;border-top:1.5px dashed #bdb299}
.step{text-align:center;position:relative}
.step .icon-circle{position:relative;top:auto;left:auto;transform:none;margin:0 auto 14px;width:52px;height:52px}
.step h5{font-size:12px;letter-spacing:.12em;text-transform:uppercase;font-weight:500;margin-bottom:6px}
.step p{font-size:12.5px;color:var(--muted)}
.why{background:var(--olive);color:#f3eee2;padding:46px 36px;border-radius:52% 48% 44% 56% / 38% 40% 60% 62%;position:relative;min-height:330px}
.why .eyebrow{color:#e7dcc6}
.why ul{list-style:none;margin-top:18px}
.why li{display:flex;align-items:center;gap:12px;font-size:13.5px;padding:8px 0}
.why li svg{width:22px;height:22px;flex:none;stroke:#efe6d3;fill:none;stroke-width:1.3}

/* ===== Testimonials ===== */
.testi-wrap{background:var(--cream);padding:70px 0 80px;border-radius:50% 50% 0 0 / 60px 60px 0 0}
.testi-slider{position:relative;padding:0 50px}
.testis{display:grid;grid-template-columns:repeat(3,1fr);gap:40px}
.testi .q{font-family:var(--serif);font-size:54px;line-height:.5;color:var(--muted);height:22px}
.testi p{font-size:13.5px;margin:6px 0 16px}
.testi .who{display:flex;justify-content:space-between;align-items:center;gap:12px}
.testi .who b{font-size:12px;letter-spacing:.12em;text-transform:uppercase;font-weight:500}
.testi .who small{display:block;font-size:12px;color:var(--muted)}
.avatar{width:46px;height:46px;border-radius:50%;background:var(--olive);color:#fff;display:flex;align-items:center;justify-content:center;font-family:var(--serif);font-size:18px;flex:none}
.arrow{position:absolute;top:40%;width:36px;height:36px;border-radius:50%;background:#fff;border:1px solid var(--line);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:16px;color:var(--text)}
.arrow.prev{left:0}.arrow.next{right:0}

/* ===== CTA ===== */
.cta{background:var(--olive);border-radius:18px;display:grid;grid-template-columns:.85fr 1.6fr;overflow:hidden;color:#f5efe2;position:relative;margin-top:-30px}
.cta .ph{min-height:160px}
.cta-body{display:flex;align-items:center;justify-content:space-between;gap:24px;padding:30px 50px 30px 40px;position:relative}
.cta .eyebrow{color:#e5d8bd}
.cta h2{font-family:var(--serif);font-weight:400;font-size:clamp(28px,3vw,38px);line-height:1.12;margin-top:6px}

/* ===== Footer ===== */
footer{padding:50px 0 0;margin-top:30px}
.foot{display:grid;grid-template-columns:1.5fr 1fr 1.2fr 1.3fr 150px;gap:30px;align-items:start}
.foot .brand{align-items:flex-start}
.foot-about p{font-size:13px;color:var(--muted);margin-top:4px}
.foot-about .row{display:flex;flex-direction:column;gap:10px}
.socials{display:flex;gap:8px;margin-top:14px}
.socials a{width:30px;height:30px;border-radius:50%;border:1px solid var(--olive);display:flex;align-items:center;justify-content:center}
.socials svg{width:14px;height:14px;fill:var(--olive)}
.foot h6{font-size:11px;letter-spacing:.16em;text-transform:uppercase;font-weight:500;margin-bottom:10px}
.foot ul{list-style:none}
.foot li{font-size:13px;color:var(--muted);padding:2px 0}
.foot li a:hover{color:var(--olive-dark)}
.arch{border-radius:80px 80px 0 0;overflow:hidden;height:170px}
.arch img{width:100%;height:100%;object-fit:cover}
.copy{background:var(--olive);color:#e9e1cf;text-align:center;font-size:12px;padding:10px;margin-top:40px}

/* ===== Gallery & Video Gallery ===== */
.g-filters{display:flex;flex-wrap:wrap;justify-content:center;gap:10px;margin:-18px 0 34px}
.g-filters button{font-family:var(--sans);font-size:11px;letter-spacing:.14em;text-transform:uppercase;font-weight:500;padding:9px 20px;border-radius:30px;border:1px solid var(--olive);background:transparent;color:var(--olive-dark);cursor:pointer;transition:background .2s,color .2s}
.g-filters button.on,.g-filters button:hover{background:var(--olive);color:#fff}
.gallery{columns:4 230px;column-gap:16px}
.g-item{break-inside:avoid;margin:0 0 16px;border-radius:12px;overflow:hidden;position:relative;cursor:zoom-in;background:var(--card);display:block;width:100%;border:0;padding:0}
.g-item img{width:100%;height:auto;transition:transform .5s}
.g-item:hover img{transform:scale(1.04)}
.g-item .cap{position:absolute;left:0;right:0;bottom:0;padding:30px 16px 12px;background:linear-gradient(transparent,rgba(40,36,24,.7));color:#fff;text-align:left;opacity:0;transition:opacity .3s}
.g-item:hover .cap,.g-item:focus-visible .cap{opacity:1}
.cap b{display:block;font-family:var(--serif);font-weight:500;font-size:19px;line-height:1.2}
.cap small{font-size:10px;letter-spacing:.16em;text-transform:uppercase;opacity:.85}
.g-item.hide,.v-card.hide{display:none}

/* Video Grid */
.v-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}
.v-card{background:var(--bg);border:1px solid var(--line);border-radius:14px;padding:10px 10px 16px;text-align:left;cursor:pointer;transition:transform .25s,box-shadow .25s;font-family:var(--sans);color:var(--text)}
.v-card:hover{transform:translateY(-4px);box-shadow:0 12px 26px rgba(59,55,47,.08)}
.v-thumb{position:relative;height:180px;border-radius:10px;overflow:hidden}
.v-thumb img{width:100%;height:100%;object-fit:cover;transition:transform .5s}
.v-card:hover .v-thumb img{transform:scale(1.05)}
.v-play{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:52px;height:52px;border-radius:50%;background:var(--olive);border:3px solid rgba(248,242,233,.9);display:flex;align-items:center;justify-content:center;box-shadow:0 6px 18px rgba(0,0,0,.28);transition:transform .25s,background .25s}
.v-card:hover .v-play{transform:translate(-50%,-50%) scale(1.1);background:var(--clay)}
.v-play svg{width:20px;height:20px;margin-left:2px}
.v-dur{position:absolute;right:10px;bottom:10px;background:rgba(40,36,24,.78);color:#fff;font-size:11px;letter-spacing:.08em;padding:3px 9px;border-radius:20px;font-weight:500}
.v-meta{padding:12px 6px 0}
.v-meta small{display:block;font-size:10px;letter-spacing:.16em;text-transform:uppercase;color:var(--clay);font-weight:500}
.v-meta h4{font-family:var(--serif);font-weight:600;font-size:18px;line-height:1.25;margin-top:4px;color:var(--text)}

.lb{position:fixed;inset:0;z-index:100;background:rgba(30,28,20,.92);display:none;align-items:center;justify-content:center;padding:60px 70px}
.lb.open{display:flex}
.lb figure{max-width:1100px;width:100%;text-align:center}
.lb img{max-height:78vh;width:auto;max-width:100%;margin:0 auto;border-radius:10px}
.lb figcaption{color:#efe6d3;font-family:var(--serif);font-size:22px;margin-top:14px}
.lb figcaption small{display:block;font-family:var(--sans);font-size:11px;letter-spacing:.16em;text-transform:uppercase;opacity:.7}
.lb button{position:absolute;background:rgba(255,255,255,.12);border:0;color:#fff;width:46px;height:46px;border-radius:50%;font-size:24px;cursor:pointer}
.lb button:hover{background:rgba(255,255,255,.25)}
.lb .x{top:20px;right:20px}.lb .lp{left:16px;top:50%;transform:translateY(-50%)}.lb .ln{right:16px;top:50%;transform:translateY(-50%)}

/* ===== Full-Site Luxury Motion & Animation System ===== */
.scroll-progress{position:fixed;top:0;left:0;height:3px;width:0%;background:linear-gradient(90deg,var(--olive),var(--clay),var(--olive-dark));z-index:999;transition:width .1s linear;box-shadow:0 1px 6px rgba(178,106,71,.35)}
header{transition:background .4s cubic-bezier(.16,1,.3,1),padding .4s cubic-bezier(.16,1,.3,1),box-shadow .4s cubic-bezier(.16,1,.3,1),backdrop-filter .4s}
header.scrolled{position:fixed;background:rgba(248,242,233,.90);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);padding:10px 0;box-shadow:0 8px 30px rgba(59,55,47,.07);border-bottom:1px solid rgba(221,208,188,.6)}
.brand img{transition:transform .5s cubic-bezier(.22,1,.36,1)}
.brand:hover img{transform:rotate(-6deg) scale(1.06)}
.menu a{position:relative;transition:color .25s}
.menu a::after{content:"";position:absolute;left:0;right:0;bottom:0;height:1.5px;background:var(--olive);transform:scaleX(0);transform-origin:right;transition:transform .35s cubic-bezier(.22,1,.36,1)}
.menu a.active::after,.menu a:hover::after{transform:scaleX(1);transform-origin:left}
.menu a.active,.menu a:hover{border-color:transparent}
.btn{transition:transform .3s cubic-bezier(.22,1,.36,1),background .3s,box-shadow .3s;will-change:transform}
.btn:hover{transform:translateY(-3px);box-shadow:0 10px 24px rgba(71,77,49,.22)}
.btn:active{transform:translateY(-1px)}
.link{display:inline-flex;align-items:center;transition:color .25s}
.link span{display:inline-block;transition:transform .3s cubic-bezier(.22,1,.36,1)}
.link:hover{color:var(--clay)}
.link:hover span{transform:translateX(5px)}

/* Scroll Reveal States */
.m-reveal{opacity:0;transform:translateY(38px);transition:opacity .85s cubic-bezier(.16,1,.3,1),transform .85s cubic-bezier(.16,1,.3,1);transition-delay:var(--m-delay,0ms);will-change:opacity,transform}
.m-reveal-left{opacity:0;transform:translateX(-46px);transition:opacity .9s cubic-bezier(.16,1,.3,1),transform .9s cubic-bezier(.16,1,.3,1);transition-delay:var(--m-delay,0ms)}
.m-reveal-right{opacity:0;transform:translateX(46px);transition:opacity .9s cubic-bezier(.16,1,.3,1),transform .9s cubic-bezier(.16,1,.3,1);transition-delay:var(--m-delay,0ms)}
.m-reveal-scale{opacity:0;transform:scale(.92) translateY(28px);transition:opacity .85s cubic-bezier(.16,1,.3,1),transform .85s cubic-bezier(.16,1,.3,1);transition-delay:var(--m-delay,0ms)}
.m-reveal.in-view,.m-reveal-left.in-view,.m-reveal-right.in-view,.m-reveal-scale.in-view{opacity:1;transform:none}

/* Animated Italic Underline on Section Headings */
.head-center h2 em,.h2 em,.cta h2 em{position:relative;display:inline-block}
.head-center h2 em::after,.h2 em::after,.cta h2 em::after{content:"";position:absolute;left:0;right:0;bottom:.06em;height:1.5px;background:var(--clay);transform:scaleX(0);transform-origin:left;transition:transform .85s .35s cubic-bezier(.16,1,.3,1)}
.in-view h2 em::after,h2.in-view em::after,.cta.in-view h2 em::after{transform:scaleX(1)}

/* Service Cards Motion */
.svc{transition:transform .45s cubic-bezier(.22,1,.36,1),box-shadow .45s cubic-bezier(.22,1,.36,1),background .35s}
.svc:hover{transform:translateY(-10px);box-shadow:0 20px 38px rgba(59,55,47,.10);background:#f4ece0}
.svc .ph img{transition:transform .65s cubic-bezier(.22,1,.36,1)}
.svc:hover .ph img{transform:scale(1.08)}
.svc .icon-circle{transition:transform .45s cubic-bezier(.34,1.56,.64,1),background .35s,box-shadow .35s}
.svc:hover .icon-circle{transform:translateX(-50%) scale(1.12) rotate(8deg);background:var(--clay);box-shadow:0 8px 18px rgba(178,106,71,.32)}

/* Projects Cards Motion */
.proj{transition:transform .45s cubic-bezier(.22,1,.36,1)}
.proj:hover{transform:translateY(-8px)}
.proj .ph{position:relative;box-shadow:0 6px 18px rgba(59,55,47,.06);transition:box-shadow .45s}
.proj:hover .ph{box-shadow:0 18px 34px rgba(59,55,47,.14)}
.proj .ph img{transition:transform .7s cubic-bezier(.22,1,.36,1)}
.proj:hover .ph img{transform:scale(1.09)}
.proj .ph::after{content:"";position:absolute;inset:0;background:linear-gradient(to top,rgba(45,40,30,.28),transparent 60%);opacity:0;transition:opacity .4s}
.proj:hover .ph::after{opacity:1}
.proj h4{transition:color .25s}
.proj:hover h4{color:var(--clay)}

/* Founder Organic Morphing & Floating Motion */
@keyframes blobMorph{
  0%,100%{border-radius:58% 42% 46% 54% / 50% 46% 54% 50%}
  33%{border-radius:46% 54% 58% 42% / 56% 42% 58% 44%}
  66%{border-radius:52% 48% 42% 58% / 44% 58% 42% 56%}
}
@keyframes photoMorph{
  0%,100%{border-radius:42% 58% 48% 52% / 46% 40% 60% 54%}
  50%{border-radius:54% 46% 42% 58% / 52% 54% 46% 48%}
}
@keyframes floatBob{
  0%,100%{transform:translateY(0) rotate(0deg)}
  50%{transform:translateY(-8px) rotate(6deg)}
}
.f-photo .olive-blob{animation:blobMorph 11s ease-in-out infinite}
.f-photo .photo{animation:photoMorph 13s ease-in-out infinite;box-shadow:0 18px 38px rgba(59,55,47,.12)}
.f-photo .photo img{transition:transform .7s cubic-bezier(.22,1,.36,1)}
.f-photo:hover .photo img{transform:scale(1.06)}
.f-photo .clay-dot{animation:floatBob 4.5s ease-in-out infinite;box-shadow:0 10px 24px rgba(178,106,71,.28)}
.f-deco{animation:blobMorph 14s ease-in-out infinite reverse;box-shadow:0 16px 34px rgba(59,55,47,.10)}
.f-deco img{transition:transform .8s cubic-bezier(.22,1,.36,1)}
.f-deco:hover img{transform:scale(1.07)}

/* Process Steps & Why Choose Us Motion */
.step .icon-circle{transition:transform .45s cubic-bezier(.34,1.56,.64,1),background .35s,box-shadow .35s}
.step:hover .icon-circle{transform:scale(1.15) translateY(-4px);background:var(--clay);box-shadow:0 10px 20px rgba(178,106,71,.28)}
.why{animation:blobMorph 15s ease-in-out infinite;box-shadow:0 20px 44px rgba(71,77,49,.22)}
.why li{transition:transform .3s cubic-bezier(.22,1,.36,1)}
.why li:hover{transform:translateX(6px)}

/* Video Play Button Sonar Pulse & Gallery Filter Pop */
@keyframes playPulse{
  0%{box-shadow:0 0 0 0 rgba(91,98,64,.55),0 6px 18px rgba(0,0,0,.28)}
  70%{box-shadow:0 0 0 16px rgba(91,98,64,0),0 6px 18px rgba(0,0,0,.28)}
  100%{box-shadow:0 0 0 0 rgba(91,98,64,0),0 6px 18px rgba(0,0,0,.28)}
}
.v-play{animation:playPulse 2.4s infinite}
@keyframes filterPop{
  from{opacity:0;transform:scale(.93) translateY(16px)}
  to{opacity:1;transform:none}
}
.g-item.pop-anim,.v-card.pop-anim{animation:filterPop .45s cubic-bezier(.16,1,.3,1) both}

/* Testimonials & CTA Motion */
.testi{transition:transform .4s cubic-bezier(.22,1,.36,1),opacity .4s}
.testi:hover{transform:translateY(-5px)}
.avatar{transition:transform .35s cubic-bezier(.34,1.56,.64,1),background .35s}
.testi:hover .avatar{transform:scale(1.1) rotate(-6deg);background:var(--clay)}
.arrow{transition:transform .25s,background .25s,color .25s,box-shadow .25s}
.arrow:hover{background:var(--olive);color:#fff;border-color:var(--olive);transform:scale(1.1);box-shadow:0 6px 16px rgba(71,77,49,.2)}
.cta{box-shadow:0 22px 48px rgba(71,77,49,.22);transition:transform .45s cubic-bezier(.22,1,.36,1)}
.cta:hover{transform:translateY(-4px)}
.cta .ph img,.arch img{transition:transform .8s cubic-bezier(.22,1,.36,1)}
.cta:hover .ph img,.arch:hover img{transform:scale(1.07)}
.socials a{transition:transform .3s cubic-bezier(.34,1.56,.64,1),background .3s,border-color .3s}
.socials a:hover{transform:translateY(-4px) scale(1.08);background:var(--olive);border-color:var(--olive)}
.socials a:hover svg{fill:#fff}
.leaf-deco{transition:transform .2s ease-out;will-change:transform}

/* ===== Responsive ===== */
@media (max-width:1024px){
  .services,.v-grid{grid-template-columns:repeat(2,1fr);row-gap:28px}
  .projects{grid-template-columns:repeat(3,1fr)}
  .founder{grid-template-columns:1fr 1fr}
  .f-deco{display:none}
  .process-wrap{grid-template-columns:1fr}
  .why{max-width:420px;margin:0 auto;border-radius:44% 56% 40% 60% / 30% 34% 66% 70%}
  .foot{grid-template-columns:1fr 1fr 1fr}
  .foot .arch{display:none}
  .foot-about{grid-column:1/-1}
}
@media (max-width:860px){
  .menu{position:fixed;top:0;right:-100%;width:78%;max-width:320px;height:100vh;background:var(--bg);flex-direction:column;padding:100px 34px;gap:22px;box-shadow:-10px 0 30px rgba(0,0,0,.08);transition:right .35s;z-index:30}
  .menu.open{right:0}
  .burger{display:block;z-index:40;position:relative}
  .burger.open span:nth-child(1){transform:translateY(7px) rotate(45deg)}
  .burger.open span:nth-child(2){opacity:0}
  .burger.open span:nth-child(3){transform:translateY(-7px) rotate(-45deg)}
  .nav .btn{display:none}
  .hero-bg::after{background:radial-gradient(ellipse 90% 55% at 50% 45%,rgba(248,242,233,.96) 0%,rgba(248,242,233,.8) 55%,rgba(248,242,233,.25) 100%)}
  .hero-bg img{object-position:60% center}
  .hero{min-height:640px}
  .hero-content{padding:130px 0 150px}
  .hero-ring{width:84px;height:84px;right:4%;bottom:110px}
  .hero-ring .dot{inset:28px}
  .scroll-cue{display:none}
}
@media (max-width:640px){
  .hero-ring{display:none}
  .hero .hero-actions{flex-direction:column;align-items:center;gap:10px}
  .hero .hero-actions .btn{min-width:230px;text-align:center}
  .testis,.services,.v-grid{grid-template-columns:1fr}
  .testis .testi:not(.show){display:none}
  .cta{grid-template-columns:1fr}
  .cta .ph{min-height:180px}
  .cta-body{flex-direction:column;align-items:flex-start;padding:30px}
  .sec{padding:56px 0}
  .projects{grid-template-columns:1fr 1fr}
  .proj:last-child{grid-column:1/-1}
  .proj-head{flex-direction:column;align-items:flex-start}
  .founder{grid-template-columns:1fr}
  .f-photo{max-width:340px;margin:0 auto}
  .steps{grid-template-columns:1fr;gap:22px}
  .steps::before{top:0;bottom:0;left:26px;right:auto;border-top:0;border-left:1.5px dashed #bdb299}
  .step{display:grid;grid-template-columns:52px 1fr;column-gap:16px;text-align:left;align-items:center}
  .step .icon-circle{margin:0;grid-row:span 2}
  .step h5{margin:0;align-self:end}
  .step p{align-self:start}
  .why{padding:44px 30px;border-radius:30px}
  .testi-slider{padding:0 40px}
  .foot{grid-template-columns:1fr 1fr}
  .foot > div:nth-child(4){grid-column:1/-1}
  .brand img{width:48px;height:48px}
  .brand-text b{font-size:22px}
  .gallery{columns:2;column-gap:10px}.g-item{margin-bottom:10px}
  .g-item .cap{opacity:1;padding:24px 10px 8px}.cap b{font-size:15px}
  .lb{padding:60px 12px}.lb .lp,.lb .ln{top:auto;bottom:18px;transform:none}
}
@media (prefers-reduced-motion:reduce){*{transition:none!important;scroll-behavior:auto!important;animation:none!important}.hero .line>span,.hero p,.hero .hero-actions{transform:none!important}.hero .eyebrow,.hero p,.hero .hero-actions,.hero-ring{opacity:1!important;letter-spacing:.18em}.hero h1 em::after{transform:scaleX(1)}.leaves,.hero-light{display:none}}
a:focus-visible,button:focus-visible{outline:2px solid var(--clay);outline-offset:3px}
</style>
</head>
<body>
<div class="scroll-progress" id="scrollProgressBar"></div>

@include('interior::frontend.partials.header')
@include('interior::frontend.partials.hero')
@include('interior::frontend.partials.services')
@include('interior::frontend.partials.founder')
@include('interior::frontend.partials.projects')
@include('interior::frontend.partials.gallery')
@include('interior::frontend.partials.video-gallery')
@include('interior::frontend.partials.process')
@include('interior::frontend.partials.testimonials')
@include('interior::frontend.partials.cta')
@include('interior::frontend.partials.footer')

<script>
  const burger=document.getElementById('burger'),menu=document.getElementById('menu');
  burger.addEventListener('click',()=>{menu.classList.toggle('open');burger.classList.toggle('open')});
  menu.querySelectorAll('a').forEach(a=>a.addEventListener('click',()=>{menu.classList.remove('open');burger.classList.remove('open')}));

  // Testimonial slider (mobile shows one at a time; desktop rotates order)
  const box=document.getElementById('testis');
  function move(dir){
    const items=[...box.children];
    if(!items.length)return;
    if(window.innerWidth<=860){
      let i=items.findIndex(t=>t.classList.contains('show'));
      if(i<0)i=0;
      items[i].classList.remove('show');
      i=(i+dir+items.length)%items.length;
      items[i].classList.add('show');
    }else{
      dir>0?box.appendChild(items[0]):box.prepend(items[items.length-1]);
    }
  }
  document.getElementById('next').onclick=()=>move(1);
  document.getElementById('prev').onclick=()=>move(-1);

  // ===== Gallery =====
  const GAL=@json($t['gallery']['items']);
  const CATN={living:'Living',kitchen:'Kitchen',bedroom:'Bedroom',bath:'Bath',dining:'Dining & Decor'};
  const grid=document.getElementById('gallery-grid');
  GAL.forEach((g,i)=>{
    g.src=g.src||`https://images.unsplash.com/photo-${g.id}?auto=format&fit=crop&w=700&q=75`;
    g.full=g.full||`https://images.unsplash.com/photo-${g.id}?auto=format&fit=crop&w=1600&q=80`;
    const b=document.createElement('button');
    b.className='g-item';b.dataset.cat=g.cat;b.dataset.i=i;
    b.innerHTML=`<img src="${g.src}" alt="${g.t}" loading="lazy"><span class="cap"><small>${CATN[g.cat]||g.cat}</small><b>${g.t}</b></span>`;
    b.querySelector('img').onerror=()=>{b.remove();g.dead=true};
    b.onclick=()=>openLb(i);
    grid.appendChild(b);
  });
  let cur='all',lbI=0;
  document.getElementById('gFilters').addEventListener('click',e=>{
    const btn=e.target.closest('button');if(!btn)return;
    document.querySelectorAll('#gFilters button').forEach(x=>x.classList.remove('on'));
    btn.classList.add('on');cur=btn.dataset.f;
    grid.querySelectorAll('.g-item').forEach(it=>it.classList.toggle('hide',cur!=='all'&&it.dataset.cat!==cur));
  });
  const lb=document.getElementById('lb'),lbImg=document.getElementById('lbImg'),lbCap=document.getElementById('lbCap');
  const visible=()=>GAL.map((g,i)=>i).filter(i=>!GAL[i].dead&&(cur==='all'||GAL[i].cat===cur));
  function show(i){lbI=i;const g=GAL[i];lbImg.src=g.full;lbImg.alt=g.t;lbCap.innerHTML=`<small>${CATN[g.cat]||g.cat}</small>${g.t}`}
  function openLb(i){show(i);lb.classList.add('open');document.body.style.overflow='hidden';document.getElementById('lbX').focus()}
  function closeLb(){lb.classList.remove('open');document.body.style.overflow=''}
  function step(d){const v=visible();let p=v.indexOf(lbI);show(v[(p+d+v.length)%v.length])}
  document.getElementById('lbX').onclick=closeLb;
  document.getElementById('lbP').onclick=()=>step(-1);
  document.getElementById('lbN').onclick=()=>step(1);
  lb.addEventListener('click',e=>{if(e.target===lb)closeLb()});
  document.addEventListener('keydown',e=>{if(!lb.classList.contains('open'))return;if(e.key==='Escape')closeLb();if(e.key==='ArrowLeft')step(-1);if(e.key==='ArrowRight')step(1)});
  let sx=0;lb.addEventListener('touchstart',e=>sx=e.touches[0].clientX,{passive:true});
  lb.addEventListener('touchend',e=>{const dx=e.changedTouches[0].clientX-sx;if(Math.abs(dx)>50)step(dx<0?1:-1)});

  // ===== Video Gallery Filter & Lightbox =====
  const vFilters=document.getElementById('vFilters'),vGrid=document.getElementById('video-grid');
  if(vFilters&&vGrid){
    vFilters.addEventListener('click',e=>{
      const btn=e.target.closest('button');if(!btn)return;
      vFilters.querySelectorAll('button').forEach(x=>x.classList.remove('on'));
      btn.classList.add('on');
      const vf=btn.dataset.vf;
      vGrid.querySelectorAll('.v-card').forEach(c=>c.classList.toggle('hide',vf!=='all'&&c.dataset.vcat!==vf));
    });
    const vLb=document.getElementById('vLb'),vWrap=document.getElementById('vPlayerWrap'),vCap=document.getElementById('vLbCap'),vClose=document.getElementById('vLbX');
    function openVideo(card){
      const url=card.dataset.vurl||'';
      const title=card.dataset.vtitle||'';
      const label=card.dataset.vlabel||'';
      if(url.match(/\.(mp4|webm|ogg)(\?|$)/i)){
        vWrap.innerHTML=`<video src="${url}" controls autoplay style="position:absolute;inset:0;width:100%;height:100%;object-fit:contain;background:#000"></video>`;
      }else{
        const sep=url.includes('?')?'&':'?';
        vWrap.innerHTML=`<iframe src="${url}${sep}autoplay=1" style="position:absolute;inset:0;width:100%;height:100%;border:0" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>`;
      }
      vCap.innerHTML=`<small>${label}</small>${title}`;
      vLb.classList.add('open');
      document.body.style.overflow='hidden';
    }
    function closeVideo(){
      vLb.classList.remove('open');
      vWrap.innerHTML='';
      document.body.style.overflow='';
    }
    vGrid.querySelectorAll('.v-card').forEach(c=>c.addEventListener('click',()=>openVideo(c)));
    vClose.onclick=closeVideo;
    vLb.addEventListener('click',e=>{if(e.target===vLb)closeVideo()});
    document.addEventListener('keydown',e=>{if(vLb.classList.contains('open')&&e.key==='Escape')closeVideo()});
  }

  // ===== Hero: drifting leaves + mouse parallax =====
  (function(){
    if(window.matchMedia('(prefers-reduced-motion: reduce)').matches)return;
    const box=document.getElementById('leaves');
    const cols=['#5b6240','#8a9163','#6f7650','#b26a47','#9aa070'];
    const n=window.innerWidth<640?8:16;
    for(let i=0;i<n;i++){
      const d=document.createElement('div');d.className='leaf';
      const s=12+Math.random()*16, c=cols[i%cols.length];
      d.style.cssText=`left:${Math.random()*100}%;width:${s}px;height:${s}px;animation-duration:${11+Math.random()*10}s;animation-delay:${-Math.random()*20}s;--dx:${(Math.random()*240-120).toFixed(0)}px;--rot:${(Math.random()*720-360).toFixed(0)}deg`;
      d.innerHTML=`<svg viewBox="0 0 24 24" style="animation-duration:${2+Math.random()*2}s"><path d="M12 2C6 6 4 12 6 20c8-1 13-7 12-17-2 0-4 0-6-1z" fill="${c}" opacity=".85"/><path d="M7 19C10 13 13 8 17 4" stroke="#f8f2e9" stroke-width=".8" fill="none" opacity=".6"/></svg>`;
      box.appendChild(d);
    }
    const hero=document.querySelector('.hero'),bg=document.querySelector('.hero-bg');
    if(window.matchMedia('(pointer:fine)').matches){
      hero.addEventListener('mousemove',e=>{
        const r=hero.getBoundingClientRect(),x=(e.clientX-r.left)/r.width-.5,y=(e.clientY-r.top)/r.height-.5;
        bg.style.transform=`translate(${-x*24}px,${-y*16}px)`;
      });
      hero.addEventListener('mouseleave',()=>bg.style.transform='');
    }
  })();

  // ===== Full-Site Scroll Reveal, Parallax Leaves, Sticky Header & Filter Animations =====
  (function(){
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const progressBar = document.getElementById('scrollProgressBar');
    const headerEl = document.querySelector('header');
    const decoLeaves = document.querySelectorAll('.leaf-deco');

    // 1. Auto-attach staggered scroll reveal classes across all 11 sections
    if(!reducedMotion){
      const revealGroups = [
        { sel: '.head-center, .proj-head', cls: 'm-reveal', stagger: 0 },
        { sel: '#services .svc', cls: 'm-reveal', stagger: 90 },
        { sel: '.founder .f-photo', cls: 'm-reveal-left', stagger: 0 },
        { sel: '.founder .f-text', cls: 'm-reveal', stagger: 120 },
        { sel: '.founder .f-deco', cls: 'm-reveal-right', stagger: 220 },
        { sel: '#projects .proj', cls: 'm-reveal-scale', stagger: 85 },
        { sel: '.g-filters', cls: 'm-reveal', stagger: 60 },
        { sel: '#gallery-grid .g-item', cls: 'm-reveal-scale', stagger: 55 },
        { sel: '#video-grid .v-card', cls: 'm-reveal', stagger: 90 },
        { sel: '#process .step', cls: 'm-reveal', stagger: 95 },
        { sel: '#process .why', cls: 'm-reveal-right', stagger: 150 },
        { sel: '#testis .testi', cls: 'm-reveal', stagger: 110 },
        { sel: '.cta', cls: 'm-reveal-scale', stagger: 0 },
        { sel: 'footer .foot > div', cls: 'm-reveal', stagger: 80 }
      ];

      revealGroups.forEach(group => {
        document.querySelectorAll(group.sel).forEach((el, idx) => {
          el.classList.add(group.cls);
          if(group.stagger > 0){
            el.style.setProperty('--m-delay', `${(idx % 8) * group.stagger}ms`);
          }
        });
      });

      const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if(entry.isIntersecting){
            entry.target.classList.add('in-view');
            io.unobserve(entry.target);
          }
        });
      }, { threshold: 0.14, rootMargin: '0px 0px -40px 0px' });

      document.querySelectorAll('.m-reveal, .m-reveal-left, .m-reveal-right, .m-reveal-scale').forEach(el => io.observe(el));
    }

    // 2. Scroll Progress Bar, Sticky Glass Header & Parallax Botanical Leaves
    const navLinks = document.querySelectorAll('#menu a[href^="#"]');
    const trackedSections = Array.from(navLinks).map(a => document.querySelector(a.getAttribute('href'))).filter(Boolean);

    function onScrollFrame(){
      const scrollY = window.scrollY || window.pageYOffset;
      const docHeight = document.documentElement.scrollHeight - window.innerHeight;
      if(progressBar && docHeight > 0){
        progressBar.style.width = `${Math.min(100, (scrollY / docHeight) * 100)}%`;
      }
      if(headerEl){
        headerEl.classList.toggle('scrolled', scrollY > 70);
      }
      if(!reducedMotion){
        decoLeaves.forEach((leaf, i) => {
          const rect = leaf.parentElement.getBoundingClientRect();
          const offset = (window.innerHeight * 0.5 - rect.top) * 0.06 * (i % 2 === 0 ? 1 : -1);
          leaf.style.transform = `translateY(${offset.toFixed(1)}px) rotate(${(offset * 0.25).toFixed(1)}deg)`;
        });
      }
      // Active section spy in header menu
      let activeId = '';
      trackedSections.forEach(sec => {
        if(scrollY >= sec.offsetTop - 220){
          activeId = '#' + sec.id;
        }
      });
      if(activeId){
        navLinks.forEach(a => a.classList.toggle('active', a.getAttribute('href') === activeId));
      }
    }
    window.addEventListener('scroll', onScrollFrame, { passive: true });
    onScrollFrame();

    // 3. Animated Pop on Gallery & Video Filter Tabs
    const gFilterBar = document.getElementById('gFilters');
    if(gFilterBar && grid){
      gFilterBar.addEventListener('click', e => {
        if(!e.target.closest('button')) return;
        grid.querySelectorAll('.g-item:not(.hide)').forEach((item, idx) => {
          item.classList.remove('pop-anim');
          void item.offsetWidth;
          item.style.animationDelay = `${idx * 45}ms`;
          item.classList.add('pop-anim');
        });
      });
    }
    if(vFilters && vGrid){
      vFilters.addEventListener('click', e => {
        if(!e.target.closest('button')) return;
        vGrid.querySelectorAll('.v-card:not(.hide)').forEach((card, idx) => {
          card.classList.remove('pop-anim');
          void card.offsetWidth;
          card.style.animationDelay = `${idx * 55}ms`;
          card.classList.add('pop-anim');
        });
      });
    }

    // 4. Subtle Magnetic Hover on Primary CTA Buttons (Desktop)
    if(!reducedMotion && window.matchMedia('(pointer:fine)').matches){
      document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('mousemove', e => {
          const r = btn.getBoundingClientRect();
          const dx = (e.clientX - (r.left + r.width / 2)) * 0.18;
          const dy = (e.clientY - (r.top + r.height / 2)) * 0.22;
          btn.style.transform = `translate(${dx.toFixed(1)}px, ${(dy - 2).toFixed(1)}px)`;
        });
        btn.addEventListener('mouseleave', () => {
          btn.style.transform = '';
        });
      });
    }
  })();
</script>
</body>
</html>
