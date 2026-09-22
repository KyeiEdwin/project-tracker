<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreProjectRequest extends FormRequest { public function authorize(): bool { return true; } public function rules(): array { return ['projectType'=>['required','in:predictive,agile,hybrid'],'name'=>['required','string','max:255'],'description'=>['nullable','string'],'startDate'=>['required','date'],'endDate'=>['required','date','after_or_equal:startDate'],'budget'=>['nullable','numeric','min:0'],'priority'=>['required','in:low,medium,high,critical'],'status'=>['required','in:planning,in-progress,on-hold,completed'],'team'=>['nullable','string','max:255'],'client'=>['nullable','string','max:255']]; } }
