'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { PlanningRequest } from '@/types';
import { planningRequestsApi } from '@/lib/api';
import { requireAuth } from '@/lib/auth';
import { Navbar } from '@/components/navbar';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Plus, FileText, Clock, User, Check } from 'lucide-react';

export default function TeamADashboard() {
  const [requests, setRequests] = useState<PlanningRequest[]>([]);
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState<'all' | 'draft' | 'submitted'>('all');

  useEffect(() => {
    // Check authentication - redirect to login if not authenticated
    if (!requireAuth('team_a')) {
      return;
    }
    loadRequests();
  }, [filter]);

  const loadRequests = async () => {
    try {
      setLoading(true);
      let response;
      if (filter === 'draft') {
        response = await planningRequestsApi.getDraft();
      } else if (filter === 'submitted') {
        response = await planningRequestsApi.getSubmitted();
      } else {
        response = await planningRequestsApi.getAll();
      }
      setRequests(Array.isArray(response.data) ? response.data : Array.isArray(response) ? response : []);
    } catch (error) {
      console.error('Failed to load requests:', error);
      setRequests([]);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <Navbar />
      <div className="max-w-7xl mx-auto p-8">
        <div className="flex justify-between items-center mb-8">
          <div className="flex items-center gap-3">
            <div className="p-3 bg-blue-100 rounded-full">
              <FileText className="w-6 h-6 text-blue-600" />
            </div>
            <div>
              <h1 className="text-3xl font-bold text-gray-900">Team A Dashboard</h1>
              <p className="text-gray-600 mt-1">Manage your planning requests</p>
            </div>
          </div>
          <Link href="/team-a/requests/new">
            <Button size="lg">
              <Plus className="w-4 h-4 mr-2" />
              Create New Request
            </Button>
          </Link>
        </div>

        <Card className="mb-6 bg-white shadow-sm">
          <CardContent className="pt-6">
            <div className="flex gap-3 flex-wrap">
              <Button
                onClick={() => setFilter('all')}
                variant={filter === 'all' ? 'default' : 'outline'}
                className={filter === 'all' ? 'bg-blue-600 hover:bg-blue-700' : 'hover:bg-blue-50 hover:text-blue-600 hover:border-blue-600'}
              >
                All Requests
              </Button>
              <Button
                onClick={() => setFilter('draft')}
                variant={filter === 'draft' ? 'default' : 'outline'}
                className={filter === 'draft' ? 'bg-yellow-600 hover:bg-yellow-700' : 'hover:bg-yellow-50 hover:text-yellow-600 hover:border-yellow-600'}
              >
                <Clock className="w-4 h-4 mr-2" />
                Drafts
              </Button>
              <Button
                onClick={() => setFilter('submitted')}
                variant={filter === 'submitted' ? 'default' : 'outline'}
                className={filter === 'submitted' ? 'bg-green-600 hover:bg-green-700' : 'hover:bg-green-50 hover:text-green-600 hover:border-green-600'}
              >
                <Check className="w-4 h-4 mr-2" />
                Submitted
              </Button>
            </div>
          </CardContent>
        </Card>

        {loading ? (
          <Card>
            <CardContent className="pt-6">
              <div className="text-center py-12 text-gray-500">
                Loading requests...
              </div>
            </CardContent>
          </Card>
        ) : requests.length === 0 ? (
          <Card>
            <CardContent className="pt-6">
              <div className="text-center py-12">
                <FileText className="w-12 h-12 mx-auto text-gray-400 mb-4" />
                <p className="text-gray-500 mb-4">No planning requests found</p>
                <Link href="/team-a/requests/new">
                  <Button>
                    <Plus className="w-4 h-4 mr-2" />
                    Create Your First Request
                  </Button>
                </Link>
              </div>
            </CardContent>
          </Card>
        ) : (
          <div className="space-y-4">
            {requests.map((request) => (
              <Link key={request.id} href={`/team-a/requests/${request.id}`}>
                <Card className="hover:shadow-xl transition-all duration-200 cursor-pointer border-l-4 border-l-blue-500 bg-white">
                  <CardHeader>
                    <div className="flex justify-between items-start">
                      <div className="flex-1">
                        <div className="flex items-center gap-2 mb-2">
                          <div className="p-2 bg-blue-50 rounded-lg">
                            <FileText className="w-5 h-5 text-blue-600" />
                          </div>
                          <CardTitle className="text-xl text-gray-900">
                            Request #{request.id}
                          </CardTitle>
                        </div>
                        <CardDescription className="space-y-2 ml-11">
                          <div className="flex items-center gap-2 text-gray-600">
                            <User className="w-4 h-4" />
                            <span>{request.creator?.name || 'Unknown'}</span>
                          </div>
                          <div className="flex items-center gap-2 text-gray-600">
                            <FileText className="w-4 h-4" />
                            <span>{request.items?.length || 0} items</span>
                          </div>
                        </CardDescription>
                      </div>
                      <div className="text-right flex flex-col items-end gap-2">
                        <Badge
                          variant={request.status === 'submitted' ? 'default' : 'secondary'}
                          className={request.status === 'submitted' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}
                        >
                          {request.status === 'submitted' ? (
                            <><Check className="w-3 h-3 mr-1" /> Submitted</>
                          ) : (
                            <><Clock className="w-3 h-3 mr-1" /> Draft</>
                          )}
                        </Badge>
                        <span className="text-sm text-gray-500">
                          {new Date(request.created_at).toLocaleDateString()}
                        </span>
                      </div>
                    </div>
                  </CardHeader>
                </Card>
              </Link>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
