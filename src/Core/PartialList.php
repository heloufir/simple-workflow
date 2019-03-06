<?php

namespace Heloufir\SimpleWorkflow\Core;

use Illuminate\Support\Collection;

class PartialList
{

    /**
     * The data collection
     *
     * @var Collection
     */
    private $data;

    /**
     * The total count of data in the database
     *
     * @var int
     */
    private $count;

    /**
     * The current page
     *
     * @var int
     */
    private $page;

    /**
     * The current page size
     *
     * @var int
     */
    private $size;

    /**
     * PartialList constructor.
     *
     * @param Collection $data
     *      The data gotten
     * @param int $count
     *      The total data count from database
     * @param int $page
     *      The current page index
     * @param int $size
     *      The current page size
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function __construct(Collection $data, int $count, int $page, int $size)
    {
        $this->data = $data;
        $this->count = $count;
        $this->page = $page;
        $this->size = $size;
    }

    /**
     * Return an array containing the partial list information
     *
     * @return array
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function toArray(): array
    {
        return [
            'data' => $this->data,
            'count' => $this->count,
            'page' => $this->page,
            'size' => $this->size
        ];
    }

}