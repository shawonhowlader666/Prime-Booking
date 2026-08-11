<?php

namespace App\Services\Search;

use App\Repositories\PropertyRepository;

/**
 * SearchService — Thin facade over PropertyRepository.
 * All caching and query logic lives in the Repository.
 * This service is kept for backward compatibility with SearchController.
 */
class SearchService
{
    public function __construct(
        protected PropertyRepository $repository
    ) {}

    public function searchHotels(array $params = []): array
    {
        return $this->repository->search($params);
    }

    public function getAvailableCities(): array
    {
        return $this->repository->getAvailableCities();
    }

    public function getPriceRange(): array
    {
        return $this->repository->getPriceRange();
    }

    public function getFilterCounts(?string $destination = ''): array
    {
        return $this->repository->getFilterCounts($destination ?? '');
    }
}
