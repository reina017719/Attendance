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
            'requested_end_time' => 'required|date_format:H:i|after:required_start_time',
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
            'requested_end_time.after' => '出勤時間もしくは退勤時間が不適切です',
            'reason.required' => '申請理由を入力してください',
            'reason.max' => '255文字以内で入力してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $start = $this->input('requested_start_time');
            $end = $this->input('requested_end_time');

            if ($start && $end) {
                $startTime = \Carbon\Carbon::createFromFormat('H:i', $start);
                $endTime = \Carbon\Carbon::createFromFormat('H:i', $end);

                if ($startTime->gt($endTime)) {
                    $validator->errors()->add('requested_end_time', '退勤時間は出勤時間より後の時刻にしてください。');
                }
            }

            $breakStart1 = $this->input('requested_break1_start_time');
            $breakEnd1   = $this->input('requested_break1_end_time');
            $breakStart2 = $this->input('requested_break2_start_time');
            $breakEnd2   = $this->input('requested_break2_end_time');

            $hasError = false;

            foreach ([
                'requested_break1_start_time' => $breakStart1,
                'requested_break1_end_time' => $breakEnd1,
                'requested_break2_start_time' => $breakStart2,
                'requested_break2_end_time' => $breakEnd2,
            ] as $field => $time) {
                if ($time && $endTime && \Carbon\Carbon::createFromFormat('H:i', $time)->gt($endTime)) {
                    $validator->errors()->add($field, '退勤時間よりも後の時間は設定できません。');
                    $hasError = true;
                }
            }
        });
    }
}