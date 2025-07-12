<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CorrectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
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
        return [
            'attendance_id' => 'required|exists:attendances,id',
            'requested_start_time' => 'required|date_format:H:i',
            'requested_end_time' => 'required|date_format:H:i',
            'requested_break1_start_time' => 'nullable|date_format:H:i',
            'requested_break1_end_time' => 'nullable|date_format:H:i',
            'requested_break2_start_time' => 'nullable|date_format:H:i',
            'requested_break2_end_time' => 'nullable|date_format:H:i',
            'reason' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'requested_start_time.required' => '出勤時間を入力してください',
            'requested_end_time.required' => '退勤時間を入力してください',
            'reason.required' => '申請理由を入力してください',
            'reason.max' => '255文字以内で入力してください',
        ];
    }
}
