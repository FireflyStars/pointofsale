<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TableFiltersController extends Controller
{

    
    public function sorts(Request $request, $data, $sort = 'id') 
    {
        if($request->has('sortby') && count($request->sortby)) 
        {
            $soryby = $request->sortby[0];
            $data = $data->orderBy($soryby['id'], $soryby['orderby']);
        }
        else 
        {
            $data = $data->orderBy($sort, 'DESC');
        }
        return $data;
    }


    public function filters(Request $request, $data) 
    {

        if($request->has('column_filters') && count($request->column_filters)) 
        {

            foreach($request->column_filters as $filter) 
            {

                if($filter['type'] == 'date')
                {

                    if(isset($filter['having']) && $filter['having'] == true)
                    {
                        if($filter['word']['from'] != '')
                        {
                            $data = $data->having($filter['id'], '>=', $filter['word']['from']);
                        }
                        if($filter['word']['to']!='')
                        {
                            $data = $data->having($filter['id'], '<', $filter['word']['to']);
                        }
                    }

                    else
                    {
                        if($filter['word']['from'] != '')
                        {
                            $data = $data->whereDate((isset($filter['table'])?$filter['table'] . '.' : '') . $filter['id'], '>=', $filter['word']['from']);
                        }
                        if($filter['word']['to']!='')
                        {
                            $data = $data->whereDate((isset($filter['table'])?$filter['table'] . '.' : '') . $filter['id'], '<', $filter['word']['to']);
                        }
                    }

                }

                elseif(isset($filter['filter_options']) && count($filter['word']) > 0)
                {
           
                    if(isset($filter['having']) && $filter['having'] == true)
                    {
                       
                        $data = $data->havingRaw($filter['id'] . " IN ('" . implode("','", $filter['word']) . "')");
                    }
                    else
                    {
                        $data = $data->whereIn($filter['id'], $filter['word']);
                    }
                
                }

                else
                {

                    if(isset($filter['having']) && $filter['having'] == true)
                    {
                        $data = $data->having($filter['id'], 'LIKE', '%' . $filter['word'] . '%');
                    }
                    else
                    {
                        $data = $data->where((isset($filter['table']) ? $filter['table'] . '.' : '') . $filter['id'], 'LIKE', '%' . $filter['word'] . '%');
                    }

                }

            }
        }

        return $data;

    }


}
