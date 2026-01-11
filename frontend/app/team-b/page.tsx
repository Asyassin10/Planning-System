'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { PlanningRequest, OperationalPlan } from '@/types';
import { planningRequestsApi, operationalPlansApi } from '@/lib/api';
import { requireAuth } from '@/lib/auth';
import { Navbar } from '@/components/navbar';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { FileText, Plus, Check, Clock } from 'lucide-react';

export default function TeamBDashboard() {
  const [submittedRequests, setSubmittedRequests] = useState<PlanningRequest[]>([]);
  const [existingPlans, setExistingPlans] = useState<OperationalPlan[]>([]);
  const [loading, setLoading] = useState(true);
  const [creating, setCreating] = useState<number | null>(null);

  useEffect(() => {
    // Check authentication - redirect to login if not authenticated
    if (!requireAuth('team_b')) {
      return;
    }
    loadData();
  }, []);

  const loadData = async () => {
    try {
      setLoading(true);
      const [requests, plans] = await Promise.all([
        planningRequestsApi.getSubmitted(),
        operationalPlansApi.getAll()
      ]);
      setSubmittedRequests(requests.data || requests);
      setExistingPlans(plans.data || plans);
    } catch (error) {
      console.error('Failed to load data:', error);
    } finally {
      setLoading(false);
    }
  };

  const createPlan = async (itemId: number) => {
    try {
      setCreating(itemId);

      // Find the item to get its dates
      const item = submittedRequests
        .flatMap(r => r.items || [])
        .find(i => i.id === itemId);

      if (!item) {
        throw new Error('Item not found');
      }

      // Format dates to YYYY-MM-DD
      const formatDate = (dateStr: string) => {
        return dateStr.split('T')[0];
      };

      await operationalPlansApi.create({
        planning_request_item_id: itemId,
        version: {
          valid_from: formatDate(item.start_date),
          valid_to: formatDate(item.end_date),
          notes: 'Initial operational plan version',
          resources: []
        }
      });

      await loadData();
    } catch (error: any) {
      alert(error.message || 'Failed to create plan');
    } finally {
      setCreating(null);
    }
  };

  const hasPlan = (itemId: number) => {
    return existingPlans.some(p => p.planning_request_item_id === itemId);
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <Navbar />
      <div className="max-w-7xl mx-auto p-8">
        <div className="mb-8">
          <div className="flex items-center gap-3 mb-2">
            <div className="p-3 bg-green-100 rounded-full">
              <FileText className="w-6 h-6 text-green-600" />
            </div>
            <h1 className="text-3xl font-bold text-gray-900">Team B - Operational Planning</h1>
          </div>
          <p className="text-gray-600 ml-16">Create and manage operational plans with resource allocation</p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          {/* Submitted Requests */}
          <div>
            <h2 className="text-2xl font-semibold mb-4">Submitted Requests</h2>
            {loading ? (
              <Card>
                <CardContent className="pt-6">
                  <div className="text-center py-12 text-gray-500">
                    Loading requests...
                  </div>
                </CardContent>
              </Card>
            ) : !Array.isArray(submittedRequests) || submittedRequests.length === 0 ? (
              <Card>
                <CardContent className="pt-6">
                  <div className="text-center py-12">
                    <FileText className="w-12 h-12 mx-auto text-gray-400 mb-4" />
                    <p className="text-gray-500">No submitted requests yet</p>
                  </div>
                </CardContent>
              </Card>
            ) : (
              <div className="space-y-4">
                {submittedRequests.map((request) => (
                  <Card key={request.id} className="border-l-4 border-l-green-500 bg-white shadow-md hover:shadow-lg transition-shadow">
                    <CardHeader className="bg-green-50">
                      <div className="flex items-center gap-2">
                        <div className="p-2 bg-green-100 rounded-lg">
                          <FileText className="w-5 h-5 text-green-600" />
                        </div>
                        <CardTitle className="text-lg text-gray-900">Request #{request.id}</CardTitle>
                      </div>
                    </CardHeader>
                    <CardContent className="pt-4">
                      <div className="space-y-3">
                        {Array.isArray(request.items) && request.items.map((item) => (
                          <Card key={item.id} className="bg-gradient-to-r from-green-50 to-white border border-green-200">
                            <CardContent className="pt-4 flex justify-between items-center">
                              <div className="flex items-center gap-3">
                                <div className="p-2 bg-green-100 rounded-lg">
                                  <FileText className="w-4 h-4 text-green-600" />
                                </div>
                                <div className="text-sm">
                                  <div className="font-semibold text-gray-900">{item.route?.name}</div>
                                  <div className="text-gray-600 flex items-center gap-1 mt-1">
                                    <Clock className="w-3 h-3" />
                                    {new Date(item.start_date).toLocaleDateString()} -{' '}
                                    {new Date(item.end_date).toLocaleDateString()}
                                  </div>
                                </div>
                              </div>
                              {hasPlan(item.id!) ? (
                                <Badge className="gap-1 bg-green-100 text-green-800">
                                  <Check className="h-3 w-3" /> Plan exists
                                </Badge>
                              ) : (
                                <Button
                                  onClick={() => createPlan(item.id!)}
                                  disabled={creating === item.id}
                                  size="sm"
                                  className="bg-green-600 hover:bg-green-700 shadow-sm"
                                >
                                  <Plus className="h-4 w-4 mr-1" />
                                  {creating === item.id ? 'Creating...' : 'Create Plan'}
                                </Button>
                              )}
                            </CardContent>
                          </Card>
                        ))}
                      </div>
                    </CardContent>
                  </Card>
                ))}
              </div>
            )}
          </div>

          {/* Existing Plans */}
          <div>
            <h2 className="text-2xl font-semibold mb-4">Existing Plans</h2>
            {loading ? (
              <Card>
                <CardContent className="pt-6">
                  <div className="text-center py-12 text-gray-500">
                    Loading plans...
                  </div>
                </CardContent>
              </Card>
            ) : !Array.isArray(existingPlans) || existingPlans.length === 0 ? (
              <Card>
                <CardContent className="pt-6">
                  <div className="text-center py-12">
                    <FileText className="w-12 h-12 mx-auto text-gray-400 mb-4" />
                    <p className="text-gray-500">No operational plans yet</p>
                    <p className="text-sm text-gray-400 mt-2">Create plans from submitted requests</p>
                  </div>
                </CardContent>
              </Card>
            ) : (
              <div className="space-y-4">
                {existingPlans.map((plan) => (
                  <Link key={plan.id} href={`/team-b/plans/${plan.id}`}>
                    <Card className="hover:shadow-xl transition-all duration-200 cursor-pointer border-l-4 border-l-green-500 bg-white">
                      <CardHeader className="bg-gradient-to-r from-green-50 to-white">
                        <div className="flex justify-between items-start">
                          <div className="flex items-center gap-2">
                            <div className="p-2 bg-green-100 rounded-lg">
                              <FileText className="h-5 w-5 text-green-600" />
                            </div>
                            <CardTitle className="text-lg text-gray-900">
                              Plan #{plan.id}
                            </CardTitle>
                          </div>
                          {plan.active_version && (
                            <Badge className="bg-green-100 text-green-800 gap-1">
                              <Check className="h-3 w-3" />
                              Active v{plan.active_version.version}
                            </Badge>
                          )}
                        </div>
                        <CardDescription className="ml-11">
                          Route: {plan.planning_request_item?.route?.name || 'N/A'}
                        </CardDescription>
                      </CardHeader>
                      <CardContent>
                        <div className="flex items-center gap-2 text-sm text-gray-600">
                          <div className="p-1.5 bg-green-50 rounded">
                            <FileText className="h-3.5 w-3.5 text-green-600" />
                          </div>
                          <span>
                            {Array.isArray(plan.versions) ? plan.versions.length : 0} version{Array.isArray(plan.versions) && plan.versions.length !== 1 ? 's' : ''}
                          </span>
                        </div>
                      </CardContent>
                    </Card>
                  </Link>
                ))}
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
