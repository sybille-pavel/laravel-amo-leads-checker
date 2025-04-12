<?php
    namespace App\DTO;

    class LeadFilterDto
    {
        public function __construct(
            public int $page = 1,
            public int $limit = 25,
            public string $sortBy = 'updated_at',
            public string $sortDirection = 'desc',
        ) {}

        public static function fromRequest(\Illuminate\Http\Request $request): LeadFilterDto
        {
            return new self(
                page: (int) $request->get('page', 1),
                limit: (int) $request->get('limit', 25),
                sortBy: $request->get('sortBy', 'updated_at'),
                sortDirection: $request->get('sortDirection', 'desc'),
            );
        }
    }
