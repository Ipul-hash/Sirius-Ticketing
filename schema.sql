-- =====================================================================
-- SiriusTicketing - Enterprise Multi-Tenant Helpdesk Database Schema
-- Stack: MySQL 8.0+ / MariaDB 10.5+ | Engine: InnoDB | Charset: utf8mb4
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS ticket_collisions;
DROP TABLE IF EXISTS ticket_activities;
DROP TABLE IF EXISTS ticket_approvals;
DROP TABLE IF EXISTS ticket_attachments;
DROP TABLE IF EXISTS ticket_messages;
DROP TABLE IF EXISTS tickets;
DROP TABLE IF EXISTS ticket_categories;
DROP TABLE IF EXISTS canned_responses;
DROP TABLE IF EXISTS sla_policies;
DROP TABLE IF EXISTS company_assets;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS departments;
DROP TABLE IF EXISTS companies;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- 1. COMPANIES (Master Tenant Platform)
-- ---------------------------------------------------------------------
CREATE TABLE companies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    domain VARCHAR(150) NULL,
    logo_path VARCHAR(255) NULL,
    status ENUM('active', 'suspended', 'trial') NOT NULL DEFAULT 'active',
    plan ENUM('starter', 'professional', 'enterprise') NOT NULL DEFAULT 'starter',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_companies_slug (slug),
    INDEX idx_companies_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. DEPARTMENTS (Divisi Internal Tenant: IT, HR, Finance, Facility)
-- ---------------------------------------------------------------------
CREATE TABLE departments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    lead_user_id BIGINT UNSIGNED NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_departments_company_active (company_id, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. USERS (Multi-Tenant Identity & RBAC)
-- ---------------------------------------------------------------------
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NULL, -- NULL khusus Superadmin Platform Sirius
    department_id BIGINT UNSIGNED NULL,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(191) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('superadmin', 'company_admin', 'agent', 'requester') NOT NULL DEFAULT 'requester',
    job_title VARCHAR(100) NULL,
    phone VARCHAR(30) NULL,
    avatar_path VARCHAR(255) NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_email (email),
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    INDEX idx_users_company_role_active (company_id, role, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tambahkan foreign key circular lead_user_id pada departments
ALTER TABLE departments
    ADD CONSTRAINT fk_departments_lead_user
    FOREIGN KEY (lead_user_id) REFERENCES users(id) ON DELETE SET NULL;

-- ---------------------------------------------------------------------
-- 4. COMPANY_ASSETS (Inventaris Perangkat & Lisensi - ServiceNow / JSM)
-- ---------------------------------------------------------------------
CREATE TABLE company_assets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    department_id BIGINT UNSIGNED NULL,
    assigned_to_user_id BIGINT UNSIGNED NULL,
    asset_tag VARCHAR(50) NOT NULL,
    name VARCHAR(150) NOT NULL,
    category ENUM('hardware', 'server', 'network', 'software_license') NOT NULL DEFAULT 'hardware',
    serial_number VARCHAR(100) NULL,
    status ENUM('in_use', 'available', 'maintenance', 'retired') NOT NULL DEFAULT 'in_use',
    notes TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to_user_id) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY uq_company_asset_tag (company_id, asset_tag),
    INDEX idx_assets_assigned_user (company_id, assigned_to_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5. SLA_POLICIES (Target Waktu Respon & Resolusi Berdasarkan Prioritas)
-- ---------------------------------------------------------------------
CREATE TABLE sla_policies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') NOT NULL,
    first_response_time_minutes INT UNSIGNED NOT NULL,
    resolution_time_minutes INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    UNIQUE KEY uq_company_priority_sla (company_id, priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 6. CANNED_RESPONSES (Template Balasan Cepat / Macros Agen - Zendesk)
-- ---------------------------------------------------------------------
CREATE TABLE canned_responses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    department_id BIGINT UNSIGNED NULL,
    title VARCHAR(150) NOT NULL,
    shortcut VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    INDEX idx_canned_company_shortcut (company_id, shortcut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 7. TICKET_CATEGORIES (Kategori Masalah & Trigger Approval)
-- ---------------------------------------------------------------------
CREATE TABLE ticket_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    department_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    default_priority ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium',
    requires_approval BOOLEAN NOT NULL DEFAULT FALSE,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
    INDEX idx_categories_company_dept (company_id, department_id, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 8. TICKETS (Pusat Transaksi Tiket - All-in-One Enterprise)
-- ---------------------------------------------------------------------
CREATE TABLE tickets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    ticket_number VARCHAR(30) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    description LONGTEXT NOT NULL,
    
    -- Relasi Kunci
    category_id BIGINT UNSIGNED NOT NULL,
    department_id BIGINT UNSIGNED NOT NULL,
    requester_id BIGINT UNSIGNED NOT NULL,
    assigned_to BIGINT UNSIGNED NULL,
    asset_id BIGINT UNSIGNED NULL,
    
    -- Status & Prioritas
    status ENUM('open', 'pending_approval', 'in_progress', 'pending_user', 'resolved', 'closed') NOT NULL DEFAULT 'open',
    priority ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium',
    
    -- Fitur ServiceNow: Approval Workflow
    approval_status ENUM('none', 'pending', 'approved', 'rejected') NOT NULL DEFAULT 'none',
    
    -- Fitur Zendesk: Ticket Merging
    is_merged BOOLEAN NOT NULL DEFAULT FALSE,
    merged_into_ticket_id BIGINT UNSIGNED NULL,
    
    -- SLA Engine Timestamps
    first_response_due_at DATETIME NULL,
    first_responded_at DATETIME NULL,
    resolution_due_at DATETIME NULL,
    resolved_at DATETIME NULL,
    closed_at DATETIME NULL,
    is_sla_breached BOOLEAN NOT NULL DEFAULT FALSE,
    
    -- Controlled Denormalization (Optimasi Read Index)
    last_reply_at DATETIME NULL,
    replies_count INT UNSIGNED NOT NULL DEFAULT 0,
    
    -- CSAT Survey (Kepuasan Pengguna)
    satisfaction_rating TINYINT UNSIGNED NULL,
    satisfaction_feedback TEXT NULL,
    
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES ticket_categories(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (requester_id) REFERENCES users(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (asset_id) REFERENCES company_assets(id) ON DELETE SET NULL,
    FOREIGN KEY (merged_into_ticket_id) REFERENCES tickets(id) ON DELETE SET NULL,
    
    -- Indeks Kinerja Tinggi
    UNIQUE KEY uq_company_ticket_number (company_id, ticket_number),
    INDEX idx_tickets_tenant_queue (company_id, status, priority, created_at),
    INDEX idx_tickets_agent_assigned (company_id, assigned_to, status),
    INDEX idx_tickets_dept_filter (company_id, department_id, status),
    INDEX idx_tickets_sla_breach (company_id, is_sla_breached, status),
    FULLTEXT KEY ft_tickets_search (subject)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 9. TICKET_MESSAGES (Percakapan: Public Reply vs Internal Note Zendesk)
-- ---------------------------------------------------------------------
CREATE TABLE ticket_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    message LONGTEXT NOT NULL,
    is_internal_note BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_messages_ticket_timeline (ticket_id, is_internal_note, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 10. TICKET_ATTACHMENTS (Lampiran File Screenshot / Dokumen)
-- ---------------------------------------------------------------------
CREATE TABLE ticket_attachments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_id BIGINT UNSIGNED NOT NULL,
    ticket_message_id BIGINT UNSIGNED NULL,
    uploaded_by BIGINT UNSIGNED NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_size_kb INT UNSIGNED NOT NULL,
    file_type VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (ticket_message_id) REFERENCES ticket_messages(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 11. TICKET_APPROVALS (Log Persetujuan Tiket - ServiceNow / JSM)
-- ---------------------------------------------------------------------
CREATE TABLE ticket_approvals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_id BIGINT UNSIGNED NOT NULL,
    approver_id BIGINT UNSIGNED NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    reason_notes TEXT NULL,
    decided_at DATETIME NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (approver_id) REFERENCES users(id),
    INDEX idx_approvals_user_status (approver_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 12. TICKET_ACTIVITIES (Audit Trail / Riwayat Perubahan)
-- ---------------------------------------------------------------------
CREATE TABLE ticket_activities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    activity_type VARCHAR(50) NOT NULL,
    old_value VARCHAR(255) NULL,
    new_value VARCHAR(255) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_activities_ticket_timeline (ticket_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 13. TICKET_COLLISIONS (Freshdesk Heartbeat: Anti Tabrakan Agen)
-- ---------------------------------------------------------------------
CREATE TABLE ticket_collisions (
    ticket_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    user_name VARCHAR(150) NOT NULL,
    last_seen_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (ticket_id, user_id),
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_collisions_active (ticket_id, last_seen_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================================
-- DUMMY SEED DATA (Ready-to-Test)
-- =====================================================================

-- 1. Tenant Perusahaan
INSERT INTO companies (id, name, slug, status, plan) VALUES 
(1, 'Sirius Global Tech', 'sirius-tech', 'active', 'enterprise'),
(2, 'PT Nusantara Mediatama', 'nusantara-media', 'active', 'professional');

-- 2. Departemen
INSERT INTO departments (id, company_id, name, description, is_active) VALUES 
(1, 1, 'IT Infrastructure & Support', 'Menangani kendala jaringan, perangkat keras, dan server', 1),
(2, 1, 'Human Resources', 'Mengelola urusan kepegawaian dan fasilitas kantor', 1),
(3, 2, 'Technical Operations', 'Support teknis operasional media', 1);

-- 3. Users (Superadmin, Company Admin, Agent, Requester)
-- Catatan: Hash password di bawah adalah 'password' (standar Laravel bcrypt: $2y$12$e0MYzXyjpJS7Pd0RVvHwHeFvF2UfQ9w4vG... dummy)
INSERT INTO users (id, company_id, department_id, name, email, password, role, job_title, is_active) VALUES 
(1, NULL, NULL, 'Sirius Root Admin', 'superadmin@sirius.io', '$2y$12$LJnU.6bN2b31mFom3vXvj.hR8z2NfFkI90HwE6Qd6J45T2zY8zY9e', 'superadmin', 'Platform Architect', 1),
(2, 1, 1, 'Andi Wijaya (Admin)', 'admin@sirius-tech.com', '$2y$12$LJnU.6bN2b31mFom3vXvj.hR8z2NfFkI90HwE6Qd6J45T2zY8zY9e', 'company_admin', 'IT Manager', 1),
(3, 1, 1, 'Rian Hidayat (Agent)', 'agent.rian@sirius-tech.com', '$2y$12$LJnU.6bN2b31mFom3vXvj.hR8z2NfFkI90HwE6Qd6J45T2zY8zY9e', 'agent', 'Senior Helpdesk Tech', 1),
(4, 1, 2, 'Siti Rahma (Requester)', 'siti.rahma@sirius-tech.com', '$2y$12$LJnU.6bN2b31mFom3vXvj.hR8z2NfFkI90HwE6Qd6J45T2zY8zY9e', 'requester', 'HR Specialist', 1);

-- Set Lead User pada Departemen
UPDATE departments SET lead_user_id = 2 WHERE id = 1;

-- 4. Inventaris Aset Kantor (ServiceNow CMDB)
INSERT INTO company_assets (id, company_id, department_id, assigned_to_user_id, asset_tag, name, category, serial_number, status) VALUES 
(1, 1, 1, 4, 'LAP-MBP-001', 'MacBook Pro M2 14 Inch', 'hardware', 'C02G1234MD6R', 'in_use'),
(2, 1, 1, NULL, 'SRV-DB-PROD', 'Dell PowerEdge R740 Database Server', 'server', 'DEL-99482-ID', 'in_use');

-- 5. Kebijakan SLA
INSERT INTO sla_policies (id, company_id, priority, first_response_time_minutes, resolution_time_minutes) VALUES 
(1, 1, 'urgent', 30, 240),
(2, 1, 'high', 120, 480),
(3, 1, 'medium', 240, 1440),
(4, 1, 'low', 480, 2880);

-- 6. Template Canned Response (Zendesk Macros)
INSERT INTO canned_responses (id, company_id, department_id, title, shortcut, message) VALUES 
(1, 1, 1, 'Instruksi Restart Router', '/restart-router', 'Halo, silakan matikan tombol power router selama 30 detik, lalu nyalakan kembali dan tunggu hingga lampu indikator PON/Internet berwarna hijau stabil.'),
(2, 1, 1, 'Permintaan Log Error', '/req-logs', 'Mohon lampirkan tangkapan layar (screenshot) kode error dan file log dari sistem Anda untuk mempermudah investigasi.');

-- 7. Kategori Tiket
INSERT INTO ticket_categories (id, company_id, department_id, name, default_priority, requires_approval, is_active) VALUES 
(1, 1, 1, 'Gangguan Internet & Wi-Fi', 'urgent', 0, 1),
(2, 1, 1, 'Permintaan Pengadaan Laptop Baru', 'high', 1, 1),
(3, 1, 1, 'Permintaan Akses Database Server', 'high', 1, 1),
(4, 1, 2, 'Pengajuan Cuti / Form HR', 'low', 0, 1);

-- 8. Tiket Contoh 1: Tiket Gangguan Internet
INSERT INTO tickets (
    id, company_id, ticket_number, subject, description, category_id, department_id, 
    requester_id, assigned_to, asset_id, status, priority, approval_status, 
    first_response_due_at, resolution_due_at, replies_count, last_reply_at
) VALUES (
    1, 1, 'TCK-2026-00001', 'Koneksi Wi-Fi Lantai 3 Tidak Bisa Akses VPN',
    'Sejak jam 9 pagi seluruh tim HR di lantai 3 tidak bisa terhubung ke server VPN kantor.',
    1, 1, 4, 3, NULL, 'in_progress', 'urgent', 'none',
    DATE_ADD(NOW(), INTERVAL 30 MINUTE), DATE_ADD(NOW(), INTERVAL 4 HOUR), 2, NOW()
);

-- 9. Pesan Tiket (Public vs Internal Note)
INSERT INTO ticket_messages (id, ticket_id, user_id, message, is_internal_note, created_at) VALUES 
(1, 1, 4, 'Tolong dicek segera ya mas, pekerjaan kami terhambat.', 0, DATE_SUB(NOW(), INTERVAL 25 MINUTE)),
(2, 1, 3, 'Saya cek access point AP-L3 memang sempat overload. Sedang direstart dari controller.', 1, DATE_SUB(NOW(), INTERVAL 15 MINUTE)),
(3, 1, 3, 'Halo Mbak Siti, kami sudah melakukan restart AP lantai 3. Mohon dicoba connect ulang ya.', 0, DATE_SUB(NOW(), INTERVAL 5 MINUTE));

-- 10. Audit Activity Trail
INSERT INTO ticket_activities (ticket_id, user_id, activity_type, old_value, new_value, notes) VALUES 
(1, 4, 'ticket_created', NULL, 'open', 'Tiket dibuat oleh requester'),
(1, 2, 'assigned_agent', 'Unassigned', 'Rian Hidayat', 'Ditugaskan oleh Admin Andi Wijaya'),
(1, 3, 'status_changed', 'open', 'in_progress', 'Teknisi mulai menangani tiket');
