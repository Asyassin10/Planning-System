"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { Button } from "@/components/ui/button";
import { LogOut, User as UserIcon } from "lucide-react";
import { getUser, logout, type User } from "@/lib/auth";
import { authApi } from "@/lib/auth-api";

export function Navbar() {
  const [user, setUser] = useState<User | null>(null);

  useEffect(() => {
    setUser(getUser());
  }, []);

  const handleLogout = async () => {
    try {
      const token = typeof window !== 'undefined' ? localStorage.getItem('auth_token') : null;
      if (token) {
        await authApi.logout(token);
      }
    } catch (error) {
      console.error("Logout error:", error);
    } finally {
      logout();
    }
  };

  if (!user) return null;

  const teamName = {
    team_a: "Team A - Planning Requests",
    team_b: "Team B - Operational Planning",
    team_c: "Team C - Execution Events",
  }[user.role];

  const dashboardLink = {
    team_a: "/team-a",
    team_b: "/team-b",
    team_c: "/team-c",
  }[user.role];

  return (
    <nav className="border-b bg-white shadow-sm">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between h-16">
          <div className="flex items-center">
            <Link href="/" className="text-xl font-bold text-gray-900">
              Planning System
            </Link>
            {dashboardLink && (
              <Link
                href={dashboardLink}
                className="ml-8 text-gray-700 hover:text-gray-900"
              >
                Dashboard
              </Link>
            )}
          </div>

          <div className="flex items-center gap-4">
            <div className="flex items-center gap-2 text-sm text-gray-700">
              <UserIcon className="h-4 w-4" />
              <div>
                <div className="font-medium">{user.name}</div>
                <div className="text-xs text-gray-500">{teamName}</div>
              </div>
            </div>

            <Button variant="outline" size="sm" onClick={handleLogout}>
              <LogOut className="h-4 w-4 mr-2" />
              Logout
            </Button>
          </div>
        </div>
      </div>
    </nav>
  );
}
