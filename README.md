# JobBridge — Job Portal Website

A modern, responsive job portal built with **PHP, MySQL, HTML, CSS and JavaScript**. Candidates can explore openings, filter jobs, apply and view their application status. Employers can publish vacancies, edit/close listings, view applicants and update application statuses.

> **Portfolio/educational demo only.** This project is not a production recruiting platform. Use fictional personal information when demonstrating it.

## Features

### Candidate
- Register and sign in as a candidate
- Browse job listings and search by keyword, company, location, category and job type
- View full job descriptions, salaries and work arrangements
- Apply once per job with an optional cover note
- View personal application history and statuses

### Employer
- Register and sign in as an employer
- Post new job openings with salaries and requirements
- Edit listings, mark them closed or reopen them
- Review applicants **only for your own listings**
- Change application status: Submitted, Reviewed, Shortlisted, Rejected
- View employer dashboard counters

### Technical
- Prepared SQL queries via PDO; password hashing with PHP `password_hash()`
- Access control for candidate and employer routes
- CSRF protection for forms; HTML escaping for submitted content
- MySQL relational database with unique constraints and foreign keys
- Modern responsive interface, without external JavaScript frameworks

## Requirements

- Windows with XAMPP (Apache + MySQL), or another PHP/MySQL environment
- PHP 8.1+ with PDO MySQL enabled
- MySQL 8+ or compatible MariaDB
- A web browser

No `pip install`, `npm install` or Composer packages are required.

## Run on Windows with XAMPP

1. Install XAMPP from https://www.apachefriends.org/ (if you have it already, skip this step).
2. Open **XAMPP Control Panel** and click **Start** next to **Apache** and **MySQL**.
3. Extract this project folder to `C:\xampp\htdocs\jobbridge`. Ensure `index.php` is at `C:\xampp\htdocs\jobbridge\index.php` (not inside a second nested folder).
4. In your browser open **http://localhost/phpmyadmin**.
5. Select the **Import** tab, choose `database.sql`, and click **Import**. This script creates the `jobbridge` database and its 3 tables (`users`, `jobs`, `applications`).
6. Create a local `config.php` by copying `config.example.php` and entering your MySQL settings (`root` / empty password, host `127.0.0.1`). If your MySQL configuration is different, update `config.php`. Alternatively copy `config.example.php` to `config.php` and edit it.
7. Visit **http://localhost/jobbridge/**.

If Apache starts on a different port, use `http://localhost:PORT/jobbridge/`.

**Note:** XAMPP can come with PHP/MySQL/MariaDB versions other than those stated above. Verify that your installation is compatible.

## Quick functional test

1. Register as an **Employer** using a dummy email like `recruiter@example.test` and a test password.
2. Open **Post a job**, create a sample listing: `Junior Python Developer` at `Example Labs`, `Delhi, India`, `Technology`, `Internship`, `Hybrid`, monthly salary ₹15,000–₹25,000, with a description longer than 30 characters.
3. Log out. Register as a **Candidate** with a different dummy email such as `candidate@example.test`.
4. Go to **Explore jobs**, find your sample role and apply with a short cover note.
5. Open **My applications** and check the application is shown.
6. Log out and sign in to your employer account. Go to **My listings → Applicants**, and change the application status to **Shortlisted**.
7. Log back in as the candidate; the application status should show **Shortlisted**.

## Project layout

```text
jobbridge/
├── assets/
│   ├── app.js
│   └── style.css
├── includes/
│   ├── bootstrap.php
│   ├── footer.php
│   ├── header.php
│   ├── job_card.php
│   └── job_form.php
├── index.php                 # Home
├── jobs.php                  # Browse and filter jobs
├── job.php                   # Job details
├── register.php              # Registration
├── login.php                 # Login
├── logout.php                # Logout
├── dashboard.php             # Role-specific dashboard
├── post_job.php              # Employer creates a job
├── manage_jobs.php           # Employer listings
├── edit_job.php              # Employer edits a job
├── close_job.php             # Employer closes a job
├── applicants.php            # Employer sees own applicants
├── update_application.php    # Employer changes application status
├── apply.php                 # Candidate submits application
├── my_applications.php       # Candidate application history
├── database.sql              # Tables and constraints
├── config.example.php        # Database configuration template
├── config.php                # Local settings; ignored by Git
├── .gitignore
├── LICENSE
└── README.md
```

## Common troubleshooting

- **Database connection failed:** Start MySQL, import `database.sql`, and review `config.php` (host, port, name, user, password).
- **404 error:** Make sure the project folder is in `htdocs`, and use `http://localhost/jobbridge/`.
- **PHP displayed as text / downloaded:** Open through Apache at `localhost`, not by double-clicking `index.php`.
- **Apache port in use:** Check XAMPP Control Panel for the actual port (80/8080); visit that port.
- **Unknown database:** Import `database.sql` via phpMyAdmin before running the site.
- **Headers already sent:** Don't add output before the initial `<?php` lines in PHP files.
- **No listings:** Register as an employer and post the first opening.

## Upload to GitHub

Create a repository such as `JobBridge-Job-Portal`, then upload the project files. **Do not upload `config.php` if it contains real credentials**. The provided `.gitignore` ignores it for Git command-line pushes; the `config.example.php` can safely be shared with placeholder/local-only values. Replace the license copyright holder with your own name if desired.

Suggested GitHub description:

> A responsive job portal built with PHP and MySQL, featuring candidate and employer accounts, job search, postings, applications, dashboards and secure role-based access.

## Deployment notes

**Netlify and Cloudflare Pages cannot run this PHP/MySQL project as-is.** A PHP-capable hosting provider with MySQL is required. Configure the database and web server, store secrets outside the public web root or use environment variables, enforce HTTPS, and take regular backups.

Before allowing real users, add password-reset/email verification, login rate limiting, anti-spam protections, a privacy policy, secure secret management, monitoring/backups, and full security testing. Do not treat the current version as production-ready.

## License

MIT (see `LICENSE`).
