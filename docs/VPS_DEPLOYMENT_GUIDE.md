# 🚀 VPS Deployment Guide (Localhost → Git → Ubuntu VPS + Caddy On-Demand SSL)

> এই গাইডটি অনুসরণ করে আপনি আপনার লোকালহোস্টের কাজ `git push` করে উবুন্টু VPS-এ লাইভ করতে পারবেন।

---

## 1. Localhost থেকে Git Push করার নিয়ম

লোকালহোস্টে যেকোনো কাজ শেষে:
```bash
git add -A
git commit -m "update: your commit message"
git push origin main
```
*নোট: `.env`, `public/themes/*` এবং `public/module-assets` ইতিমধ্যে `.gitignore`-এ দেওয়া আছে, তাই লোকালহোস্টের সেটিংস বা উইন্ডোজের সিমলিংক কখনোই VPS-এর সাথে কনফ্লিক্ট করবে না।*

---

## 2. Ubuntu VPS-এ প্রথমবার সেটআপ

### 2.1. প্রজেক্ট ক্লোন ও ডিপেন্ডেন্সি ইনস্টল
```bash
cd /var/www
git clone <YOUR_GIT_REPO_URL> multi-website-hub
cd multi-website-hub
composer install --optimize-autoloader --no-dev
cp .env.example .env
php artisan key:generate
```

### 2.2. VPS-এর `.env` কনফিগারেশন
VPS-এর `.env` ফাইলে আপনার আসল ডোমেইন ও MySQL ইউজার (যার `CREATE/DROP DATABASE` পারমিশন আছে) সেট করুন:
```env
APP_NAME="YourHub Cloud"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourhub.com

APP_MODE=saas
CENTRAL_DOMAINS=yourhub.com
DASHBOARD_DOMAIN=app.yourhub.com
SAAS_SERVER_IP=YOUR_VPS_PUBLIC_IP
SAAS_CNAME_TARGET=sites.yourhub.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=multi_website_hub
DB_USERNAME=saas_admin
DB_PASSWORD=YOUR_STRONG_DB_PASSWORD
```

### 2.3. এক কমান্ডে SaaS ডেপ্লয়মেন্ট (`saas:deploy`)
```bash
php artisan saas:deploy --sync-demos
```
এই কমান্ডটি স্বয়ংক্রিয়ভাবে:
1. Central Database-এর সব মাইগ্রেশন রান করবে
2. Linux সার্ভারে `public/themes/*` সিমলিংক তৈরি করবে
3. ১১টি থিমের প্রতিটির জন্য আলাদা Demo Tenant Database (`hub_t_0001` ... `hub_t_0011`) তৈরি ও সিড করবে
4. ক্যাশ অপটিমাইজ করবে।

---

## 3. পরবর্তীতে যেকোনো আপডেট VPS-এ নেওয়ার নিয়ম

লোকালহোস্ট থেকে `git push` করার পর VPS-এ শুধু এই ৩টি লাইন রান করবেন:
```bash
cd /var/www/multi-website-hub
git pull origin main
php artisan saas:deploy
```

---

## 4. Caddy Server (Auto Wildcard Subdomain & Unlimited Custom Domain SSL)

`/etc/caddy/Caddyfile`-এ আমাদের রেডিমেড [docs/Caddyfile.example](file:///c:/laragon/www/multi-website-hub/docs/Caddyfile.example)-এর কনফিগারেশন কপি করুন এবং রিলোড দিন:
```bash
sudo systemctl reload caddy
```
ক্লায়েন্ট যখন ড্যাশবোর্ডে কাস্টম ডোমেইন যোগ করে **Verify DNS** করবে, Caddy স্বয়ংক্রিয়ভাবে `/internal/domain-check` চেক করে ফ্রি Let's Encrypt SSL ইস্যু করবে।

---

## 5. Cron Scheduler (Auto-Expiry, Commission Release & Nightly Per-Tenant Backups)

VPS-এ `crontab -e` খুলে এই এক লাইন যোগ করুন:
```cron
* * * * * cd /var/www/multi-website-hub && php artisan schedule:run >> /dev/null 2>&1
```
এটি প্রতিদিন স্বয়ংক্রিয়ভাবে:
- `billing:check-expirations` (মেয়াদোত্তীর্ণ ট্রায়াল/সাবস্ক্রিপশন সাসপেন্ড)
- `resellers:release-commissions` (রিসেলারের ৭ দিন পূর্ণ হওয়া কমিশন ওয়ালেটে রিলিজ)
- `tenants:cleanup-unverified` (৪৮ ঘণ্টার পুরনো অভেরিফাইড সাইনআপ ক্লিনআপ)
- `tenants:backup` (রাত ২:৩০-এ প্রতিটা Tenant DB-র আলাদা ব্যাকআপ ও রোটেশন) চালাবে।
