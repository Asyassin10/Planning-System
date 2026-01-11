export interface User {
  id: number;
  name: string;
  email: string;
  role: 'team_a' | 'team_b' | 'team_c';
}

export function getUser(): User | null {
  if (typeof window === 'undefined') return null;

  const userStr = localStorage.getItem('user');
  if (!userStr) return null;

  try {
    return JSON.parse(userStr);
  } catch {
    return null;
  }
}

export function getToken(): string | null {
  if (typeof window === 'undefined') return null;
  return localStorage.getItem('auth_token');
}

export function isAuthenticated(): boolean {
  return !!getToken();
}

export function requireAuth(requiredRole?: 'team_a' | 'team_b' | 'team_c'): boolean {
  const user = getUser();

  if (!user || !isAuthenticated()) {
    if (typeof window !== 'undefined') {
      window.location.href = '/login';
    }
    return false;
  }

  if (requiredRole && user.role !== requiredRole) {
    if (typeof window !== 'undefined') {
      window.location.href = '/';
    }
    return false;
  }

  return true;
}

export function logout(): void {
  if (typeof window === 'undefined') return;

  localStorage.removeItem('auth_token');
  localStorage.removeItem('user');
  window.location.href = '/login';
}
