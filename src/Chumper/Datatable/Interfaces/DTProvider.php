<?php

namespace Chumper\Datatable\Interfaces;

interface DatatableProvider
{
    public function process(ColumnConfiguration $columnConfiguration, PresentationConfiguration $presentationConfiguration) : DTData;
}