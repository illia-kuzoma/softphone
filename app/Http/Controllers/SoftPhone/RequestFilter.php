<?php

namespace App\Http\Controllers\SoftPhone;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RequestFilter extends Controller
{
    private function checkFunctionality( $functionality)
    {
        if(!in_array($functionality, [\App\Models\RequestFilter::FUNCTIONALITY_MISSED, \App\Models\RequestFilter::FUNCTIONALITY_STATUSES]))
        {
            throw new \Exception('Functionality parameter error');
        }
    }
    private function checkName($name)
    {
        if(strlen($name)<1){
            throw new \Exception('Name parameter error');
        }
    }

    //private function parseFields

    public function list(Request $request): string
    {
        $functionality = $request->get('page');
        $this->checkFunctionality($functionality);

        $a_filter =  \App\Models\RequestFilter::query()->select(['id',
            'text_name as name',
            'day',
            'text_department_id as department_id',
            'text_team_id as team_id',
            'text_period as period',
            'text_status_type as status_type',
            'text_user_id as user_id',
            's_chart_status as chart_status',
            's_chart_phone_status as chart_phone_status'
        ])->
        where('text_functionality','=', $functionality)->get()->toArray();

        $a_comma_separated = ['department_id', 'team_id', 'user_id', 'status_type', 'chart_status', 'chart_phone_status'];
        foreach($a_filter as &$item)
        {

            foreach($item as $field=>&$value)
            {

                if($value && in_array($field, $a_comma_separated))
                {
                    $value = explode(',', $value);
                }
            }
        }
        unset($item);
        return json_encode($a_filter);
    }

    /**
     * слать заголовок Content-Type multipart/form-data
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function save(Request $request)
    {
        $name = $request->post('name');
        $functionality = $request->post('page');
        $this->checkName($name);
        $this->checkFunctionality($functionality);
        $o_filter = \App\Models\RequestFilter::query()->where('text_name', '=', $name)->
        where('text_functionality', '=', $functionality)->get();
        if($o_filter->isEmpty())
        {
            $day = $request->post('day');
            $text_period = $request->post('period');
            $text_department_id = $request->post('department_id');
            $text_team_id = $request->post('team_id');
            $text_user_id = $request->post('user_id');
            $text_status_types = $request->post('status_type');
            $this->_checkString('status_type', $text_status_types);
            $this->_checkStatusType($text_status_types);
            $s_chart_status = $request->post('chart_status');
            $this->_checkString('chart_status', $s_chart_status);
            $this->_checkChartStatus($s_chart_status);
            $s_chart_phone_status = $request->post('chart_phone_status');
            $this->_checkString('chart_phone_status', $s_chart_phone_status);
            $this->_checkChartPhoneStatus($s_chart_phone_status);

            $o_filter = new \App\Models\RequestFilter;
            $o_filter->text_name = $name;
            $o_filter->text_functionality = $functionality;
            $o_filter->day = $day;
            $o_filter->text_period = $text_period;
            $o_filter->text_department_id = $text_department_id;
            $o_filter->text_team_id = $text_team_id;
            $o_filter->text_user_id = $text_user_id;
            $o_filter->text_status_type = $text_status_types;
            if($s_chart_status)
                $o_filter->s_chart_status = $s_chart_status;
            if($s_chart_phone_status)
                $o_filter->s_chart_phone_status = $s_chart_phone_status;

            $o_filter->save();
        }
        return response('', 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function delete(Request $request)
    {
        $id = $request->get('id');
        $model = \App\Models\RequestFilter::query()->find($id);
        if($model){
            $model->delete();
        }
        return response('', 200)
            ->header('Content-Type', 'text/plain');
    }

    public function get(Request $request)
    {
        $id = $request->get('id');
        $model = \App\Models\RequestFilter::query()->find($id);
        if(!$model){
            return response('', 200)
                ->header('Content-Type', 'text/plain');
        }
        $url = '/';
        $type = '';
        switch($model->text_functionality)
        {
            case \App\Models\RequestFilter::FUNCTIONALITY_MISSED:{
                $url.= 'report/missed/call/';
            }break;
            case \App\Models\RequestFilter::FUNCTIONALITY_STATUSES:{
                $url.= 'report/agent/status/page/';
                $type = '/'.$model->text_status_type;
            }break;
        }
        $url.=$model->day.'/'.$model->text_period.'/'.(empty($model->text_department_id)?'-':$model->text_department_id).'/'.($model->text_team_id?$model->text_team_id:'-'). ($model->text_user_id?'/'.$model->text_user_id:$type?'-':'').$type;
        return \Redirect::to($url);
        //print_r($model->attributesToArray());exit;
    }

    private function _checkStatusType(?string $text_status_types)
    {
        if($text_status_types)
        {
            $a_status_type = array_filter(explode(',', $text_status_types));
            foreach($a_status_type as $text_status_type)
            {
                if(!in_array(trim($text_status_type), [
                    'chat_status', 'mail_status', 'phone_status', 'status'
                ]))
                {
                    throw new \Exception('Status type parameter missmatch.');
                }
            }
        }
    }

    private function _checkChartStatus(?string $s_chart_status)
    {
        if($s_chart_status)
        {
            $a_chart_status = array_filter(explode(',', $s_chart_status));
            foreach($a_chart_status as $text_status)
            {
                if(!in_array(trim($text_status), [
                    'OFFLINE', 'ONLINE'
                ]))
                {
                    throw new \Exception('Status parameter missmatch.');
                }
            }
        }
    }

    private function _checkChartPhoneStatus(?string $s_chart_phone_status)
    {
        if($s_chart_phone_status)
        {
            $a_chart_phone_status = array_filter(explode(',', $s_chart_phone_status));
            foreach($a_chart_phone_status as $text_status)
            {
                if(!in_array(trim($text_status), [
                    'OFFLINE', 'ONLINE', 'BUSY', 'ONCALL'
                ]))
                {
                    throw new \Exception('Status phone parameter missmatch.');
                }
            }
        }
    }

    /**
     * Checks that value exists and it is a string type.
     * @param string $text_name
     * @param string $text_val
     * @throws \Exception
     */
    private function _checkString(string $text_name, ?string $text_val)
    {
        if(!is_null($text_val) && !is_string($text_val))
        {
            throw new \Exception(sprintf('Parameter %s not a string.', $text_name));
        }
    }
}
