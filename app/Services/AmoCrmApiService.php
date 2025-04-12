<?php
    // app/Services/AmoCrmApiService.php
    namespace App\Services;

    use App\DTO\LeadFilterDto;

    class AmoCrmApiService
    {
        public function __construct(
            protected AmoCrmAuthService $auth
        ) {}

        public function getLeads(LeadFilterDto $filterDto)
        {
            $filter = new \AmoCRM\Filters\LeadsFilter();
            $filter->setPage($filterDto->page);
            $filter->setLimit($filterDto->limit);

            if($filterDto->status && $filterDto->pipelines){
                $filter->setStatuses([
                    ['status_id' => $filterDto->status, 'pipeline_id' => $filterDto->pipelines],
                ]);
            }

            $sortable = ['updated_at', 'created_at'];
            if (in_array($filterDto->sortBy, $sortable)) {
                $filter->setOrder($filterDto->sortBy, strtolower($filterDto->sortDirection) === 'asc' ? 'asc' : 'desc');
            }

            return $this->auth->getClient()->leads()->get($filter, ['contacts']);
        }

        public function getContactsById(array $id)
        {
            $filter = new \AmoCRM\Filters\ContactsFilter();
            $filter->setIds($id);

            return $this->auth->getClient()->contacts()->get($filter);
        }

        public function getStatusesByPipeline(): array
        {
            $pipelines = $this->auth->getClient()->pipelines()->get();
            $map = [];

            foreach ($pipelines as $pipeline) {
                foreach ($pipeline->getStatuses() as $status) {
                    $map[$pipeline->getId()][$status->getId()] = $status->getName();
                }
            }

            return $map;
        }
    }
