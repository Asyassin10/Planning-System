'use client';

import { useEffect, useState } from 'react';
import { useRouter, useParams } from 'next/navigation';
import { OperationalPlan, Resource } from '@/types';
import { operationalPlansApi, resourcesApi } from '@/lib/api';
import { requireAuth } from '@/lib/auth';
import { Navbar } from '@/components/navbar';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, Plus, X, Save, Check, FileText, Clock, User } from 'lucide-react';

export default function OperationalPlanDetails() {
  const router = useRouter();
  const params = useParams();
  const [plan, setPlan] = useState<OperationalPlan | null>(null);
  const [resources, setResources] = useState<Resource[]>([]);
  const [loading, setLoading] = useState(true);
  const [showVersionForm, setShowVersionForm] = useState(false);

  useEffect(() => {
    // Check authentication
    if (!requireAuth('team_b')) {
      return;
    }
    loadData();
  }, []);

  const loadData = async () => {
    try {
      setLoading(true);
      const [planData, resourcesData] = await Promise.all([
        operationalPlansApi.getOne(Number(params.id)),
        resourcesApi.getAll({ active_only: true })
      ]);
      setPlan(planData.data || planData);
      setResources(resourcesData.data || resourcesData);
    } catch (error) {
      console.error('Failed to load data:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleActivateVersion = async (versionId: number) => {
    try {
      await operationalPlansApi.activateVersion(versionId);
      await loadData();
    } catch (error: any) {
      alert(error.message || 'Failed to activate version');
    }
  };

  if (loading || !plan) {
    return (
      <div className="min-h-screen bg-gray-50">
        <Navbar />
        <div className="max-w-6xl mx-auto p-8">
          <Card>
            <CardContent className="pt-6">
              <div className="text-center py-12">
                {loading ? (
                  <div className="text-gray-500">Loading plan details...</div>
                ) : (
                  <>
                    <FileText className="w-12 h-12 mx-auto text-gray-400 mb-4" />
                    <p className="text-gray-500">Plan not found</p>
                  </>
                )}
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
      <div className="max-w-6xl mx-auto p-8">
        <Button
          onClick={() => router.push('/team-b')}
          variant="ghost"
          className="mb-4"
        >
          <ArrowLeft className="h-4 w-4 mr-2" />
          Back to Dashboard
        </Button>

        <div className="mb-8">
          <div className="flex items-start gap-3 mb-2">
            <div className="p-3 bg-green-100 rounded-full">
              <FileText className="w-6 h-6 text-green-600" />
            </div>
            <div>
              <h1 className="text-3xl font-bold mb-2 text-gray-900">
                Operational Plan #{plan.id}
              </h1>
              <p className="text-gray-600">
                Route: {plan.planning_request_item?.route?.name || 'N/A'}
              </p>
              <p className="text-gray-600">
                Period: {plan.planning_request_item?.start_date} to{' '}
                {plan.planning_request_item?.end_date}
              </p>
            </div>
          </div>
        </div>

        <div className="mb-6">
          <Button
            onClick={() => setShowVersionForm(!showVersionForm)}
          >
            {showVersionForm ? (
              <><X className="h-4 w-4 mr-2" /> Cancel</>
            ) : (
              <><Plus className="h-4 w-4 mr-2" /> Create New Version</>
            )}
          </Button>
        </div>

        {showVersionForm && (
          <VersionForm
            planId={plan.id}
            resources={resources}
            onSuccess={() => {
              setShowVersionForm(false);
              loadData();
            }}
          />
        )}

        <div className="space-y-6">
          <div className="flex items-center gap-2">
            <div className="p-2 bg-green-100 rounded-lg">
              <FileText className="w-5 h-5 text-green-600" />
            </div>
            <h2 className="text-2xl font-semibold text-gray-900">Versions</h2>
          </div>
          {Array.isArray(plan.versions) && plan.versions.length > 0 ? (
            plan.versions
              .sort((a, b) => b.version - a.version)
              .map((version) => (
                <Card key={version.id} className={`border-l-4 shadow-md ${version.is_active ? 'border-l-green-500 bg-gradient-to-r from-green-50 to-white' : 'border-l-gray-300 bg-white'}`}>
                  <CardHeader className={version.is_active ? 'bg-green-50 border-b border-green-100' : ''}>
                    <div className="flex justify-between items-start">
                      <div className="flex-1">
                        <div className="flex items-center gap-2 mb-2">
                          <div className={`p-2 rounded-lg ${version.is_active ? 'bg-green-100' : 'bg-gray-100'}`}>
                            <FileText className={`w-5 h-5 ${version.is_active ? 'text-green-600' : 'text-gray-600'}`} />
                          </div>
                          <CardTitle className="flex items-center gap-2 text-xl text-gray-900">
                            Version {version.version}
                            {version.is_active && (
                              <Badge className="bg-green-600 text-white">
                                <Check className="h-3 w-3 mr-1" />
                                Active
                              </Badge>
                            )}
                          </CardTitle>
                        </div>
                        <CardDescription className="ml-11">
                          <div className="flex items-center gap-2 text-gray-700">
                            <Clock className="w-4 h-4" />
                            Valid: {new Date(version.valid_from).toLocaleDateString()} -{' '}
                            {new Date(version.valid_to).toLocaleDateString()}
                          </div>
                        </CardDescription>
                        <p className="text-gray-600 text-sm mt-2 ml-11 flex items-center gap-2">
                          <User className="w-4 h-4" />
                          Created by {version.creator?.name || 'Unknown'} on{' '}
                          {new Date(version.created_at).toLocaleDateString()}
                        </p>
                      </div>
                      {!version.is_active && (
                        <Button
                          onClick={() => handleActivateVersion(version.id)}
                          className="bg-green-600 hover:bg-green-700 shadow-md hover:shadow-lg transition-all"
                        >
                          <Check className="h-4 w-4 mr-2" />
                          Activate
                        </Button>
                      )}
                    </div>
                  </CardHeader>
                  <CardContent>
                    {version.notes && (
                      <div className="mb-4 p-3 bg-gray-50 rounded">
                        <strong>Notes:</strong> {version.notes}
                      </div>
                    )}

                    {Array.isArray(version.resources) && version.resources.length > 0 && (
                      <div>
                        <div className="flex items-center gap-2 mb-3">
                          <div className="p-1.5 bg-green-100 rounded">
                            <User className="w-4 h-4 text-green-600" />
                          </div>
                          <h4 className="font-semibold text-gray-900">Resources:</h4>
                        </div>
                        <div className="space-y-2">
                          {version.resources.map((vr) => (
                            <Card key={vr.id} className="border border-green-200 bg-gradient-to-r from-green-50 to-white">
                              <CardContent className="pt-4 flex justify-between items-center">
                                <div className="flex items-center gap-3">
                                  <div className="p-2 bg-green-100 rounded-lg">
                                    <User className="w-4 h-4 text-green-600" />
                                  </div>
                                  <div>
                                    <div className="font-semibold text-gray-900">{vr.resource?.name}</div>
                                    <div className="text-sm text-gray-600">
                                      Type: {vr.resource?.type}
                                    </div>
                                  </div>
                                </div>
                                <div className="text-right">
                                  <div className="text-sm font-medium text-gray-900">Capacity: {vr.capacity}</div>
                                  <Badge className={`text-xs mt-1 ${vr.is_permanent ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800'}`}>
                                    {vr.is_permanent ? 'Permanent' : 'Temporary'}
                                  </Badge>
                                </div>
                              </CardContent>
                            </Card>
                          ))}
                        </div>
                      </div>
                    )}
                  </CardContent>
                </Card>
              ))
          ) : (
            <Card>
              <CardContent className="pt-6">
                <p className="text-center text-gray-500">
                  No versions yet. Create the first version above.
                </p>
              </CardContent>
            </Card>
          )}
        </div>
      </div>
    </div>
  );
}

function VersionForm({
  planId,
  resources,
  onSuccess
}: {
  planId: number;
  resources: Resource[];
  onSuccess: () => void;
}) {
  const [formData, setFormData] = useState({
    valid_from: '',
    valid_to: '',
    notes: '',
    resources: [] as any[]
  });
  const [submitting, setSubmitting] = useState(false);

  const addResource = () => {
    setFormData({
      ...formData,
      resources: [
        ...formData.resources,
        { resource_id: 0, capacity: 1, is_permanent: true, notes: '' }
      ]
    });
  };

  const removeResource = (index: number) => {
    setFormData({
      ...formData,
      resources: formData.resources.filter((_, i) => i !== index)
    });
  };

  const updateResource = (index: number, field: string, value: any) => {
    const newResources = [...formData.resources];
    newResources[index] = { ...newResources[index], [field]: value };
    setFormData({ ...formData, resources: newResources });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitting(true);

    try {
      await operationalPlansApi.createVersion(planId, formData);
      onSuccess();
    } catch (error: any) {
      alert(error.message || 'Failed to create version');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <Card className="mb-6 bg-white shadow-lg border-l-4 border-l-green-500">
      <CardHeader className="bg-green-50 border-b border-green-100">
        <div className="flex items-center gap-2">
          <div className="p-2 bg-green-100 rounded-lg">
            <Plus className="w-5 h-5 text-green-600" />
          </div>
          <div>
            <CardTitle className="text-green-900">Create New Version</CardTitle>
            <CardDescription>Add a new version to this operational plan</CardDescription>
          </div>
        </div>
      </CardHeader>
      <CardContent>
        <form onSubmit={handleSubmit}>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div className="space-y-2">
              <Label htmlFor="valid-from" className="text-blue-600 font-medium">Valid From</Label>
              <Input
                id="valid-from"
                type="date"
                value={formData.valid_from}
                onChange={(e) => setFormData({ ...formData, valid_from: e.target.value })}
                className="border-2 border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all text-blue-600"
                required
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="valid-to" className="text-blue-600 font-medium">Valid To</Label>
              <Input
                id="valid-to"
                type="date"
                value={formData.valid_to}
                onChange={(e) => setFormData({ ...formData, valid_to: e.target.value })}
                className="border-2 border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all text-blue-600"
                required
              />
            </div>
          </div>

          <div className="mb-4 space-y-2">
            <Label htmlFor="notes" className="text-blue-600 font-medium">Notes</Label>
            <textarea
              id="notes"
              value={formData.notes}
              onChange={(e) => setFormData({ ...formData, notes: e.target.value })}
              className="w-full p-2 border-2 border-gray-200 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all text-blue-600"
              rows={3}
              placeholder="Add any notes about this version..."
            />
          </div>

          <div className="mb-4">
            <div className="flex justify-between items-center mb-3">
              <div className="flex items-center gap-2">
                <div className="p-1.5 bg-green-100 rounded">
                  <User className="w-4 h-4 text-green-600" />
                </div>
                <h4 className="font-semibold text-gray-900">Resources</h4>
              </div>
              <Button
                type="button"
                onClick={addResource}
                variant="outline"
                size="sm"
                className="border-2 border-green-300 text-green-600 hover:bg-green-50 hover:border-green-500"
              >
                <Plus className="h-4 w-4 mr-2" />
                Add Resource
              </Button>
            </div>

            {Array.isArray(formData.resources) && formData.resources.map((resource, index) => (
              <Card key={index} className="mb-3 border border-green-200 bg-gradient-to-r from-green-50 to-white">
                <CardContent className="pt-4">
                  <div className="flex justify-between mb-3">
                    <div className="flex items-center gap-2">
                      <div className="p-1.5 bg-green-100 rounded">
                        <User className="w-4 h-4 text-green-600" />
                      </div>
                      <span className="font-semibold text-gray-900">Resource {index + 1}</span>
                    </div>
                    <Button
                      type="button"
                      onClick={() => removeResource(index)}
                      variant="ghost"
                      size="sm"
                      className="hover:bg-red-50 hover:text-red-600"
                    >
                      <X className="h-4 w-4" />
                    </Button>
                  </div>
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <select
                      value={resource.resource_id}
                      onChange={(e) => updateResource(index, 'resource_id', Number(e.target.value))}
                      className="p-2 border-2 border-gray-200 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all text-blue-600"
                      required
                    >
                      <option value="" className="text-blue-600">Select resource</option>
                      {Array.isArray(resources) && resources.map((r) => (
                        <option key={r.id} value={r.id} className="text-blue-600">
                          {r.name} ({r.type})
                        </option>
                      ))}
                    </select>
                    <Input
                      type="number"
                      min="1"
                      placeholder="Capacity"
                      value={resource.capacity}
                      onChange={(e) => updateResource(index, 'capacity', Number(e.target.value))}
                      className="border-2 border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all text-blue-600"
                      required
                    />
                    <select
                      value={resource.is_permanent ? 'true' : 'false'}
                      onChange={(e) => updateResource(index, 'is_permanent', e.target.value === 'true')}
                      className="p-2 border-2 border-gray-200 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all text-blue-600"
                    >
                      <option value="true" className="text-blue-600">Permanent</option>
                      <option value="false" className="text-blue-600">Temporary</option>
                    </select>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>

          <div className="relative my-6">
            <div className="absolute inset-0 flex items-center">
              <div className="w-full border-t border-gray-200"></div>
            </div>
            <div className="relative flex justify-center text-sm">
              <span className="px-2 bg-white text-gray-500">Ready to create?</span>
            </div>
          </div>

          <div className="flex gap-4">
            <Button
              type="button"
              onClick={onSuccess}
              variant="outline"
              className="flex-1 border-2 border-gray-300 hover:bg-gray-50 transition-all"
            >
              Cancel
            </Button>
            <Button
              type="submit"
              disabled={submitting}
              className="flex-1 bg-green-600 hover:bg-green-700 transition-all shadow-md hover:shadow-lg"
            >
              <Save className="h-4 w-4 mr-2" />
              {submitting ? 'Creating...' : 'Create Version'}
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>
  );
}
