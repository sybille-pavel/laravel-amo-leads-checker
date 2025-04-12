<?php
    namespace App\DTO;

    use AmoCRM\Models\LeadModel;

    class LeadDto
    {
        public static function fromEntity(LeadModel $lead, array $statuses, ?string $contactName): array
        {
            $pipelineId = $lead->getPipelineId();
            $statusId = $lead->getStatusId();
            $statusName = $statuses[$pipelineId][$statusId] ?? 'Неизвестный статус';

            return [
                'id' => $lead->getId(),
                'name' => $lead->getName(),
                'status' => $statusName,
                'contact' => $contactName,
                'updated_at' => optional($lead->getUpdatedAt())
                    ? \Carbon\Carbon::createFromTimestamp($lead->getUpdatedAt())->format('Y-m-d H:i:s')
                    : null,
                'created_at' => optional($lead->getCreatedAt())
                    ? \Carbon\Carbon::createFromTimestamp($lead->getCreatedAt())->format('Y-m-d H:i:s')
                    : null,
            ];
        }
    }
