'use client';

import { useEffect, useState } from 'react';
import { useRouter, useParams } from 'next/navigation';
import { PlanningRequest } from '@/types';
import { planningRequestsApi } from '@/lib/api';
import { requireAuth } from '@/lib/auth';
import { Navbar } from '@/components/navbar';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, Trash2, Send, Check, Clock, User, FileText } from 'lucide-react';

export default function PlanningRequestDetails() {
  const router = useRouter();
  const params = useParams();
  const [request, setRequest] = useState<PlanningRequest | null>(null);
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    // Check authentication
    if (!requireAuth('team_a')) {
      return;
    }
    loadRequest();
  }, []);

  const loadRequest = async () => {
    try {
      setLoading(true);
      const response = await planningRequestsApi.getOne(Number(params.id));
      // Extract the data from the response object
      const data = response?.data || response;
      setRequest(data);
    } catch (err: any) {
      setError(err.message || 'Failed to load request');
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async () => {
    if (!confirm('Are you sure you want to submit this request? It will become immutable.')) {
      return;
    }

    try {
      setSubmitting(true);
      await planningRequestsApi.submit(Number(params.id));
      await loadRequest();
    } catch (err: any) {
      setError(err.message || 'Failed to submit request');
    } finally {
      setSubmitting(false);
    }
  };

  const handleDelete = async () => {
    if (!confirm('Are you sure you want to delete this request?')) {
      return;
    }

    try {
      await planningRequestsApi.delete(Number(params.id));
      router.push('/team-a');
    } catch (err: any) {
      setError(err.message || 'Failed to delete request');
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50">
        <Navbar />
        <div className="max-w-4xl mx-auto p-8">
          <Card>
            <CardContent className="pt-6">
              <div className="text-center py-12 text-gray-500">
                Loading request details...
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    );
  }

  if (!request) {
    return (
      <div className="min-h-screen bg-gray-50">
        <Navbar />
        <div className="max-w-4xl mx-auto p-8">
          <Card>
            <CardContent className="pt-6">
              <div className="text-center py-12">
                <FileText className="w-12 h-12 mx-auto text-gray-400 mb-4" />
                <p className="text-gray-500">Request not found</p>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <Navbar />
      <div className="max-w-4xl mx-auto p-8">
        <div className="mb-8">
          <Button
            onClick={() => router.push('/team-a')}
            variant="ghost"
            className="mb-4"
          >
            <ArrowLeft className="h-4 w-4 mr-2" />
            Back to Requests
          </Button>

          <div className="flex justify-between items-start">
            <div className="flex items-start gap-3">
              <div className="p-3 bg-blue-100 rounded-full">
                <FileText className="w-6 h-6 text-blue-600" />
              </div>
              <div>
                <h1 className="text-3xl font-bold mb-2 text-gray-900">
                  Planning Request #{request.id}
                </h1>
                <p className="text-gray-600">
                  Created by {request.creator?.name || 'Unknown'} on{' '}
                  {new Date(request.created_at).toLocaleDateString()}
                </p>
              </div>
            </div>
            <Badge variant={request.status === 'submitted' ? 'default' : 'secondary'} className="text-sm">
              {request.status === 'submitted' ? (
                <><Check className="h-3 w-3 mr-1" /> Submitted</>
              ) : (
                <><Clock className="h-3 w-3 mr-1" /> Draft</>
              )}
            </Badge>
          </div>
        </div>

        {error && (
          <div className="mb-4 p-3 bg-red-100 text-red-700 rounded-md text-sm">
            {error}
          </div>
        )}

        <Card className="mb-6 bg-white shadow-md">
          <CardHeader className="bg-blue-50 border-b border-blue-100">
            <CardTitle className="text-blue-900">Request Items</CardTitle>
            <CardDescription>Details of the requested planning items</CardDescription>
          </CardHeader>
          <CardContent className="pt-6">
            <div className="space-y-4">
              {Array.isArray(request.items) && request.items.map((item, index) => (
                <Card key={index} className="border-l-4 border-l-blue-400 bg-gradient-to-r from-blue-50 to-white hover:shadow-md transition-shadow">
                  <CardContent className="pt-6">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div className="flex items-center gap-2">
                        <div className="p-2 bg-blue-100 rounded-lg">
                          <FileText className="w-4 h-4 text-blue-600" />
                        </div>
                        <div>
                          <span className="text-sm text-gray-500">Route</span>
                          <div className="font-medium text-gray-900">
                            {item.route?.name || 'N/A'} ({item.route?.identifier})
                          </div>
                        </div>
                      </div>
                      <div className="flex items-center gap-2">
                        <div className="p-2 bg-blue-100 rounded-lg">
                          <User className="w-4 h-4 text-blue-600" />
                        </div>
                        <div>
                          <span className="text-sm text-gray-500">Capacity</span>
                          <div className="font-medium text-gray-900">{item.capacity}</div>
                        </div>
                      </div>
                      <div className="flex items-center gap-2">
                        <div className="p-2 bg-blue-100 rounded-lg">
                          <Clock className="w-4 h-4 text-blue-600" />
                        </div>
                        <div>
                          <span className="text-sm text-gray-500">Start Date</span>
                          <div className="font-medium text-gray-900">
                            {new Date(item.start_date).toLocaleDateString()}
                          </div>
                        </div>
                      </div>
                      <div className="flex items-center gap-2">
                        <div className="p-2 bg-blue-100 rounded-lg">
                          <Clock className="w-4 h-4 text-blue-600" />
                        </div>
                        <div>
                          <span className="text-sm text-gray-500">End Date</span>
                          <div className="font-medium text-gray-900">
                            {new Date(item.end_date).toLocaleDateString()}
                          </div>
                        </div>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              ))}
            </div>
          </CardContent>
        </Card>

        {request.status === 'draft' && (
          <div className="flex gap-4">
            <Button
              onClick={handleDelete}
              variant="outline"
              className="flex-1 border-2 border-red-500 text-red-600 hover:bg-red-50 hover:border-red-600 transition-all"
            >
              <Trash2 className="h-4 w-4 mr-2" />
              Delete Request
            </Button>
            <Button
              onClick={handleSubmit}
              disabled={submitting}
              className="flex-1 bg-blue-600 hover:bg-blue-700 transition-all shadow-md hover:shadow-lg"
            >
              <Send className="h-4 w-4 mr-2" />
              {submitting ? 'Submitting...' : 'Submit Request'}
            </Button>
          </div>
        )}

        {request.status === 'submitted' && request.submitted_at && (
          <Card className="bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-300 shadow-md">
            <CardContent className="pt-6">
              <div className="flex items-start gap-3">
                <div className="p-2 bg-green-200 rounded-full">
                  <Check className="h-5 w-5 text-green-700" />
                </div>
                <div className="flex-1">
                  <p className="font-semibold text-green-900 text-lg">
                    Successfully Submitted
                  </p>
                  <p className="text-green-700 text-sm mt-1">
                    {new Date(request.submitted_at).toLocaleString()}
                  </p>
                  <div className="mt-3 p-3 bg-white rounded-lg border border-green-200">
                    <p className="text-sm text-gray-700">
                      This request is now <span className="font-semibold text-green-800">immutable</span> and available for Team B to create operational plans.
                    </p>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        )}
      </div>
    </div>
  );
}
