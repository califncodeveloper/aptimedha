<?php

namespace App\Http\Requests\Admin;

use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class QuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Prepare data prior to validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->input('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? ($this->input('is_active') == '1' || $this->input('is_active') === 'published'),
            ]);
        }

        $removals = [
            'remove_image',
            'remove_question_image',
            'remove_option_a_image',
            'remove_option_b_image',
            'remove_option_c_image',
            'remove_option_d_image',
            'remove_explanation_image',
        ];

        foreach ($removals as $removalKey) {
            if ($this->has($removalKey)) {
                $this->merge([
                    $removalKey => filter_var($this->input($removalKey), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? ($this->input($removalKey) == '1'),
                ]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $questionId = $this->route('mcq') instanceof \App\Models\Question
            ? $this->route('mcq')->id
            : $this->route('mcq');

        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'topic_id' => ['required', 'integer', 'exists:topics,id'],
            'question_number' => [
                'nullable',
                'integer',
                'min:1',
                Rule::unique('questions', 'question_number')
                    ->where('topic_id', $this->input('topic_id'))
                    ->ignore($questionId),
            ],
            'section_number' => ['nullable', 'integer', 'min:1'],
            'question_text' => ['required', 'string'],
            'question_image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'question_image_alt' => ['nullable', 'string', 'max:255'],
            'remove_image' => ['nullable', 'boolean'],
            'remove_question_image' => ['nullable', 'boolean'],
            'option_a' => ['required', 'string'],
            'option_a_image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'remove_option_a_image' => ['nullable', 'boolean'],
            'option_b' => ['required', 'string'],
            'option_b_image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'remove_option_b_image' => ['nullable', 'boolean'],
            'option_c' => ['required', 'string'],
            'option_c_image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'remove_option_c_image' => ['nullable', 'boolean'],
            'option_d' => ['required', 'string'],
            'option_d_image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'remove_option_d_image' => ['nullable', 'boolean'],
            'correct_option' => ['required', 'string', 'in:A,B,C,D'],
            'explanation' => ['nullable', 'string'],
            'explanation_image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'remove_explanation_image' => ['nullable', 'boolean'],
            'difficulty' => ['nullable', 'string', 'in:easy,medium,hard'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Configure the validator for cross-field taxonomy consistency.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $categoryId = (int) $this->input('category_id');
            $subjectId = $this->input('subject_id') ? (int) $this->input('subject_id') : null;
            $topicId = (int) $this->input('topic_id');

            if ($categoryId && $subjectId) {
                $subject = Subject::find($subjectId);
                if ($subject && (int) $subject->category_id !== $categoryId) {
                    $v->errors()->add('subject_id', 'The selected subject does not belong to the selected category.');
                }
            }

            if ($categoryId && $topicId) {
                $topic = Topic::find($topicId);
                if ($topic && (int) $topic->category_id !== $categoryId) {
                    $v->errors()->add('topic_id', 'The selected topic does not belong to the selected category.');
                }

                if ($subjectId && $topic) {
                    if ((int) $topic->subject_id !== $subjectId) {
                        $v->errors()->add('topic_id', 'The selected topic does not belong to the selected subject.');
                    }
                }
            }
        });
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'A parent category is required.',
            'category_id.exists' => 'The selected category does not exist.',
            'topic_id.required' => 'A leaf topic is required.',
            'topic_id.exists' => 'The selected topic does not exist.',
            'question_text.required' => 'The question text cannot be empty.',
            'option_a.required' => 'Option A is required.',
            'option_b.required' => 'Option B is required.',
            'option_c.required' => 'Option C is required.',
            'option_d.required' => 'Option D is required.',
            'correct_option.required' => 'You must designate the correct answer option.',
            'correct_option.in' => 'The correct answer option must be one of A, B, C, or D.',
            'question_image.image' => 'The question file must be an image.',
            'question_image.mimes' => 'The question image must be a file of type: jpeg, png, jpg, gif, webp.',
            'question_image.max' => 'The question image size must not exceed 5MB.',
        ];
    }
}

