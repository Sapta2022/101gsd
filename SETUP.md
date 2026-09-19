# 101GSD — Git & deployment setup

One-time setup to get from "empty GitHub repo" to "push to `staging` or
`main` and the server updates itself." Follow it in order; each step says
how to check it actually worked before moving on.

## How the three branches work

| Branch | Runs on | How it updates |
| --- | --- | --- |
| `local` | Your machine, via WAMP | You just run it — `git checkout local`, no deploy |
| `staging` | `staging.101gsd.in` on cPanel | Auto: push to `staging` → GitHub Actions builds and deploys |
| `main` | `101gsd.in` (live) on cPanel | Auto, but paused for your approval — push to `main` → GitHub Actions builds, waits for you to click **Approve**, then deploys |

Day to day: you branch off `local` for a feature or phase, open a PR into
`local`, merge. When a batch of work is ready to show, merge `local` into
`staging` (a PR or a fast-forward merge — either works) and the staging site
updates itself within a couple of minutes. Once it's checked and approved,
merge `staging` into `main` the same way, click **Approve** on the run in
the **Actions** tab, and the live site updates.

`local` itself is never auto-deployed anywhere — it's just the working
branch on GitHub that your machine's `git pull`/`git push` talks to; WAMP
runs whatever's checked out in your own working copy.

---

## 1. Create the Laravel project (on your machine, WAMP)

```bash
cd C:\wamp64\www
composer create-project laravel/laravel 101gsd "^13.0"
cd 101gsd
```

Point WAMP's vhost or just use `http://localhost/101gsd/public` for now.
Copy `.env.example` from this kit over the one Laravel generated (it has the
Razorpay/SMS placeholders already), fill in your local DB name, then:

```bash
php artisan key:generate
php artisan migrate
```

**Check:** `http://localhost/101gsd/public` loads the Laravel welcome page.

## 2. Add this kit to the project

Copy everything from this kit into the project root, on top of what
`composer create-project` generated (it won't collide — Laravel's own
`.gitignore`/`.gitattributes` get replaced with the versions here, which
are the same file plus the deploy-specific additions):

```
101gsd/
├── .env.example / .env.staging.example / .env.live.example / .env.ci
├── .gitattributes
├── .gitignore
├── .github/
│   ├── workflows/{ci,deploy-staging,deploy-live}.yml
│   ├── scripts/call-deploy-hook.sh
│   ├── ISSUE_TEMPLATE/phase.yml
│   └── pull_request_template.md
├── deploy/
│   ├── hook.php
│   ├── README.md
│   ├── deploy-hook.config.example.php
│   └── public-entrypoint.example.php
├── scripts/generate-deploy-secret.sh
└── SETUP.md   (this file)
```

## 3. Initialise git and push to GitHub

```bash
git init -b local
git add .
git commit -m "Initial Laravel 13 setup with CI/CD kit"
```

Create an empty repository on GitHub (no README/.gitignore — you already
have them), then:

```bash
git remote add origin https://github.com/<you>/101gsd.git
git push -u origin local
git branch staging
git push -u origin staging
git branch main
git push -u origin main
```

**Check:** GitHub shows three branches, all identical right now.

## 4. Protect the branches

Repo → **Settings → Branches → Add branch ruleset** (or classic **Branch
protection rules**, either works):

- `main`: require a pull request before merging, require the **CI** status
  check to pass, do not allow force pushes.
- `staging`: same, PR + required **CI** check.
- `local`: optional — up to you; many solo setups skip protection here.

**Check:** try pushing directly to `main` from your machine — it should be
rejected once this is on.

## 5. Protect the live environment (manual approval gate)

Repo → **Settings → Environments → New environment** → name it exactly
`production` (the `deploy-live.yml` workflow already targets this name) →
**Required reviewers** → add yourself.

Create a second environment named `staging` too (no required reviewer needed
— it's referenced by `deploy-staging.yml` and lets you scope staging's
secrets separately from production's in the next step).

**Check:** both environments show up under **Settings → Environments**.

## 6. cPanel: folder layout

Create this layout once, by hand, in cPanel's **File Manager** (or via FTP).
The key idea: each environment's Laravel app lives *outside* the folder the
subdomain serves, and only `public/` is exposed.

```
/home/<cpanel-user>/
├── staging_app/            <- staging deploys land here (server-dir secret)
│   ├── app/ bootstrap/ config/ ... vendor/
│   ├── deploy/
│   │   └── deploy-hook.config.php     <- you create this, step 9
│   └── public/
│       ├── index.php
│       └── dh-<random>.php            <- you create this, step 9
├── live_app/                <- live deploys land here
│   └── (same shape)
└── public_html/             <- leave alone / your existing site, if any
```

In cPanel → **Domains** (or **Subdomains**), create `staging.101gsd.in` and
point its **document root** to `staging_app/public`. Do the same for
`101gsd.in` → `live_app/public` (or point your existing domain's docroot
there when you're ready to cut over).

**Check:** visiting `staging.101gsd.in` shows a 500 or blank page right
now — expected, nothing's deployed yet. A 404 "domain not found" means the
docroot mapping didn't take; re-check that step.

## 7. cPanel: databases

**MySQL Databases** in cPanel, once per environment:

- Create database + a dedicated user for it + attach with **All Privileges**
- Do this twice: one DB/user pair for staging, one for live — never share
  a database between them.

**Check:** note down both database names, usernames and passwords — they go
into `.env` in step 9.

## 8. cPanel: FTP account scoped to each folder

**FTP Accounts** in cPanel, once per environment:

- Create an FTP user whose **home directory** is `staging_app/` (not the
  whole cPanel account) — this limits what a leaked FTP password can reach.
- Same for `live_app/`.

**Check:** log in with an FTP client (FileZilla, WinSCP) using each account
and confirm you land directly inside an empty `staging_app`/`live_app`, not
at the account root.

## 9. Server-side one-time files (per environment)

Do this for **staging** first, then repeat for **live** later.

1. Generate a deploy secret on your machine:
   ```bash
   bash scripts/generate-deploy-secret.sh
   ```
2. Upload `deploy/deploy-hook.config.example.php` to
   `staging_app/deploy/deploy-hook.config.php` (rename on upload), edit it
   in place (cPanel File Manager's editor, or edit locally and re-upload)
   to paste in the secret from step 1.
3. Upload `deploy/public-entrypoint.example.php` to
   `staging_app/public/dh-<pick-your-own-random-string>.php` — pick your
   own name, don't reuse the example in this guide. Write the final name
   down; it goes into a GitHub secret in step 10.
4. Copy `.env.staging.example` to `staging_app/.env`, fill in: the staging
   DB credentials from step 7, `APP_URL=https://staging.101gsd.in`, and
   leave `APP_KEY` blank for now.
5. Generate an app key **locally** and paste it in (there's no SSH to run
   `artisan` on the server itself):
   ```bash
   php artisan key:generate --show
   ```
   Paste the `base64:...` value as `staging_app/.env`'s `APP_KEY`.

**Check:** `staging_app/.env` exists with a real `APP_KEY`, real DB
credentials, and `APP_URL` set — but the app itself isn't deployed yet, so
there's nothing to browse to yet.

## 10. GitHub secrets

Repo → **Settings → Environments → staging → Environment secrets** (keeps
staging's secrets out of live's environment, and vice versa):

| Secret | Value |
| --- | --- |
| `STAGING_FTP_HOST` | cPanel's FTP hostname (e.g. `ftp.101gsd.in`) |
| `STAGING_FTP_USERNAME` | the FTP account from step 8 |
| `STAGING_FTP_PASSWORD` | its password |
| `STAGING_FTP_SERVER_DIR` | `./` (since that FTP account's home *is* `staging_app/`) |
| `STAGING_DEPLOY_HOOK_URL` | `https://staging.101gsd.in/dh-<your-name>.php` |
| `STAGING_DEPLOY_HOOK_SECRET` | the secret from step 9.1 |

Then **Settings → Environments → production → Environment secrets**, same
six keys with the `LIVE_` prefix instead, pointing at `live_app` / `101gsd.in`
— you'll fill these in when you repeat step 9 for live, later.

**Check:** each environment's secrets list shows exactly its six entries —
`gh secret list` or the Settings UI both confirm names (values are never
shown back).

## 11. First deploy to staging

```bash
git checkout staging
git merge local
git push
```

Watch **Actions** tab → the `Deploy to staging` run. It builds, uploads over
FTPS, then calls the deploy hook.

**Check, in order:**
1. The **Actions** run is green end to end.
2. `staging_app/vendor/` and `staging_app/public/build/` now exist (FTP in
   and look, or check the run's log for the upload step's file count).
3. `https://staging.101gsd.in` loads the Laravel welcome page.
4. The **Trigger post-deploy Artisan tasks** step's log shows
   `"ok": true` with all nine steps (`down`, `migrate`, `config:cache`,
   `route:cache`, `view:cache`, `event:cache`, `storage:link`,
   `queue:restart`, `up`) each with `"exit": 0`.

If step 4 shows `"ok": false`, the failing step's `"output"` in that same
JSON is Artisan's own error message — that's almost always enough to fix it
directly (a bad DB credential shows here immediately, for instance).

## 12. Cron (the scheduler)

cPanel → **Cron Jobs**, add one line (adjust the path to match your cPanel
username):

```
* * * * * php /home/<cpanel-user>/staging_app/artisan schedule:run >> /dev/null 2>&1
```

**Check:** `storage/framework/schedule-*` or your log channel shows the
scheduler firing within a couple of minutes of adding it (Laravel logs
scheduled runs if you've registered anything in `routes/console.php` yet —
otherwise this is a no-op until phase 4/9 add the show reminders).

## 13. Repeat steps 6-12 for live

Same steps, `live_app` instead of `staging_app`, `101gsd.in` instead of
`staging.101gsd.in`, `LIVE_*` secrets instead of `STAGING_*`, and — the one
real difference — **live Razorpay keys**, not test keys, in `.env`.

Don't push to `main` until you're actually ready to go live: the workflow
will deploy whatever's on that branch the moment you approve it.

## 14. Day-to-day phase workflow

```bash
git checkout local
git pull
git checkout -b phase-4-show-setup
# ... work, commit ...
git push -u origin phase-4-show-setup
# open a PR into local on GitHub, using the PR template; merge once CI is green
```

Open one GitHub Issue per SOW phase using the **SOW phase** issue template
(`.github/ISSUE_TEMPLATE/phase.yml`) so each phase has a single place
tracking its scope and acceptance criteria; link PRs to it as you go.

When a phase (or a batch of commits) is ready to be seen:

```bash
git checkout staging
git merge local
git push        # staging redeploys automatically
```

When staging looks right and you're ready to ship it:

```bash
git checkout main
git merge staging
git push        # waits in Actions for your Approve, then deploys live
```

## 15. Rolling back a bad deploy

```bash
git checkout main            # or staging
git revert <bad-commit-sha>  # or: git reset --hard <last-good-sha> && git push --force-with-lease
git push
```

Either push re-triggers the deploy workflow with the older code. See
`deploy/README.md` for the one wrinkle: a migration that already ran on the
server doesn't automatically reverse itself just because the code did — if
the bad deploy included a schema change, roll that back by hand first
(there's no SSH to run `migrate:rollback` remotely; either add a temporary
Artisan command bound to the hook's fixed step list, or make the correction
via phpMyAdmin for a one-off fix).

## Troubleshooting

| Symptom | Likely cause |
| --- | --- |
| Actions run fails at "Install PHP dependencies" | `composer.lock` out of date — run `composer update` locally, commit the lock file |
| FTP upload step fails with auth error | FTP account password wrong in the GitHub secret, or the account's home directory isn't what `STAGING_FTP_SERVER_DIR`/`LIVE_FTP_SERVER_DIR` assumes |
| Deploy hook step gets HTTP 401 | Wrong secret, or the public entrypoint file (`dh-<name>.php`) wasn't actually renamed/uploaded on the server, or `deploy-hook.config.php` is missing |
| Deploy hook step gets HTTP 500, `"error": "deploy hook is misconfigured"` | `app_path` in `deploy-hook.config.php` doesn't point at the folder that actually has `vendor/` and `bootstrap/app.php` |
| Site shows "Service Unavailable" after a deploy | A step in the hook's sequence failed before reaching `up` — check the Action run's log for which one and its `output` |
| `https://staging.101gsd.in` 404s entirely | Subdomain's document root isn't pointed at `staging_app/public` |
