<?php

namespace App\Ninja\Datatables;

use Auth;
use URL;
use Utils;

class TicketCategoryDatatable extends EntityDatatable
{
    public $entityType = ENTITY_TICKET_CATEGORY;
    public $sortCol = 1;

    public function columns()
    {
        return [
        ];
    }

    public function actions()
    {
        return [];
    }
}
