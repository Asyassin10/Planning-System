'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { authApi } from '@/lib/auth-api';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { LogIn, User, Mail, Lock } from 'lucide-react';

export default function LoginPage() {
  const router = useRouter();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      const response = await authApi.login(email, password);

      // Store token and user in localStorage
      localStorage.setItem('auth_token', response.token);
      localStorage.setItem('user', JSON.stringify(response.user));

      // Redirect based on role
      if (response.user.role === 'team_a') {
        router.push('/team-a');
      } else if (response.user.role === 'team_b') {
        router.push('/team-b');
      } else if (response.user.role === 'team_c') {
        router.push('/team-c');
      } else {
        router.push('/');
      }
    } catch (err: any) {
      setError(err.message || 'Login failed');
    } finally {
      setLoading(false);
    }
  };

  const handleQuickLogin = async (testEmail: string) => {
    setEmail(testEmail);
    setPassword('password');

    try {
      setLoading(true);
      const response = await authApi.login(testEmail, 'password');

      localStorage.setItem('auth_token', response.token);
      localStorage.setItem('user', JSON.stringify(response.user));

      if (response.user.role === 'team_a') {
        router.push('/team-a');
      } else if (response.user.role === 'team_b') {
        router.push('/team-b');
      } else if (response.user.role === 'team_c') {
        router.push('/team-c');
      } else {
        router.push('/');
      }
    } catch (err: any) {
      setError(err.message || 'Login failed');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-50 p-4">
      <div className="w-full max-w-md">
        <Card>
          <CardHeader className="text-center">
            <div className="flex justify-center mb-4">
              <div className="p-3 bg-blue-100 rounded-full">
                <User className="w-8 h-8 text-blue-600" />
              </div>
            </div>
            <CardTitle className="text-2xl text-blue-600">Welcome Back</CardTitle>
            <CardDescription className="text-blue-600">Sign in to your account</CardDescription>
          </CardHeader>
          <CardContent>
            {error && (
              <div className="mb-4 p-3 bg-red-100 text-red-700 rounded-md text-sm">
                {error}
              </div>
            )}

            <form onSubmit={handleLogin} className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="email" className="text-blue-600">Email</Label>
                <div className="relative">
                  <Mail className="absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-400 w-4 h-4" />
                  <Input
                    id="email"
                    type="email"
                    placeholder="your@email.com"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    className="pl-10 text-blue-600"
                    required
                  />
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="password" className="text-blue-600">Password</Label>
                <div className="relative">
                  <Lock className="absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-400 w-4 h-4" />
                  <Input
                    id="password"
                    type="password"
                    placeholder="••••••••"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    className="pl-10 text-blue-600"
                    required
                  />
                </div>
              </div>

              <Button type="submit" className="w-full bg-blue-600 hover:bg-blue-700 text-white" disabled={loading}>
                <LogIn className="w-4 h-4 mr-2" />
                {loading ? 'Signing in...' : 'Sign In'}
              </Button>
            </form>

            <div className="mt-6">
              <div className="relative">
                <div className="absolute inset-0 flex items-center">
                  <div className="w-full border-t border-gray-200"></div>
                </div>
                <div className="relative flex justify-center text-sm">
                  <span className="px-2 bg-white text-blue-600">Quick Test Login</span>
                </div>
              </div>

              <div className="mt-4 space-y-2">
                <Button
                  type="button"
                  variant="outline"
                  className="w-full text-blue-600 border-blue-600 hover:bg-blue-50"
                  onClick={() => handleQuickLogin('alice@example.com')}
                  disabled={loading}
                >
                  Team A (Alice)
                </Button>
                <Button
                  type="button"
                  variant="outline"
                  className="w-full text-blue-600 border-blue-600 hover:bg-blue-50"
                  onClick={() => handleQuickLogin('bob@example.com')}
                  disabled={loading}
                >
                  Team B (Bob)
                </Button>
                <Button
                  type="button"
                  variant="outline"
                  className="w-full text-blue-600 border-blue-600 hover:bg-blue-50"
                  onClick={() => handleQuickLogin('charlie@example.com')}
                  disabled={loading}
                >
                  Team C (Charlie)
                </Button>
              </div>

              <div className="mt-4 text-center text-sm text-blue-600">
                All test accounts use password: <code className="bg-blue-100 px-2 py-1 rounded text-blue-600">password</code>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
