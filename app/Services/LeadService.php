<?php
    namespace App\Services;

    use App\DTO\LeadFilterDto;
    use Illuminate\Support\Collection;

    class LeadService
    {
        public function __construct(
            protected AmoCrmApiService $amo,
        ) {}

        public function getFormattedLeads(LeadFilterDto $filterDto): Collection
        {
            $statuses = $this->amo->getStatusesByPipeline();
            $leads = $this->amo->getLeads($filterDto);
            $contacts = $this->getContacts($leads);

            return collect($leads->all())->map(function ($lead) use ($statuses, $contacts) {
                $contactId = $lead->getContacts()?->first()?->getId();
                $contactName = $contactId ? ($contacts->firstWhere('id', $contactId)['name'] ?? null) : null;

                return \App\DTO\LeadDto::fromEntity($lead, $statuses, $contactName);
            });
        }

        private function getContacts($leads): Collection
        {
            $contacts_id = collect($leads->all())->map(function ($lead) {
                return $lead->getContacts()?->first()?->getId();
            })->filter()->values()->toArray();

            return collect($this->amo->getContactsById($contacts_id)->toArray());
        }
    }

