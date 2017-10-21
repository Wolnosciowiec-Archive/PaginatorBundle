<?php declare(strict_types = 1);

namespace PaginatorBundle\Repository;

class PaginatedResults implements \JsonSerializable
{
    /**
     * @var array $results
     */
    protected $results = [];

    /**
     * @var int $maxPages
     */
    protected $maxPages = 0;

    /**
     * @var int $currentPage
     */
    protected $currentPage = 1;

    public function __construct(array $results, $maxPages, $page)
    {
        if ((int)$maxPages === 0) {
            $maxPages = 1;
        }
            
        $this->results     = $results;
        $this->maxPages    = $maxPages;
        $this->currentPage = $page;
    }

    /**
     * Get count of pages
     * ==================
     *
     * @param int $resultsCount
     * @param int $perPage
     *
     * @return int
     */
    public static function getPagesCount($resultsCount, $perPage)
    {
        return round($resultsCount / $perPage, 0, PHP_ROUND_HALF_UP);
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @return int
     */
    public function countResults()
    {
        return count($this->results);
    }

    /**
     * @return bool
     */
    public function hasAnyResults()
    {
        return $this->countResults() > 0;
    }

    /**
     * @return int
     */
    public function getMaxPages()
    {
        return $this->maxPages;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @return bool
     */
    public function isNextPageAvailable()
    {
        return ($this->currentPage + 1) <= $this->getMaxPages();
    }

    /**
     * @return bool
     */
    public function isPreviousPageAvailable()
    {
        return $this->currentPage > 1;
    }

    public function jsonSerialize()
    {
        return [
            'results' =>                 $this->getResults(),
            'maxPages' =>                $this->getMaxPages(),
            'currentPage' =>             $this->getCurrentPage(),
            'isPreviousPageAvailable' => $this->isPreviousPageAvailable(),
            'isNextPageAvailable' =>     $this->isNextPageAvailable(),
            'hasAnyResults' =>           $this->hasAnyResults(),
            'countResults' =>            $this->countResults(),
        ];
    }
}
