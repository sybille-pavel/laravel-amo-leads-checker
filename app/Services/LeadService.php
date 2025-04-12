<?php
    namespace App\Services;

    use App\DTO\LeadFilterDto;
    use Illuminate\Support\Collection;

    class LeadService
    {
        public function __construct(
            protected AmoCrmService $amo,
        ) {}

        public function getFormattedLeads(LeadFilterDto $filterDto): Collection
        {
            $leads = $this->amo->getLeads($filterDto);
            $contacts = $this->getContacts($leads);
            $statuses = $this->amo->getStatusesByPipeline();

            return collect($leads->all())->map(function ($lead) use ($statuses, $contacts) {
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
        }

        private function getContacts($leads): Collection
        {
            $contacts_id = collect($leads->all())->map(function ($lead) {
                return $lead->getContacts()?->first()?->getId();
            })->filter()->values()->toArray();

            return collect($this->amo->getContactsById($contacts_id)->toArray());
        }
    }

