import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface TeamListItem {
    team_id: string;
    name: string;
}

export interface Auth {
    user: User;
    teams: TeamListItem[];
}
export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Site {
    id: string,
    display_name: string,
    team_id: string,
    user_id: string,
    created_at: string,
    updated_at: string,
    page_count: number,
    pages?: SitePage[]
}

export interface SitePage {
    id: string;
    site_id: string;
    display_name: string | null;
    url: string;
    description: string | null;
    user_id: string | null;
    paused: boolean;

    // checking configuration
    check_interval_seconds: number;
    timeout_seconds: number;
    verify_ssl: boolean;
    http_method: string; // e.g., 'GET', 'POST'
    payload: Record<string, unknown> | unknown[] | null;
    authorization_type: string | null; // bearer | digest | basic | grant | etc.
    authorization_payload: Record<string, unknown> | unknown[] | null;
    headers: Record<string, string> | null;
    retries: number;
    expected_status: number[] | null;

    // scheduling / status
    next_check_at: string | null;
    is_down: boolean;
    down_at: string | null;

    // timestamps
    created_at: string;
    updated_at: string;

    // optional eager-loaded visit records summary (when included)
    records?: VisitRecord[];
    latest_records?: VisitRecord[]
}


export interface VisitRecord {
    id: number;
    site_page_id: string;
    status: number; // HTTP status code
    duration_ms: number;
    content_length: number | null;
    has_error: boolean;
    has_met_expected_status: boolean;
    created_at: string; // ISO datetime
    payload?: VisitRecordPayload | null
}


export interface VisitRecordPayload {
    id: number;
    visit_record_id: number;
    response_headers: Record<string, string> | null;
    response_body: string | null;
    created_at: string;
}


export type BreadcrumbItemType = BreadcrumbItem;

// Dashboard widgets
export type DashboardWidgetId = 'pages-total' | 'pages-on-fire' | string;
export type DashboardWidgetTuple<D = Record<string, unknown>> = [DashboardWidgetId, D];
export interface PagesTotalWidgetData {
    total: number;
    up: number;
}

export interface PagesOnFireWidgetItem {
    id: string;
    url: string;
    status?: number | null;
    expected?: number[] | null;
}

export interface PagesOnFireWidgetData {
    count: number;
    items: PagesOnFireWidgetItem[];
}
