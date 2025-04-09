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

        public function api(Request $request)
        {
            if (!$this->amo->hasToken()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            try {
                $leads = $this->amo->getLeads();
                $statuses = $this->amo->getStatusesByPipeline();

                $formattedLeads = collect($leads->all())->map(function ($lead) use ($statuses) {
                    $pipelineId = $lead->getPipelineId();
                    $statusId = $lead->getStatusId();

                    $statusName = $statuses[$pipelineId][$statusId] ?? 'Неизвестный статус';

                    $contact = null;

                    if ($lead->getContacts()) {
                        $firstContact = $lead->getContacts()->first();
                        $contact = $firstContact ? $firstContact->getName() : null;
                    }

                    return [
                        'id' => $lead->getId(),
                        'name' => $lead->getName(),
                        'status' => $statusName,
                        'contact' => $contact,
                        'updated_at' => $lead->getUpdatedAt()
                            ? \Carbon\Carbon::createFromTimestamp($lead->getUpdatedAt())->format('Y-m-d H:i:s')
                            : null,
                    ];
                });

                return response()->json($formattedLeads);
            } catch (\Throwable $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
    }
