# Comprehensive Functional Implementation Plan: Rudood Platform (Ready for Deployment)

This document outlines the **"Bigger Picture"** roadmap. Now that the architectural pivot to a **Laravel Monolithic Blade structure** is complete (all HTML files are successfully served as `.blade.php` views), this plan focuses on transforming these static UI templates into a **fully functional, interactive, and deployable SaaS platform**.

---

## 1. Authentication & Tenancy (The Gateway)

**Goal:** Secure the platform so users can register, log in, and manage their own isolated workspace data.

- [ ] **Database Updates**: Ensure the `users` table is linked to a `workspaces` table (Multi-tenancy).
- [ ] **Controllers**: Implement `AuthController` containing `register`, `login`, and `logout` methods using native Laravel Session Auth.
- [ ] **Blade Wiring**: Update `register.blade.php` and `login.blade.php` to use correct `POST` routes, add `@csrf` tokens, and display validation error messages.
- [ ] **Middleware**: Create `WorkspaceAuth` middleware to protect dashboard routes and ensure data isolation (Users cannot see other workspaces' bots or messages).

---

## 2. AI Training & Rules Engine (`ai-manage.blade.php`)

**Goal:** Allow users to configure their AI Bot, upload knowledge base files, and define custom FAQ rules.

- [x] **Packages Installed**: `smalot/pdfparser` (PDF) + `phpoffice/phpword` (Word/DOCX) installed via Composer.
- [x] **Controllers**: `BotController` and `AutoRuleController` fully implemented.
- [x] **Form 1 - FAQ Builder**: `<form id="faqForm">` wired to `POST /ai-manage/save-rule`. Saves Questions & Answers to `auto_rules` table. Supports delete per rule.
- [x] **Form 2 - Document Upload**: `<form id="uploadDocForm">` wired to `POST /ai-manage/upload-doc`. Extracts text from PDF/DOCX/TXT and stores in `knowledge_bases` table. Supports delete.
- [x] **Dynamic UI**: View fetches and renders all saved rules and documents from PostgreSQL. Empty states shown when no data.

---

## 3. Real-Time Live Chat (`live-chat.blade.php`)

**Goal:** An interactive dashboard where agents can see live incoming messages and chat with customers in real time.

- [ ] **Controllers**: Implement `ConversationController` and `MessageController`.
- [ ] **Dynamic UI**: Query the `conversations` table and render the list of customers on the left sidebar. Query the `messages` table for the active conversation and render the chat bubbles.
- [ ] **Sending Messages**: Wire the chat input form to send an AJAX POST request to Laravel to save the agent's message.
- [ ] **WebSockets & Redis Bridge**:
  - When Laravel saves a new message, trigger a Redis Pub/Sub event (`MessageSent`).
  - The Node.js server (`server.js`) listens to this Redis event and broadcasts it via Socket.io.
  - Add JavaScript inside `live-chat.blade.php` to listen for Socket.io events and dynamically append incoming messages to the screen without refreshing the page.

---

## 4. The AI Automation Loop (The Brain)

**Goal:** Make the Bot automatically reply to customer inquiries when active.

- [ ] **Webhook Endpoint**: Create a public API route (`/api/webhook/incoming`) to simulate receiving messages from WhatsApp/Instagram.
- [ ] **AI Processing Job**: Create a Laravel background queue job (`ProcessCustomerMessage`).
- [ ] **Logic**:
  1. Receive incoming message.
  2. Check if the bot is active for this workspace.
  3. Search `auto_rules` for exact keyword matches.
  4. If no match, send the message + `knowledge_bases` context to the **OpenAI API**.
  5. Save the generated AI reply to the database.
  6. Trigger the Redis event so the Live Chat UI instantly shows the bot's reply.

---

## 5. Settings & Integrations (`settings.blade.php`)

**Goal:** Allow users to manage their profile and connect external channels.

- [ ] **Controllers**: Implement `SettingsController`.
- [ ] **Wiring**: Wire the Settings form to update the Workspace's API Keys (e.g., Meta/WhatsApp token) in the database.

---

## Verification Plan

### Manual Verification
1. **Auth Flow**: Register a new user, log out, log back in.
2. **AI Config Flow**: Add a new FAQ rule via the `ai-manage` page and verify it appears in the PostgreSQL database.
3. **Real-time Flow**: Open the `live-chat` page in two separate browser tabs. Send a message from one tab and verify it instantly appears in the other tab via WebSockets.

---

## Open Questions for Review

> [!IMPORTANT]
> **OpenAI API Key**: Do you have a real OpenAI API key you'd like me to integrate for the AI automation loop, or should I build a "Simulation Mode" that returns dummy AI responses for testing purposes first?
> 
> **File Processing**: For the Knowledge Base uploads (PDF/Word), do you want to use a specific PHP package for text extraction (like `spatie/pdf-to-text`), or just simulate the upload success for now?
