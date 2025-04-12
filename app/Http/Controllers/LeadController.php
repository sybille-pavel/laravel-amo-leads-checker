<?php

    namespace App\Http\Controllers;

    use App\DTO\LeadFilterDto;
    use App\Services\AmoCrmAuthService;
    use App\Services\LeadService;
    use Illuminate\Http\Request;

    class LeadController extends Controller
    {
        public function __construct(
            protected LeadService $leadService,
            protected AmoCrmAuthService $amo
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

        public function api(Request $request)
        {
            if (!$this->amo->hasToken()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            try {
                $filterDto = LeadFilterDto::fromRequest($request);

                $leads = $this->leadService->getFormattedLeads($filterDto);

                return response()->json(['data' => $leads]);
            } catch (\Throwable $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }


    }
