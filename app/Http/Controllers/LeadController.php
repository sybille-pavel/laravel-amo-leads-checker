<?php

    namespace App\Http\Controllers;

    use App\Services\AmoCrmService;
    use Illuminate\Http\Request;

    class LeadController extends Controller
    {
        protected AmoCrmService $amo;

        public function __construct(AmoCrmService $amo)
        {
            $this->amo = $amo;
        }

        public function index(Request $request)
        {
            if (!$this->amo->hasToken()) {
                return redirect('/auth/redirect');
            }

            return view('leads.index', ['leads']);

        }

        private function getContacts($leads)
        {
            $contacts_id = collect($leads->all())->map(function ($lead){
                if ($lead->getContacts()) {
                    $firstContact = $lead->getContacts()->first();
                    return $firstContact->getId();
                }
                return null;
            });

            $contacts_id = array_values($contacts_id->filter()->toArray());

            $contacts = $this->amo->getContactsById($contacts_id);
            return collect($contacts->toArray());
        }

        public function api(Request $request)
        {
            if (!$this->amo->hasToken()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            try {
                $page = (int) $request->get('page', 1);
                $limit = (int) $request->get('limit', 25);
                $sortBy = $request->get('sortBy', 'updated_at');
                $sortDirection = $request->get('sortDirection', 'desc');

                $leads = $this->amo->getLeads($page, $limit, $sortBy, $sortDirection);
                $statuses = $this->amo->getStatusesByPipeline();

                $contacts = $this->getContacts($leads);

                $formattedLeads = collect($leads->all())->map(function ($lead) use ($statuses, $contacts) {
                    $pipelineId = $lead->getPipelineId();
                    $statusId = $lead->getStatusId();

                    $statusName = $statuses[$pipelineId][$statusId] ?? 'Неизвестный статус';

                    $contact = null;
                    if ($lead->getContacts()) {
                        $firstContact = $lead->getContacts()->first();
                        $contactInfo = $contacts->firstWhere('id', $firstContact->getId());
                        $contact = $contactInfo ? $contactInfo['name'] : null;
                    }

                    return [
                        'id' => $lead->getId(),
                        'name' => $lead->getName(),
                        'status' => $statusName,
                        'contact' => $contact,
                        'updated_at' => $lead->getUpdatedAt()
                            ? \Carbon\Carbon::createFromTimestamp($lead->getUpdatedAt())->format('Y-m-d H:i:s')
                            : null,
                        'created_at' => $lead->getCreatedAt()
                            ? \Carbon\Carbon::createFromTimestamp($lead->getCreatedAt())->format('Y-m-d H:i:s')
                            : null,
                    ];
                });

                return response()->json([
                    'data' => $formattedLeads
                ]);
            } catch (\Throwable $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }


    }
