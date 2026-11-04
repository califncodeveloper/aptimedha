<?php

namespace App\Http\Requests\Admin;

use App\Models\Subject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class TopicRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $topicId = $this->route('topic') instanceof \App\Models\Topic
            ? $this->route('topic')->id
            : $this->route('topic');

        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:180', Rule::unique('topics', 'slug')->ignore($topicId)],
            'description' => ['nullable', 'string', 'max:2000'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Configure additional validation checks to ensure parent hierarchy consistency.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $categoryId = (int) $this->input('category_id');
            $subjectId = $this->input('subject_id');

            if ($subjectId && $categoryId) {
                $subject = Subject::find($subjectId);
                if ($subject && (int) $subject->category_id !== $categoryId) {
                    $v->errors()->add('subject_id', 'The selected subject does not belong to the selected category.');
                }
            }
        });
    }
}

