<?php

    namespace App\Http\Controllers;

    use App\DTO\LeadFilterDto;
    use App\Services\AmoCrmService;
    use App\Services\LeadService;
    use Illuminate\Http\Request;

    class LeadController extends Controller
    {
        public function __construct(
            protected LeadService $leadService,
            protected AmoCrmService $amo
        )
        {
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
                $filterDto = new LeadFilterDto(
                    page: (int) $request->get('page', 1),
                    limit: (int) $request->get('limit', 25),
                    sortBy: $request->get('sortBy', 'updated_at'),
                    sortDirection: $request->get('sortDirection', 'desc'),
                );

                $leads = $this->leadService->getFormattedLeads($filterDto);

                return response()->json(['data' => $leads]);
            } catch (\Throwable $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }


    }
