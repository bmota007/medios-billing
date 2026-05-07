# MediosBilling Master Roadmap
Updated: April 2026

---

# CORE MISSION

Build the most powerful SMB billing / quoting / collections / business command center SaaS.

Target industries:

- Cleaning
- Painting
- Contractors
- Roofing
- Landscaping
- Churches
- Local services
- Agencies
- Freelancers
- Multi-location companies

---

# PHASE 1 — FOUNDATION LOCKDOWN

## Security

- Role middleware complete
- Route permission audit
- Sidebar by role
- Force password change
- Login rate limiting
- Super admin bypass logic

## Backup Policy

Before major edits:

backupmedios

---

# PHASE 2 — DASHBOARD V3

## Build New Modular Dashboard

Files:

resources/views/dashboard/
- index.blade.php
- hero.blade.php
- stats.blade.php
- revenue-chart.blade.php
- quick-actions.blade.php
- recent-invoices.blade.php
- ai-coach.blade.php
- tax-widget.blade.php

## Features

- Role-aware widgets
- Light / Dark mode
- Auto mode by time
- Mobile responsive
- Add-on ready

---

# PHASE 3 — THEME ENGINE

Database:

users.theme_mode

Values:

- auto
- light
- dark

Logic:

7am–6pm = light  
6pm–7am = dark

Manual override allowed.

---

# PHASE 4 — TAX CENTER

## MVP

- Revenue by year
- Paid invoices
- Pending invoices
- CSV export
- PDF tax report
- Monthly totals

## Later

- Expense tracking
- Accountant login
- 1099 reports
- Sales tax tools

---

# PHASE 5 — AI BUSINESS COACH

Using OpenAI API

## Suggestions

- overdue invoices
- low revenue alerts
- quote conversion tips
- top customers
- monthly strategy

## Later

- voice AI owner assistant
- smart reminders
- pricing recommendations

---

# PHASE 6 — PROOF & SECURITY CENTER

Track:

- invoice sent time
- email opened
- invoice viewed IP
- device/browser
- quote signed IP
- payment timestamps
- user actions

Purpose:

- chargeback defense
- fraud reduction
- proof of service

---

# PHASE 7 — CUSTOMER EXPERIENCE

- better payment portal
- one click pay
- saved cards
- payment plans
- customer login portal
- branded portal

---

# PHASE 8 — PLAN TIERS

## Starter

- invoices
- quotes
- customers
- 1 user
- basic dashboard

## Growth

- team users
- branding
- reminders
- reports
- tax center basic

## Pro

- unlimited users
- AI coach
- security center
- advanced analytics
- recurring invoices

## Elite

- white label
- multi-location
- custom workflows
- priority support
- advanced AI

---

# PHASE 9 — INDUSTRY PACKS

Templates for:

- cleaning companies
- painters
- roofing
- pressure washing
- church donations
- law firms
- consultants

---

# PHASE 10 — FUTURE EXPANSION

- mobile apps
- client texting center
- GPS field proof
- payroll sync
- QuickBooks sync
- Zapier
- Stripe Connect marketplace
- franchise mode

---

# BUILD RULES

1. Full files only
2. Backups before edits
3. No blind edits
4. Prototype first
5. Live deploy second
6. Test super admin + tenant users

---

# CURRENT PRIORITY ORDER

1. Dashboard V3 prototype
2. Theme engine
3. Tax Center
4. AI Coach
5. Proof Center
6. Plan Upsells
