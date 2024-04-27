<?php

namespace App\MyHelpers;


use Doctrine\Common\Collections\ArrayCollection;

class PaginationHelper
{
    private $products;
    private int $current_page;
    private int $previous_page;
    private int $nbr_pages;
    private $aiResults;


    /**
     * @param  $products
     * @param int $current_page
     * @param int $previous_page
     * @param int $nbr_pages
     */
    public function __construct($products, int $current_page, int $previous_page, int $nbr_pages, $aiResults = [])
    {
        $this->products = $products;
        $this->current_page = $current_page;
        $this->previous_page = $previous_page;
        $this->nbr_pages = $nbr_pages;
        $this->aiResults = $aiResults;
    }


    public function getAiResult(int $number)
    {
        $array = array_slice($this->aiResults, ($this->current_page - 1) * $number, $number);
        if ($array)
            return $array;
        else
            return null;
//        return $this->aiResults;
    }

    public function setAiResults(mixed $aiResults): void
    {
        $this->aiResults = $aiResults;
    }


    public function getProducts()
    {
        return new ArrayCollection($this->products);
    }

    public function getNProducts(int $number)
    {
        if ($this->products instanceof ArrayCollection) {
            $this->toArray();
        }
        return array_slice($this->products, ($this->current_page - 1) * $number, $number);
    }


    public function setProducts($products): void
    {
        $this->products = $products;
    }

    public function getCurrentPage(): int
    {
        return $this->current_page;
    }

    public function setCurrentPage(int $current_page): void
    {
        $this->current_page = $current_page;
    }

    public function getPreviousPage(): int
    {
        return $this->previous_page;
    }

    public function setPreviousPage(int $previous_page): void
    {
        $this->previous_page = $previous_page;
    }

    public function getNbrPages(): int
    {
        return $this->nbr_pages;
    }

    public function setNbrPages(int $nbr_pages): void
    {
        $this->nbr_pages = $nbr_pages;
    }

    public function toArray()
    {
        $result = [];
        foreach ($this->products as $element) {
            $result[] = $element; // Assuming related entities have a similar method
        }
        $this->products = $result;
    }

}