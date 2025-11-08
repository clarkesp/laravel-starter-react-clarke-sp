import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Head } from '@inertiajs/react';
import { Activity, Shield, ShieldCheck, Users } from 'lucide-react';

interface AdminDashboardProps {
    admin: {
        id: number;
        name: string;
        email: string;
        roles: string[];
        is_super_admin: boolean;
    };
    stats: {
        total_admins: number;
        active_admins: number;
        total_roles: number;
        total_permissions: number;
    };
    recentActivity: Array<{
        id: number;
        user: string;
        action: string;
        resource_type: string;
        description: string;
        created_at: string;
    }>;
}

export default function AdminDashboard({ admin, stats, recentActivity }: AdminDashboardProps) {
    return (
        <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
            <Head title="Admin Dashboard" />
            
            {/* Header */}
            <div className="bg-white shadow dark:bg-gray-800">
                <div className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-3xl font-bold text-gray-900 dark:text-white">
                                Admin Dashboard
                            </h1>
                            <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Welcome back, {admin.name}
                            </p>
                        </div>
                        {admin.is_super_admin && (
                            <div className="flex items-center gap-2 rounded-lg bg-orange-100 px-4 py-2 dark:bg-orange-900/20">
                                <ShieldCheck className="h-5 w-5 text-orange-600 dark:text-orange-400" />
                                <span className="text-sm font-semibold text-orange-900 dark:text-orange-300">
                                    Super Admin
                                </span>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* Main Content */}
            <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                {/* Stats Grid */}
                <div className="mb-8 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Total Admins
                            </CardTitle>
                            <Users className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.total_admins}</div>
                            <p className="text-xs text-muted-foreground">
                                {stats.active_admins} active
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Roles
                            </CardTitle>
                            <Shield className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.total_roles}</div>
                            <p className="text-xs text-muted-foreground">
                                Role management
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Permissions
                            </CardTitle>
                            <ShieldCheck className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.total_permissions}</div>
                            <p className="text-xs text-muted-foreground">
                                Access control
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Recent Activity
                            </CardTitle>
                            <Activity className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{recentActivity.length}</div>
                            <p className="text-xs text-muted-foreground">
                                Last 10 actions
                            </p>
                        </CardContent>
                    </Card>
                </div>

                {/* Recent Activity */}
                <Card>
                    <CardHeader>
                        <CardTitle>Recent Activity</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="space-y-4">
                            {recentActivity.length > 0 ? (
                                recentActivity.map((activity) => (
                                    <div
                                        key={activity.id}
                                        className="flex items-start gap-4 border-b pb-4 last:border-0 last:pb-0"
                                    >
                                        <div className="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                                            <Activity className="h-5 w-5 text-primary" />
                                        </div>
                                        <div className="flex-1">
                                            <p className="text-sm font-medium">
                                                {activity.description}
                                            </p>
                                            <p className="text-xs text-muted-foreground">
                                                {activity.user} • {activity.created_at}
                                            </p>
                                        </div>
                                        <span className="rounded-full bg-gray-100 px-2 py-1 text-xs font-medium dark:bg-gray-800">
                                            {activity.action}
                                        </span>
                                    </div>
                                ))
                            ) : (
                                <p className="text-center text-sm text-muted-foreground">
                                    No recent activity
                                </p>
                            )}
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    );
}
