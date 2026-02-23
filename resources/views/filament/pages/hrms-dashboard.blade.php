<x-filament::page>

{{-- Enhanced Compact Styles with ATI Branding --}}
<style>
    :root {
        --ati-green: #10b981;
        --ati-green-dark: #059669;
        --ati-amber: #f59e0b;
        --ati-amber-dark: #d97706;
    }

    .dashboard-container {
        background: linear-gradient(135deg, #f0fdf4 0%, #fef3c7 100%);
        min-height: 100vh;
        padding: 1.25rem;
    }

    .dark .dashboard-container {
        background: linear-gradient(135deg, #064e3b 0%, #78350f 100%);
    }

    /* Compact Hero Section */
    .hero-section {
        background: linear-gradient(135deg, #10b981 0%, #059669 50%, #f59e0b 100%);
        border-radius: 16px;
        padding: 1.75rem 1.5rem;
        margin-bottom: 1.25rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.25);
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 70%);
        animation: float 20s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        50% { transform: translate(20px, -20px) rotate(180deg); }
    }

    .hero-content { position: relative; z-index: 1; }

    .hero-title {
        font-size: 1.75rem;
        font-weight: 900;
        color: white;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 0.375rem;
        animation: slideIn 0.6s ease-out;
    }

    .hero-subtitle {
        font-size: 0.95rem;
        color: rgba(255,255,255,0.95);
        margin-bottom: 1rem;
        animation: slideIn 0.6s ease-out 0.1s backwards;
    }

    .hero-meta {
        display: flex;
        gap: 0.875rem;
        flex-wrap: wrap;
        animation: slideIn 0.6s ease-out 0.2s backwards;
    }

    .hero-meta-item {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(8px);
        padding: 0.5rem 0.875rem;
        border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.25);
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }

    .hero-meta-item:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-1px);
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }

    /* Compact Section Headers */
    .section-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid transparent;
        border-image: linear-gradient(90deg, var(--ati-green), var(--ati-amber)) 1;
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--ati-green), var(--ati-amber));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Section header link */
    .section-header-link {
        margin-left: auto;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--ati-green);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.625rem;
        border-radius: 6px;
        border: 1px solid var(--ati-green);
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .section-header-link:hover {
        background: var(--ati-green);
        color: white;
    }

    /* Compact Module Cards */
    .modules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .module-card {
        background: white;
        border-radius: 14px;
        padding: 1.25rem;
        border: 2px solid #e5e7eb;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        animation: fadeInUp 0.5s ease-out backwards;
    }

    .dark .module-card {
        background: #1f2937;
        border-color: #374151;
    }

    .module-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 0;
        background: linear-gradient(135deg, var(--ati-green), var(--ati-amber));
        opacity: 0.08;
        transition: height 0.3s ease;
    }

    .module-card:hover::after { height: 100%; }

    .module-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.12);
        border-color: var(--ati-green);
    }

    .module-header {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
        position: relative;
        z-index: 1;
    }

    .module-icon-wrapper {
        flex-shrink: 0;
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .module-card:hover .module-icon-wrapper {
        transform: scale(1.1) rotate(-8deg);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .module-info { flex: 1; }

    .module-title {
        font-size: 1rem;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 0.375rem;
    }

    .dark .module-title { color: #f3f4f6; }

    .module-desc {
        font-size: 0.8125rem;
        color: #6b7280;
        line-height: 1.4;
    }

    .dark .module-desc { color: #9ca3af; }

    .module-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        z-index: 1;
        padding-top: 0.875rem;
        border-top: 1px solid #e5e7eb;
    }

    .dark .module-footer { border-color: #374151; }

    .module-stat {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        background: #f3f4f6;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.8125rem;
        color: #1f2937;
    }

    .dark .module-stat {
        background: #374151;
        color: #f3f4f6;
    }

    .module-arrow {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: linear-gradient(135deg, var(--ati-green), var(--ati-green-dark));
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .module-card:hover .module-arrow {
        transform: translateX(3px);
        background: linear-gradient(135deg, var(--ati-amber), var(--ati-amber-dark));
    }

    /* Two-Column Layout for Widgets */
    .widgets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.25rem;
    }

    /* Widget Card Base */
    .widget-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        border: 2px solid #e5e7eb;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        animation: fadeInUp 0.5s ease-out backwards;
    }

    .dark .widget-card {
        background: #1f2937;
        border-color: #374151;
    }

    /* Activity Feed - Compact */
    .activity-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.875rem;
        border-radius: 12px;
        margin-bottom: 0.75rem;
        border: 2px solid transparent;
        transition: all 0.25s ease;
        animation: fadeInUp 0.5s ease-out backwards;
        text-decoration: none;
        cursor: pointer;
    }

    .activity-item:last-child { margin-bottom: 0; }

    .activity-item:hover {
        background: #f9fafb;
        border-color: var(--ati-green);
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.12);
    }

    .dark .activity-item:hover { background: #111827; }

    .activity-icon-wrapper {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.25s ease;
    }

    .activity-item:hover .activity-icon-wrapper {
        transform: scale(1.08) rotate(3deg);
    }

    .activity-icon-wrapper.blue   { background: linear-gradient(135deg, #dbeafe, #93c5fd); }
    .activity-icon-wrapper.amber  { background: linear-gradient(135deg, #fef3c7, #fcd34d); }
    .activity-icon-wrapper.purple { background: linear-gradient(135deg, #e9d5ff, #c084fc); }
    .activity-icon-wrapper.rose   { background: linear-gradient(135deg, #ffe4e6, #fda4af); }
    .activity-icon-wrapper.green  { background: linear-gradient(135deg, #d1fae5, #6ee7b7); }

    .activity-content { flex: 1; min-width: 0; }

    .activity-title {
        font-weight: 700;
        font-size: 0.875rem;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .dark .activity-title { color: #f3f4f6; }

    .activity-meta {
        font-size: 0.75rem;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .dark .activity-meta { color: #9ca3af; }

    .activity-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.625rem;
        border-radius: 6px;
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .activity-badge.blue   { background: #dbeafe; color: #1e40af; }
    .activity-badge.amber  { background: #fef3c7; color: #92400e; }
    .activity-badge.purple { background: #e9d5ff; color: #6b21a8; }
    .activity-badge.rose   { background: #ffe4e6; color: #9f1239; }
    .activity-badge.green  { background: #d1fae5; color: #065f46; }

    /* Arrow shown on hover */
    .activity-arrow {
        flex-shrink: 0;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transform: translateX(-4px);
        transition: all 0.25s ease;
    }

    .dark .activity-arrow { background: #374151; }

    .activity-item:hover .activity-arrow {
        opacity: 1;
        transform: translateX(0);
        background: linear-gradient(135deg, var(--ati-green), var(--ati-green-dark));
    }

    /* Announcements Widget */
    .announcement-item {
        display: block;
        text-decoration: none;
        padding: 1rem;
        border-left: 3px solid #e5e7eb;
        margin-bottom: 0.875rem;
        background: #f9fafb;
        border-radius: 0 8px 8px 0;
        transition: all 0.25s ease;
        cursor: pointer;
    }

    .announcement-item:last-child { margin-bottom: 0; }

    .dark .announcement-item {
        background: #111827;
        border-left-color: #374151;
    }

    .announcement-item.high {
        border-left-color: #dc2626;
        background: #fef2f2;
    }

    .dark .announcement-item.high {
        background: #7f1d1d;
        border-left-color: #ef4444;
    }

    .announcement-item.medium {
        border-left-color: #f59e0b;
        background: #fffbeb;
    }

    .dark .announcement-item.medium {
        background: #78350f;
        border-left-color: #fbbf24;
    }

    .announcement-item:hover {
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-left-width: 4px;
    }

    .announcement-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .announcement-title {
        font-weight: 700;
        font-size: 0.875rem;
        color: #1f2937;
        flex: 1;
    }

    .dark .announcement-title { color: #f3f4f6; }

    .announcement-date {
        font-size: 0.75rem;
        color: #6b7280;
    }

    .dark .announcement-date { color: #9ca3af; }

    .announcement-message {
        font-size: 0.8125rem;
        color: #4b5563;
        line-height: 1.5;
    }

    .dark .announcement-message { color: #d1d5db; }

    /* Events List */
    .event-item {
        display: flex;
        gap: 1rem;
        padding: 1rem;
        border-radius: 12px;
        background: #f9fafb;
        margin-bottom: 0.75rem;
        transition: all 0.25s ease;
        text-decoration: none;
        cursor: pointer;
        border: 2px solid transparent;
    }

    .event-item:last-child { margin-bottom: 0; }

    .dark .event-item { background: #111827; }

    .event-item:hover {
        background: white;
        box-shadow: 0 4px 16px rgba(16, 185, 129, 0.12);
        transform: translateY(-2px);
        border-color: var(--ati-green);
    }

    .dark .event-item:hover { background: #1f2937; }

    .event-date-box {
        flex-shrink: 0;
        width: 56px;
        padding: 0.625rem;
        background: linear-gradient(135deg, var(--ati-green), var(--ati-green-dark));
        border-radius: 10px;
        text-align: center;
        color: white;
        transition: all 0.25s ease;
    }

    .event-item:hover .event-date-box {
        background: linear-gradient(135deg, var(--ati-amber), var(--ati-amber-dark));
    }

    .event-month {
        font-size: 0.625rem;
        font-weight: 600;
        text-transform: uppercase;
        opacity: 0.9;
    }

    .event-day {
        font-size: 1.5rem;
        font-weight: 900;
        line-height: 1;
    }

    .event-details { flex: 1; min-width: 0; }

    .event-title {
        font-weight: 700;
        font-size: 0.875rem;
        color: #1f2937;
        margin-bottom: 0.375rem;
    }

    .dark .event-title { color: #f3f4f6; }

    .event-meta {
        font-size: 0.75rem;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .dark .event-meta { color: #9ca3af; }

    /* Birthday Widget */
    .birthday-item {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        padding: 0.875rem;
        border-radius: 10px;
        background: #f9fafb;
        margin-bottom: 0.625rem;
        transition: all 0.25s ease;
    }

    .birthday-item:last-child { margin-bottom: 0; }

    .dark .birthday-item { background: #111827; }

    .birthday-item:hover {
        background: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transform: scale(1.02);
    }

    .dark .birthday-item:hover { background: #1f2937; }

    .birthday-item.today {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border: 2px solid #f59e0b;
    }

    .dark .birthday-item.today {
        background: linear-gradient(135deg, #78350f, #92400e);
        border-color: #fbbf24;
    }

    .birthday-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--ati-green), var(--ati-amber));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1rem;
    }

    .birthday-info { flex: 1; }

    .birthday-name {
        font-weight: 700;
        font-size: 0.875rem;
        color: #1f2937;
    }

    .dark .birthday-name { color: #f3f4f6; }

    .birthday-dept {
        font-size: 0.75rem;
        color: #6b7280;
    }

    .dark .birthday-dept { color: #9ca3af; }

    .birthday-date {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--ati-amber);
    }

    /* Pending Actions */
    .pending-action-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: 12px;
        background: #f9fafb;
        margin-bottom: 0.75rem;
        transition: all 0.25s ease;
        cursor: pointer;
        text-decoration: none;
    }

    .pending-action-item:last-child { margin-bottom: 0; }

    .dark .pending-action-item { background: #111827; }

    .pending-action-item:hover {
        background: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transform: translateX(4px);
    }

    .dark .pending-action-item:hover { background: #1f2937; }

    .pending-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pending-icon.blue   { background: linear-gradient(135deg, #dbeafe, #93c5fd); }
    .pending-icon.amber  { background: linear-gradient(135deg, #fef3c7, #fcd34d); }
    .pending-icon.purple { background: linear-gradient(135deg, #e9d5ff, #c084fc); }
    .pending-icon.green  { background: linear-gradient(135deg, #d1fae5, #6ee7b7); }
    .pending-icon.rose   { background: linear-gradient(135deg, #ffe4e6, #fda4af); }

    .pending-info { flex: 1; }

    .pending-title {
        font-weight: 700;
        font-size: 0.875rem;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .dark .pending-title { color: #f3f4f6; }

    .pending-subtitle {
        font-size: 0.75rem;
        color: #6b7280;
    }

    .dark .pending-subtitle { color: #9ca3af; }

    .pending-count {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--ati-green), var(--ati-green-dark));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 900;
        font-size: 1.25rem;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 2.5rem 1.5rem;
        background: linear-gradient(135deg, #f9fafb, #f3f4f6);
        border-radius: 14px;
        border: 2px dashed #d1d5db;
    }

    .dark .empty-state {
        background: linear-gradient(135deg, #1f2937, #111827);
        border-color: #4b5563;
    }

    .empty-icon {
        width: 56px;
        height: 56px;
        margin: 0 auto 1rem;
        opacity: 0.25;
    }

    .empty-title {
        font-size: 1rem;
        font-weight: 700;
        color: #6b7280;
        margin-bottom: 0.375rem;
    }

    .dark .empty-title { color: #9ca3af; }

    .empty-text {
        color: #9ca3af;
        font-size: 0.875rem;
    }

    .dark .empty-text { color: #6b7280; }

    /* Password Modal */
    .password-modal {
        background: white !important;
        border-radius: 20px;
        padding: 2.5rem;
        max-width: 480px;
        border: 3px solid var(--ati-green);
        box-shadow: 0 20px 60px rgba(16, 185, 129, 0.35);
        animation: modalSlideIn 0.4s ease-out;
    }

    .dark .password-modal {
        background: #1f2937 !important;
        border-color: var(--ati-amber);
        box-shadow: 0 20px 60px rgba(245, 158, 11, 0.35);
    }

    @keyframes modalSlideIn {
        from { opacity: 0; transform: translateY(-20px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .modal-icon-wrapper {
        width: 72px;
        height: 72px;
        margin: 0 auto 1.25rem;
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: pulse 2s ease-in-out infinite;
    }

    .dark .modal-icon-wrapper {
        background: linear-gradient(135deg, #7f1d1d, #991b1b);
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7); }
        50% { transform: scale(1.05); box-shadow: 0 0 0 8px rgba(220, 38, 38, 0); }
    }

    .modal-title {
        font-size: 1.75rem;
        font-weight: 900;
        background: linear-gradient(135deg, #dc2626, #991b1b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.875rem;
        text-align: center;
    }

    .modal-text {
        text-align: center;
        color: #6b7280;
        font-size: 0.9375rem;
        line-height: 1.6;
        margin-bottom: 1.75rem;
    }

    .dark .modal-text { color: #9ca3af; }

    .modal-btn {
        width: 100%;
        padding: 0.875rem 1.75rem;
        background: linear-gradient(135deg, #dc2626, #991b1b);
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.9375rem;
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(220, 38, 38, 0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.625rem;
    }

    .modal-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(220, 38, 38, 0.4);
        background: linear-gradient(135deg, #991b1b, #7f1d1d);
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .admin-grid-3col { grid-template-columns: 1fr 1fr !important; }
    }

    @media (max-width: 768px) {
        .dashboard-container { padding: 0.875rem; }
        .hero-section { padding: 1.25rem 1rem; }
        .hero-title { font-size: 1.5rem; }
        .hero-subtitle { font-size: 0.875rem; }
        .modules-grid { grid-template-columns: 1fr; }
        .widgets-grid { grid-template-columns: 1fr; }
        .admin-grid-3col { grid-template-columns: 1fr !important; }
    }

    /* Stagger Animations */
    .stagger-1 { animation-delay: 0.05s; }
    .stagger-2 { animation-delay: 0.1s; }
    .stagger-3 { animation-delay: 0.15s; }
    .stagger-4 { animation-delay: 0.2s; }
    .stagger-5 { animation-delay: 0.25s; }
    .stagger-6 { animation-delay: 0.3s; }
</style>

{{-- Password Change Modal --}}
@if($mustChangePassword)
    <div x-data="{ open: true }" x-show="open" x-trap="open"
         class="fixed inset-0 bg-black/80 backdrop-blur-md z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             class="password-modal w-full">
            <div class="modal-icon-wrapper">
                <x-heroicon-o-shield-exclamation class="w-9 h-9 text-red-600 dark:text-red-400" />
            </div>
            <h2 class="modal-title">Security Alert!</h2>
            <p class="modal-text">
                You are currently using a temporary password. For your account security, please update your password immediately to continue using the system.
            </p>
            <button @click="window.location.href='{{ route('filament.hrms.pages.profile') }}'"
                    class="modal-btn">
                <x-heroicon-o-lock-closed class="w-5 h-5" />
                <span>Update Password Now</span>
            </button>
        </div>
    </div>
@endif

{{-- Dashboard Container --}}
<div class="dashboard-container">

    {{-- HERO / GREETING SECTION --}}
    <div class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">{{ $this->getGreeting() }}</h1>
            <p class="hero-subtitle">Welcome back to ATI-HRMS. Here's your overview for today.</p>
            <div class="hero-meta">
                <div class="hero-meta-item">
                    <x-heroicon-o-calendar class="w-4 h-4" />
                    <span>{{ $this->getCurrentDate() }}</span>
                </div>
                <div class="hero-meta-item">
                    <x-heroicon-o-clock class="w-4 h-4" />
                    <span>{{ $this->getCurrentTime() }}</span>
                </div>
                @if($user->isAdmin())
                    <div class="hero-meta-item">
                        <x-heroicon-o-shield-check class="w-4 h-4" />
                        <span>Administrator</span>
                    </div>
                @else
                    <div class="hero-meta-item">
                        <x-heroicon-o-user class="w-4 h-4" />
                        <span>Employee</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- EMPLOYEE VIEW --}}
    @if(!$user->isAdmin())
        <div class="widgets-grid" style="margin-bottom: 1.25rem;">

            {{-- Announcements --}}
            <div class="widget-card stagger-1">
                <div class="section-header">
                    <x-heroicon-o-megaphone class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    <h3 class="section-title">Announcements</h3>
                    <a href="{{ route('filament.hrms.resources.announcements.index') }}" class="section-header-link">
                        View All
                        <x-heroicon-o-arrow-right class="w-3 h-3" />
                    </a>
                </div>
                @forelse($announcements as $announcement)
                    <a href="{{ route('filament.hrms.resources.announcements.index') }}"
                       class="announcement-item {{ $announcement['priority'] }}">
                        <div class="announcement-header">
                            <x-dynamic-component :component="$announcement['icon']" class="w-4 h-4 flex-shrink-0" />
                            <div class="announcement-title">{{ $announcement['title'] }}</div>
                            <div class="announcement-date">{{ $announcement['date'] }}</div>
                        </div>
                        <div class="announcement-message">{{ $announcement['message'] }}</div>
                    </a>
                @empty
                    <div class="empty-state">
                        <x-heroicon-o-bell-slash class="empty-icon text-gray-400" />
                        <h4 class="empty-title">No Announcements</h4>
                        <p class="empty-text">Check back later for updates.</p>
                    </div>
                @endforelse
            </div>

            {{-- Upcoming Events --}}
            <div class="widget-card stagger-2">
                <div class="section-header">
                    <x-heroicon-o-calendar-days class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                    <h3 class="section-title">Upcoming Events</h3>
                    <a href="{{ route('filament.hrms.resources.events.index') }}" class="section-header-link">
                        View All
                        <x-heroicon-o-arrow-right class="w-3 h-3" />
                    </a>
                </div>
                @forelse($upcomingEvents as $event)
                    <a href="{{ route('filament.hrms.resources.events.index') }}" class="event-item">
                        <div class="event-date-box">
                            <div class="event-month">{{ \Carbon\Carbon::parse($event['date'])->format('M') }}</div>
                            <div class="event-day">{{ \Carbon\Carbon::parse($event['date'])->format('d') }}</div>
                        </div>
                        <div class="event-details">
                            <div class="event-title">{{ $event['title'] }}</div>
                            <div class="event-meta">
                                <span class="flex items-center gap-1">
                                    <x-heroicon-o-clock class="w-3.5 h-3.5" />
                                    {{ $event['time'] }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <x-heroicon-o-map-pin class="w-3.5 h-3.5" />
                                    {{ $event['location'] }}
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="empty-state">
                        <x-heroicon-o-calendar-days class="empty-icon text-gray-400" />
                        <h4 class="empty-title">No Upcoming Events</h4>
                        <p class="empty-text">Stay tuned for future events.</p>
                    </div>
                @endforelse
            </div>

        </div>
    @endif

    {{-- ADMIN VIEW --}}
    @if($user->isAdmin())
        {{-- Row 1: Three columns --}}
        <div class="admin-grid-3col" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">

            {{-- Col 1: Recent Activities --}}
            <div class="widget-card stagger-1">
                <div class="section-header">
                    <x-heroicon-o-clock class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                    <h3 class="section-title">Recent Activities</h3>
                </div>
                @forelse($recentActivities as $index => $activity)
                    <a href="{{ $activity['url'] }}"
                       class="activity-item"
                       style="animation-delay: {{ $index * 0.05 }}s">
                        <div class="activity-icon-wrapper {{ $activity['color'] }}">
                            <x-dynamic-component
                                :component="$activity['icon']"
                                class="w-5 h-5 text-{{ $activity['color'] }}-600" />
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">{{ $activity['employee'] }}</div>
                            <div class="activity-meta">
                                <span class="activity-badge {{ $activity['color'] }}">{{ $activity['type'] }}</span>
                                <span>{{ $activity['status'] }}</span>
                                <span>•</span>
                                <span>{{ $activity['date'] }}</span>
                            </div>
                        </div>
                        <div class="activity-arrow">
                            <x-heroicon-o-arrow-right class="w-3.5 h-3.5 text-white" />
                        </div>
                    </a>
                @empty
                    <div class="empty-state">
                        <x-heroicon-o-inbox class="empty-icon text-gray-400" />
                        <h4 class="empty-title">No Recent Activities</h4>
                        <p class="empty-text">Activities will appear here.</p>
                    </div>
                @endforelse
            </div>

            {{-- Col 2: Pending Actions --}}
            <div class="widget-card stagger-2">
                <div class="section-header">
                    <x-heroicon-o-bell-alert class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                    <h3 class="section-title">Pending Actions</h3>
                </div>
                @forelse($pendingActions as $action)
                    <a href="{{ route($action['route']) }}" class="pending-action-item">
                        <div class="pending-icon {{ $action['color'] }}">
                            <x-dynamic-component :component="$action['icon']" class="w-6 h-6 text-{{ $action['color'] }}-600" />
                        </div>
                        <div class="pending-info">
                            <div class="pending-title">{{ $action['title'] }}</div>
                            <div class="pending-subtitle">Requires your attention</div>
                        </div>
                        <div class="pending-count">{{ $action['count'] }}</div>
                    </a>
                @empty
                    <div class="empty-state">
                        <x-heroicon-o-check-circle class="empty-icon text-green-400" />
                        <h4 class="empty-title">All Caught Up!</h4>
                        <p class="empty-text">No pending actions at this time.</p>
                    </div>
                @endforelse
            </div>

            {{-- Col 3: Announcements --}}
            <div class="widget-card stagger-3">
                <div class="section-header">
                    <x-heroicon-o-megaphone class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    <h3 class="section-title">Announcements</h3>
                    <a href="{{ route('filament.hrms.resources.announcements.index') }}" class="section-header-link">
                        View All
                        <x-heroicon-o-arrow-right class="w-3 h-3" />
                    </a>
                </div>
                @forelse($announcements as $announcement)
                    <a href="{{ route('filament.hrms.resources.announcements.index') }}"
                       class="announcement-item {{ $announcement['priority'] }}">
                        <div class="announcement-header">
                            <x-dynamic-component :component="$announcement['icon']" class="w-4 h-4 flex-shrink-0" />
                            <div class="announcement-title">{{ $announcement['title'] }}</div>
                            <div class="announcement-date">{{ $announcement['date'] }}</div>
                        </div>
                        <div class="announcement-message">{{ $announcement['message'] }}</div>
                    </a>
                @empty
                    <div class="empty-state">
                        <x-heroicon-o-bell-slash class="empty-icon text-gray-400" />
                        <h4 class="empty-title">No Announcements</h4>
                        <p class="empty-text">Check back later for updates.</p>
                    </div>
                @endforelse
            </div>

        </div>

        {{-- Row 2: Upcoming Events full-width --}}
        <div class="widget-card stagger-4" style="margin-bottom: 1.25rem;">
            <div class="section-header">
                <x-heroicon-o-calendar-days class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                <h3 class="section-title">Upcoming Events</h3>
                <a href="{{ route('filament.hrms.resources.events.index') }}" class="section-header-link">
                    View All
                    <x-heroicon-o-arrow-right class="w-3 h-3" />
                </a>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 0.75rem;">
                @forelse($upcomingEvents as $event)
                    <a href="{{ route('filament.hrms.resources.events.index') }}" class="event-item" style="margin-bottom: 0;">
                        <div class="event-date-box">
                            <div class="event-month">{{ \Carbon\Carbon::parse($event['date'])->format('M') }}</div>
                            <div class="event-day">{{ \Carbon\Carbon::parse($event['date'])->format('d') }}</div>
                        </div>
                        <div class="event-details">
                            <div class="event-title">{{ $event['title'] }}</div>
                            <div class="event-meta">
                                <span class="flex items-center gap-1">
                                    <x-heroicon-o-clock class="w-3.5 h-3.5" />
                                    {{ $event['time'] }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <x-heroicon-o-map-pin class="w-3.5 h-3.5" />
                                    {{ $event['location'] }}
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="empty-state" style="grid-column: 1 / -1;">
                        <x-heroicon-o-calendar-days class="empty-icon text-gray-400" />
                        <h4 class="empty-title">No Upcoming Events</h4>
                        <p class="empty-text">Stay tuned for future events.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    {{-- QUICK ACCESS MODULES --}}
    <div>
        <div class="section-header">
            <x-heroicon-o-squares-2x2 class="w-6 h-6 text-green-600 dark:text-green-400" />
            <h2 class="section-title">Quick Access Modules</h2>
        </div>

        <div class="modules-grid">
            @foreach($modules as $index => $module)
                <a href="{{ route($module['route']) }}" class="module-card stagger-{{ $index + 1 }}">
                    <div class="module-header">
                        <div class="module-icon-wrapper {{ $module['icon_bg'] }}">
                            <x-dynamic-component :component="$module['icon']"
                                class="w-6 h-6 {{ $module['icon_color'] }}" />
                        </div>
                        <div class="module-info">
                            <h3 class="module-title">{{ $module['title'] }}</h3>
                            <p class="module-desc">
                                {{ $user->isAdmin() ? $module['admin_text'] : $module['employee_text'] }}
                            </p>
                        </div>
                    </div>
                    <div class="module-footer">
                        <div class="module-stat">
                            <x-heroicon-o-document-text class="w-4 h-4 {{ $module['icon_color'] }}" />
                            <span>{{ $module['stat'] }}</span>
                        </div>
                        <div class="module-arrow">
                            <x-heroicon-o-arrow-right class="w-4 h-4 text-white" />
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    {{-- BIRTHDAY CELEBRANTS --}}
    <div class="widget-card stagger-1" style="margin-bottom: 1.25rem;">
        <div class="section-header">
            <x-heroicon-o-cake class="w-6 h-6 text-pink-600 dark:text-pink-400" />
            <h3 class="section-title">Birthday Celebrants This Month</h3>
        </div>
        @forelse($birthdayCelebrants as $celebrant)
            <div class="birthday-item {{ $celebrant['is_today'] ? 'today' : '' }}">
                <div class="birthday-avatar">
                    {{ strtoupper(substr($celebrant['name'], 0, 2)) }}
                </div>
                <div class="birthday-info">
                    <div class="birthday-name">
                        {{ $celebrant['name'] }}
                        @if($celebrant['is_today'])
                            <span class="ml-1">🎉</span>
                        @endif
                    </div>
                    <div class="birthday-dept">{{ $celebrant['department'] }}</div>
                </div>
                <div class="birthday-date">{{ $celebrant['date'] }}</div>
            </div>
        @empty
            <div class="empty-state">
                <x-heroicon-o-face-smile class="empty-icon text-gray-400" />
                <h4 class="empty-title">No Birthdays This Month</h4>
                <p class="empty-text">We'll celebrate next month!</p>
            </div>
        @endforelse
    </div>

</div>

</x-filament::page>
