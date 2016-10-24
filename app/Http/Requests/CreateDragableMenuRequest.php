<?php


namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateDragableMenuRequest extends Request
{
    private $logger;
    public function __construct()
    {
        //    parent::__construct();


    }
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $request=Request::all();
        $segment=$this->segments();
        $parentId=isset($request['parent_id'])?$request['parent_id']:'0';
        switch ($this->method()) {
            case 'POST': {
                return [
                    'name' => 'required',
                    'parent_id' => 'integer'


                ];
            }
            case 'PUT':
                return [

                    'parent_id' => 'integer',
                    'name' => 'required',

                ];
                break;

        }


    }

    public function messages()
    {

        return [
            'name.required' => 'name.required',
            'name.unique' => 'name.unique',
            'parent_id.required' => 'parent_id.required'


        ];
    }

    public function response(array $errors)
    {
        foreach ($errors as $oneErr=>$err)
        {
         /*   if ($oneErr=='addresses.0.email') $oneErr='email'; //i cannot handle this: addresses.0.email.email with angular2 so I changed to
            $onlyErr[$oneErr]=$this->logger->dictionary_local[$err[0]];*/

        }
        //$this->logger->warning('response','log.requestMsg',['message' => $onlyErr]);
//        return response()->json(['errors'=>$onlyErr, 'http_status'=>422]);
    return response()->json([ 'http_status'=>$errors]);

    }
}
