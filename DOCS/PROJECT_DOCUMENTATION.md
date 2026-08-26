# 📚 Rudood AI Platform — Comprehensive Project Documentation

> **Version:** 2.0 Enterprise  
> **Framework:** Laravel 11.x (PHP 8.4 / 8.2)  
> **Database:** PostgreSQL 16 with `pgvector` & SQLite  
> **Design Language:** Modern Dark Luxury & Gold Glassmorphism (Bootstrap 5.3 RTL)  

---

## 📑 Table of Contents

1. [Executive Summary & Value Proposition](#1-executive-summary--value-proposition)
2. [High-Level System Architecture](#2-high-level-system-architecture)
3. [Technology Stack](#3-technology-stack)
4. [Complete Feature Breakdown](#4-complete-feature-breakdown)
   - [4.1 Public Portal & Interactive Live Demo](#41-public-portal--interactive-live-demo)
   - [4.2 Merchant Store Dashboard & Live Chat Inbox](#42-merchant-store-dashboard--live-chat-inbox)
   - [4.3 Omni-Channel Management Hub (4 Channels)](#43-omni-channel-management-hub-4-channels)
   - [4.4 AI Engine, Semantic RAG & Auto-Rules](#44-ai-engine-semantic-rag--auto-rules)
   - [4.5 AI Playground & Prompt Workbench](#45-ai-playground--prompt-workbench)
   - [4.6 Super Admin Command Center & Audit Trail](#46-super-admin-command-center--audit-trail)
   - [4.7 Contact Us Inquiries Management System](#47-contact-us-inquiries-management-system)
5. [Database Architecture & Schema](#5-database-architecture--schema)
6. [Webhooks & Real-Time Ingestion](#6-webhooks--real-time-ingestion)
7. [Docker & Containerized Infrastructure](#7-docker--containerized-infrastructure)
8. [Automated Testing & Verification Suite](#8-automated-testing--verification-suite)

---

## 1. Executive Summary & Value Proposition

**Rudood (ردود)** is a state-of-the-art AI customer service and sales automation platform tailored for modern e-commerce merchants and enterprise stores worldwide. It replaces slow, costly human support queues with autonomous AI agents that understand Arabic and global languages across diverse dialects, answer customer queries within milliseconds, recommend products, and escalate complex or frustrated conversations to human agents seamlessly.

### Core Value Drivers:
- **Instant Response Times**: Reduces first-response latency from hours to < 1.2 seconds.
- **70%+ Support Cost Reduction**: Resolves 85–94% of repetitive pre-sale and after-sale inquiries without human intervention.
- **Unified Omni-Channel Inbox**: Consolidates conversations from WhatsApp, Telegram, Instagram, and web store widgets into one synchronized dashboard.
- **Local RAG & Guardrails**: Keeps AI grounded in the merchant’s uploaded documents and store policies without hallucinating.

---

## 2. High-Level System Architecture

```
                                  [ CUSTOMERS & CHANNELS ]
                                             │
      ┌──────────────────┬───────────────────┼──────────────────┬──────────────────┐
      ▼                  ▼                   ▼                  ▼                  ▼
 WhatsApp Cloud    Telegram Bot      Instagram Direct     Web Live Widget     Contact Form
      │                  │                   │                  │                  │
      └──────────────────┼───────────────────┴──────────────────┘                  │
                         ▼                                                         ▼
                 [ Webhook Router ]                                      [ Contact Controller ]
                         │                                                         │
                         ▼                                                         ▼
           [ ProcessCustomerMessage Job ]                               [ ContactMessage DB ]
                         │                                                         │
         ┌───────────────┴───────────────┐                                         ▼
         ▼                               ▼                              [ Super Admin Inbox ]
  [ Tier 1: Auto-Rule ]          [ Tier 2: RAG Engine ]
  (Instant Keyword Match)        (Semantic Chunk Match)
         │                               │
         ├───────────────┬───────────────┘
         │               ▼
         │       [ Tier 3: LLM Call ]
         │       (Gemini / OpenAI / Claude)
         │               │
         ▼               ▼
      [ Response Dispatcher & Decision Log ]
                         │
                         ▼
        [ Customer Reply + Merchant Dashboard ]
```

---

## 3. Technology Stack

### Backend Core
- **Framework**: Laravel 11.x
- **Language**: PHP 8.4 / PHP 8.2+
- **Architectural Patterns**: Service-Oriented Architecture (`AiService`, `RagService`, `AdminStatsService`), Repository/Eloquent models, Asynchronous Queue Workers.
- **Security**: Granular Role-Based Access Control (`super_admin`, `owner`, `agent`), Rate Limiting, Atomic Registration, Session Impersonation, Webhook Verification Tokens.

### Database & Storage
- **Production DB**: PostgreSQL 16 with `pgvector` extension for native vector embedding search.
- **Development DB**: SQLite 3 with full migration parity.
- **Caching & Queues**: Redis Alpine for background jobs (`ai-processing`), session storage, and rate-limiting.

### Real-Time Infrastructure
- **WebSockets**: Standalone Node.js WebSocket server (`backend/websocket/server.js`) on port 3000.
- **Long-Polling**: Built-in `php artisan telegram:poll` daemon for local development without public HTTPS webhooks.

### Frontend & UI System
- **Template Engine**: Laravel Blade with shared layout partials (`theme.blade.php`, `breadcrumbs.blade.php`).
- **Styling**: Vanilla CSS3 + Bootstrap 5.3 RTL + Custom Glassmorphism (`mystyle.css`).
- **Typography**: Google Fonts `Cairo` (400–900) & `Reem Kufi` (700/900).
- **Client Components**:
  - `widget.js`: Zero-dependency, lightweight embeddable live chat widget.
  - `landing-animations.js`: HTML5 Canvas floating particles and dynamic typography animations.
  - `marked.js`: Client-side Markdown parser for rich text bot messages.

---

## 4. Complete Feature Breakdown

### 4.1 Public Portal & Interactive Live Demo
- **Modern Landing Page (`/index`)**: High-converting dark/gold aesthetic with animated typography, feature showcases, customer trust badges, and interactive pricing plans.
- **Live Case Study Simulator (`/demo`)**:
  - Simulates a real e-commerce store dashboard ("متجر النخبة للعطور") for prospective buyers without requiring registration.
  - Interactive multi-channel tabs: **WhatsApp** (سارة العتيبي), **Telegram** (فهد الشمري), **Instagram** (ريم الدوسري), **Web Widget** (نورة القحطاني), and **Escalation Case** (خالد الحربي).
  - Real-time simulated AI replies, customer CRM side drawer, and human takeover toggle with sound effects.
- **Public Informational Pages**: Features (`/features`), Pricing tiers (`/pricing`), and dynamic Blog (`/blog`, `/blog/{slug}`).

---

### 4.2 Merchant Store Dashboard & Live Chat Inbox
- **Analytics Dashboard (`/dashboard`)**:
  - Live metric cards: Total Conversations, AI Automated Resolution Rate (%), Active Integrated Channels, Average Response Speed (< 1.2s).
  - 14-day interactive chart of customer engagement volume.
  - Real-time customer activity stream.
- **Multi-Channel Live Inbox (`/live-chat`)**:
  - Split-view layout: Active chat sessions list on the right, active message stream in the center, customer CRM details on the left.
  - **Human Takeover Mode**: One-click button (`إيقاف البوت / استئناف البوت`) to pause the bot on a specific conversation for manual agent intervention.
  - **Canned Slash Replies**: Instant pre-saved snippets (`/welcome`, `/shipping`, `/pricing`).
  - **Internal CRM Notes & Tags**: Agents can add private notes and tags (VIP, Interested, Returning) to any conversation.
  - **CSV Export**: Streamed download of historical chat transcripts.

---

### 4.3 Omni-Channel Management Hub (4 Channels)
Located at **`/channels`**, this hub provides centralized control over all store communications:
1. **WhatsApp Cloud API**:
   - Ingests Meta webhook payloads (`messages`, `statuses`).
   - Dispatches outgoing template and session messages via Meta Graph API v19.0.
2. **Telegram Bot API**:
   - Supports both webhook delivery and local long-polling (`php artisan telegram:poll`).
   - Live **«فحص الاتصال»** button verifies token connectivity against Telegram servers.
3. **Web Live Chat Widget**:
   - Generates a customized 1-line `<script>` tag for external stores (Salla, Zid, Shopify, WooCommerce).
   - Live in-page color and branding customizer.
4. **Instagram Direct & Comments**:
   - Auto-replies to Instagram Direct Messages.
   - Automatically replies to public post comments and sends private follow-up DMs.
- **Global Channel Toggles**: 1-click ON/OFF toggle switch for every channel to activate or suspend automation without losing credentials.

---

### 4.4 AI Engine, Semantic RAG & Auto-Rules
A multi-tier fallback architecture ensures accuracy, speed, and reliability:
- **Tier 1 (Instant Auto-Rules)**:
  - Scans incoming messages for keyword triggers.
  - Returns exact merchant-defined responses instantly (0ms latency, zero API token cost).
- **Tier 2 (Semantic RAG Retrieval)**:
  - Documents (PDF, DOCX, TXT) uploaded in `/ai-manage` are parsed and split into semantic text chunks cached in the database.
  - Searches for chunk overlap and injects relevant context into the LLM system prompt.
- **Tier 3 (Direct LLM Generation)**:
  - Connects to Google Gemini, OpenAI, Claude, or OpenAI-Compatible endpoints.
  - Dynamic system prompts incorporate merchant tone (`formal`, `friendly`, `sales`).
- **AI FAQ Extraction**: Automatically reads uploaded store manuals and extracts 5–10 structured Q&A pairs with keywords.
- **Sentiment & Urgency Analyzer**: Detects frustration, anger, or urgency keywords and automatically flags conversations for human supervisor escalation.

---

### 4.5 AI Playground & Prompt Workbench
Located at **`/playground`**, this workbench lets merchants simulate and tune bot behavior:
- **Real-Time Latency Tracking**: Displays exact round-trip response time in milliseconds.
- **RAG Inspector**: Visualizes retrieved document chunks with similarity percentage bars.
- **Dynamic Model Fetcher**: Queries active provider endpoints to load available models.
- **Save as Defaults**: Persists tested temperature, model, and system prompt directly to the store bot.
- **Diagnostic Error Notice**: When fallback occurs, displays a detailed banner explaining API issues (e.g. invalid key or rate limit).

---

### 4.6 Super Admin Command Center & Audit Trail
Located at **`/admin/*`**, accessible exclusively to users with `role: super_admin`:
- **Admin Dashboard & Statistics (`/admin/dashboard`, `/admin/statistics`)**:
  - Platform-wide KPIs: Total registered workspaces, active subscriptions, total messages, AI provider distribution.
  - 15-minute live stream telemetry polling (`/admin/statistics/live`).
- **Workspace & Store Management (`/admin/workspaces`)**:
  - Create new stores, upgrade/downgrade subscription plans, update bot parameters.
  - **Impersonation**: Admin can log into any merchant store session with one click and return safely via `leaveImpersonation()`.
- **User & Merchant Management (`/admin/users`)**: Search, filter, edit, and reset passwords for store owners.
- **Blog & Article CMS (`/admin/articles`)**: Rich content editor, auto-slug generator, category filtering, and publication toggle.
- **Enterprise Audit Trail (`/admin/audit-logs`)**: Immutable logging of all sensitive administrative actions, bot adjustments, and human takeovers with styled pagination.
- **System Health & Infrastructure (`/admin/system`)**: Real-time database table row counts, size metrics (PostgreSQL/SQLite), and Redis health.

---

### 4.7 Contact Us Inquiries Management System
- **Public Form (`/contact`)**: Allows visitors to submit inquiries with automatic validation and CSRF protection.
- **Admin Inquiries Inbox (`/admin/contacts`)**:
  - Live KPI cards: Total Inquiries, New (Unread), In Progress, Resolved.
  - Keyword search and date/status filters.
  - Inspection modal displaying full message text, sender IP, and timestamp.
  - Status updater (`new` ➔ `in_progress` ➔ `resolved`) with internal admin notes.
  - One-click **«الرد عبر البريد الإلكتروني»** pre-filling `mailto:` with subject line.
  - Unread badge counter in the admin sidebar navigation.

---

## 5. Database Architecture & Schema

### Core Tables & Models:
1. `workspaces`: Multi-tenant tenant accounts with quota limits (`plan_id`, `monthly_messages_quota`, `status`).
2. `users`: Authentication records (`email`, `password`, `role`: `super_admin`/`owner`/`agent`, `workspace_id`).
3. `bots`: AI configuration per workspace (`ai_provider`, `model_type`, `temperature`, `system_prompt`, `welcome_message`, `is_active`).
4. `auto_rules`: Fast keyword trigger rules (`keywords`, `trigger_condition`, `reply_template`, `is_active`).
5. `knowledge_bases`: Uploaded documents (`file_name`, `document_text`, `chunks_json`).
6. `channels`: Connected messaging channels (`platform`, `is_connected`, `is_active`, `credentials_json`).
7. `customers`: End-user customer profiles (`name`, `phone`, `chat_id`, `crm_notes`, `tags`).
8. `conversations`: Chat sessions (`platform`, `status`, `is_bot_active`, `unread_count`, `last_message_at`).
9. `messages`: Individual chat messages (`sender_type`: `customer`/`bot`/`agent`, `message_text`, `is_read`).
10. `canned_replies`: Slash shortcuts (`shortcut`, `title`, `message_body`).
11. `contact_messages`: Public contact inquiries (`name`, `email`, `subject`, `message`, `status`, `admin_notes`).
12. `articles`: Blog publications (`title`, `slug`, `content`, `category`, `is_published`).
13. `audit_logs`: Immutable security trail (`user_id`, `action`, `description`, `metadata_json`).
14. `ai_decision_logs`: Telemetry logs (`trigger`, `ai_provider`, `response_time_ms`, `customer_message`, `bot_reply`).

---

## 6. Webhooks & Real-Time Ingestion

| Endpoint | Method | Purpose |
| :--- | :--- | :--- |
| `/api/webhook/telegram/{workspaceId}` | POST | Ingests Telegram Bot incoming messages and updates |
| `/api/webhook/whatsapp` | GET / POST | Meta webhook verification (GET) and incoming WhatsApp message ingestion (POST) |
| `/api/webhook/instagram` | GET / POST | Meta webhook verification (GET) and Instagram DM / comment ingestion (POST) |
| `/api/widget/config` | GET | Delivers live widget styling and greeting configuration |
| `/api/widget/send` | POST | Ingests website widget chat messages and returns AI response |

---

## 7. Docker & Containerized Infrastructure

The platform includes a production-grade multi-container Docker Compose setup:

```
┌─────────────────────────────────────────────────────────────┐
│                    DOCKER COMPOSE STACK                     │
├─────────────────┬─────────────────┬─────────────────────────┤
│ Service         │ Image           │ Port Mapping            │
├─────────────────┼─────────────────┼─────────────────────────┤
│ app             │ PHP 8.4 Alpine  │ 8000:80 (Nginx + FPM)   │
│ websocket       │ Node.js 20      │ 3000:3000 (Socket.IO)   │
│ postgres        │ pgvector:pg16   │ 5432:5432 (Postgres 16) │
│ redis           │ Redis Alpine    │ 6379:6379 (Cache/Queue) │
└─────────────────┴─────────────────┴─────────────────────────┘
```

---

## 8. Automated Testing & Verification Suite

The repository features an integrated automated test suite executable via:
```bash
php tests_suite_runner.php
```

### Test Suite Coverage (74 Tests, 100% Pass Rate):
- **Suite 1: Auth & Multi-Tenancy (6 tests)**: Super Admin roles, Owner isolation, Atomic registration, Impersonation flow.
- **Suite 2: Super Admin Command Center (11 tests)**: KPIs, Telemetry stream, Workspace CRUD, User password reset, Articles, System stats, Audit logs, Contact message CRUD & status update.
- **Suite 3: Store Dashboard & Live Chat (7 tests)**: Metrics calculation, Inbox rendering, Human takeover toggle, Canned replies, CRM notes, CSV export.
- **Suite 4: AI & RAG Engine (8 tests)**: Model fetching, Document chunking, Semantic keyword scoring, Auto-rule matching, FAQ extraction, Sentiment analysis.
- **Suite 5: AI Playground (3 tests)**: Simulator interface, Latency measurement, Parameter persistence.
- **Suite 6: Settings & Omni-Channel (11 tests)**: Bot settings, Custom API keys, Channel connections, Webhook processors, Widget config, Instagram verification, Channel ON/OFF toggles.
- **Suite 7: Advanced High-Impact AI Suite (10 tests)**: Cosine vector similarity, Normalized vector embeddings, Hybrid RAG scoring, Voice note audio transcription, Store order tracking tool, Product inventory tool, Intent tool calling dispatcher, Context window auto-summarization.
- **Suite 8: WhatsApp Interactive Messages (7 tests)**: Quick Reply buttons payload builder, Interactive List Menu payload builder, Product catalog carousel cards builder, WhatsApp button click webhook ingestion, WhatsApp list menu row selection webhook ingestion, Agent interactive buttons dispatch, Agent product catalog carousel dispatch.
- **Suite 9: Live Chat & Agent Experience Enhancements (7 tests)**: Image attachment upload & preview, PDF document upload & download card, Conversation resolution & automated CSAT survey trigger, Customer satisfaction 1-5 rating & feedback storage, Web widget CSAT rating submission, Urgent escalation alarm state detection, Typing indicator simulation latency bounds (800ms–1500ms).

