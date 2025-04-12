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
    }
