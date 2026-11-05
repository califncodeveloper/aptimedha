<x-layouts.admin>
    <x-slot:title>Edit MCQ #{{ $question->question_number }} - AptiMedha Admin</x-slot:title>
    <x-slot:header>Edit Question #{{ $question->question_number }}</x-slot:header>

    <div class="space-y-6 max-w-4xl" x-data="{
        selectedCategory: '{{ old('category_id', $question->category_id) }}',
        selectedSubject: '{{ old('subject_id', $question->subject_id) }}',
        selectedTopic: '{{ old('topic_id', $question->topic_id) }}',
        correctOption: '{{ old('correct_option', $question->correct_option) }}',
        imagePreview: null,
        removeImage: false,
        optAPreview: null,
        removeOptA: false,
        optBPreview: null,
        removeOptB: false,
        optCPreview: null,
        removeOptC: false,
        optDPreview: null,
        removeOptD: false,
        expPreview: null,
        removeExp: false,
        allSubjects: {{ $subjects->toJson() }},
        allTopics: {{ $topics->toJson() }},
        get filteredSubjects() {
            if (!this.selectedCategory) return [];
            return this.allSubjects.filter(s => s.category_id == this.selectedCategory);
        },
        get filteredTopics() {
            if (!this.selectedCategory) return [];
            return this.allTopics.filter(t => {
                const matchCat = t.category_id == this.selectedCategory;
                const matchSubj = !this.selectedSubject || t.subject_id == this.selectedSubject;
                return matchCat && matchSubj;
            });
        },
        handleCategoryChange() {
            this.selectedSubject = '';
            this.selectedTopic = '';
        },
        handleSubjectChange() {
            this.selectedTopic = '';
        },
        handleImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.imagePreview = URL.createObjectURL(file);
                this.removeImage = false;
            } else {
                this.imagePreview = null;
            }
        },
        handleOptionImage(event, opt) {
            const file = event.target.files[0];
            this['opt' + opt + 'Preview'] = file ? URL.createObjectURL(file) : null;
            this['removeOpt' + opt] = false;
        },
        handleExpImage(event) {
            const file = event.target.files[0];
            this.expPreview = file ? URL.createObjectURL(file) : null;
            this.removeExp = false;
        }
    }">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.mcqs.index') }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to All MCQs
            </a>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $question->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                    {{ $question->is_active ? 'Published' : 'Draft' }}
                </span>
                <a href="{{ route('mcq.show', $question->id) }}" target="_blank" class="text-xs font-bold text-[#087F82] hover:underline inline-flex items-center">
                    <i data-lucide="external-link" class="w-3.5 h-3.5 mr-1"></i> Public View
                </a>
            </div>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                <p class="font-bold mb-1">Please correct the following errors:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8">
            <form action="{{ route('admin.mcqs.update', $question->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Taxonomy Hierarchy: Category, Subject, Topic --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pb-6 border-b border-gray-100">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Category <span class="text-rose-500">*</span>
                        </label>
                        <select name="category_id"
                                x-model="selectedCategory"
                                @change="handleCategoryChange()"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border @error('category_id') border-rose-500 @else border-gray-300 @enderror text-xs sm:text-sm focus:border-[#087F82] focus:outline-none">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-[11px] text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Subject / Branch <span class="text-gray-400 font-normal lowercase">(optional)</span>
                        </label>
                        <select name="subject_id"
                                x-model="selectedSubject"
                                @change="handleSubjectChange()"
                                class="w-full px-3.5 py-2.5 rounded-xl border @error('subject_id') border-rose-500 @else border-gray-300 @enderror text-xs sm:text-sm focus:border-[#087F82] focus:outline-none">
                            <option value="">-- Direct Category / None --</option>
                            <template x-for="subj in filteredSubjects" :key="subj.id">
                                <option :value="subj.id" x-text="subj.name" :selected="subj.id == selectedSubject"></option>
                            </template>
                        </select>
                        @error('subject_id')
                            <p class="text-[11px] text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Leaf Topic <span class="text-rose-500">*</span>
                        </label>
                        <select name="topic_id"
                                x-model="selectedTopic"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border @error('topic_id') border-rose-500 @else border-gray-300 @enderror text-xs sm:text-sm focus:border-[#087F82] focus:outline-none">
                            <option value="">-- Select Topic --</option>
                            <template x-for="top in filteredTopics" :key="top.id">
                                <option :value="top.id" x-text="top.name" :selected="top.id == selectedTopic"></option>
                            </template>
                        </select>
                        @error('topic_id')
                            <p class="text-[11px] text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Question Number, Section & Status --}}
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Question Number in Topic <span class="text-rose-500">*</span>
                        </label>
                        <input type="number"
                               name="question_number"
                               value="{{ old('question_number', $question->question_number) }}"
                               required
                               min="1"
                               class="w-full px-3.5 py-2.5 rounded-xl border @error('question_number') border-rose-500 @else border-gray-300 @enderror text-xs sm:text-sm focus:border-[#087F82] focus:outline-none">
                        @error('question_number')
                            <p class="text-[11px] text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Section Allocation
                        </label>
                        <select name="section_number" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm focus:border-[#087F82] focus:outline-none">
                            <option value="1" @selected(old('section_number', $question->section_number) == 1)>Section 1 (Q1 - Q100)</option>
                            <option value="2" @selected(old('section_number', $question->section_number) == 2)>Section 2 (Q101 - Q200)</option>
                            <option value="3" @selected(old('section_number', $question->section_number) == 3)>Section 3 (Q201+)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Difficulty Level
                        </label>
                        <select name="difficulty" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm focus:border-[#087F82] focus:outline-none">
                            <option value="easy" @selected(old('difficulty', $question->difficulty) === 'easy')>Easy</option>
                            <option value="medium" @selected(old('difficulty', $question->difficulty) === 'medium')>Medium</option>
                            <option value="hard" @selected(old('difficulty', $question->difficulty) === 'hard')>Hard</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Publication Status
                        </label>
                        <select name="is_active" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm focus:border-[#087F82] focus:outline-none">
                            <option value="1" @selected(old('is_active', $question->is_active ? '1' : '0') == '1')>Published (Active)</option>
                            <option value="0" @selected(old('is_active', $question->is_active ? '1' : '0') == '0')>Draft (Hidden)</option>
                        </select>
                    </div>
                </div>

                {{-- Question Statement --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Question Statement <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="question_text"
                              rows="3"
                              required
                              class="w-full p-3.5 rounded-xl border @error('question_text') border-rose-500 @else border-gray-300 @enderror text-sm focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none">{{ old('question_text', $question->question_text) }}</textarea>
                    @error('question_text')
                        <p class="text-[11px] text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Question Diagram / Image Handling & Alt Text --}}
                <div class="p-4 rounded-xl border border-gray-200/80 bg-gray-50/60 space-y-3">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Question Diagram / Figure Image
                    </label>

                    @if($question->question_image)
                        <div class="flex items-start gap-4 p-3 bg-white rounded-xl border border-gray-200" x-show="!removeImage">
                            <div class="w-20 h-20 rounded-lg border border-gray-200 overflow-hidden bg-gray-50 flex items-center justify-center p-1">
                                <img src="{{ $question->image_url }}" alt="Current Diagram" class="w-full h-full object-contain">
                            </div>
                            <div class="flex-1 text-xs space-y-1">
                                <p class="font-bold text-[#153B70]">Current Image File:</p>
                                <p class="font-mono text-gray-500 break-all">{{ $question->question_image }}</p>
                                <label class="inline-flex items-center gap-1.5 text-rose-600 font-bold cursor-pointer mt-1">
                                    <input type="checkbox" name="remove_question_image" value="1" x-model="removeImage" class="rounded text-rose-600 focus:ring-rose-500">
                                    <span>Remove current image</span>
                                </label>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <label class="block text-[11px] text-gray-500 mb-1">
                                {{ $question->question_image ? 'Upload new image to replace current:' : 'Upload optional image:' }}
                            </label>
                            <input type="file"
                                   name="question_image"
                                   @change="handleImage"
                                   accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                   class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-[#153B70] hover:file:bg-gray-200">
                        </div>
                        <template x-if="imagePreview">
                            <div class="w-20 h-20 rounded-xl border border-gray-200 overflow-hidden bg-gray-50 flex items-center justify-center p-1">
                                <img :src="imagePreview" alt="Upload Preview" class="w-full h-full object-contain">
                            </div>
                        </template>
                    </div>
                    @error('question_image')
                        <p class="text-[11px] text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">
                            Image ALT Text (Accessibility & Diagram Description)
                        </label>
                        <input type="text"
                               name="question_image_alt"
                               value="{{ old('question_image_alt', $question->question_image_alt) }}"
                               placeholder="e.g. Geometry figure showing right triangle with hypotenuse 10cm"
                               class="w-full text-xs px-3 py-2 rounded-lg border border-gray-200 focus:border-[#087F82] focus:outline-none">
                        @error('question_image_alt')
                            <p class="text-[11px] text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Four Options with Radio selector for Correct Option & Diagrams --}}
                <div class="space-y-3 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Options & Answer Key <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-xs text-[#087F82] font-semibold">Select the green radio button for the correct answer</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Option A --}}
                        <div class="p-3.5 rounded-xl border transition-colors space-y-2.5"
                             :class="correctOption === 'A' ? 'border-emerald-500 bg-emerald-50/50' : 'border-gray-200'">
                            <div class="flex items-center gap-2">
                                <input type="radio"
                                       name="correct_option"
                                       value="A"
                                       x-model="correctOption"
                                       class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                                <span class="font-bold text-xs" :class="correctOption === 'A' ? 'text-emerald-800' : 'text-gray-500'">Option A</span>
                                <span x-show="correctOption === 'A'" class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded">Correct Answer</span>
                            </div>
                            <input type="text"
                                   name="option_a"
                                   value="{{ old('option_a', $question->option_a) }}"
                                   required
                                   class="w-full text-xs sm:text-sm px-3 py-2 rounded-lg border border-gray-200 focus:border-[#087F82] focus:outline-none">
                            @error('option_a')
                                <p class="text-[11px] text-rose-500 mt-0.5 font-semibold">{{ $message }}</p>
                            @enderror

                            {{-- Option A Diagram Management --}}
                            <div class="pt-2 border-t border-gray-100 text-xs">
                                @if($question->option_a_image)
                                    <div class="flex items-center gap-2 p-1.5 bg-white rounded border border-gray-200 mb-1.5" x-show="!removeOptA">
                                        <img src="{{ $question->option_a_image_url }}" alt="Option A" class="w-8 h-8 object-contain">
                                        <label class="text-[11px] text-rose-600 font-semibold cursor-pointer">
                                            <input type="checkbox" name="remove_option_a_image" value="1" x-model="removeOptA" class="rounded text-rose-600">
                                            <span>Remove</span>
                                        </label>
                                    </div>
                                @endif
                                <div class="flex items-center gap-2">
                                    <input type="file" name="option_a_image" @change="handleOptionImage($event, 'A')" accept="image/*" class="text-[11px] text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[11px] file:bg-gray-100 file:text-[#153B70]">
                                    <template x-if="optAPreview">
                                        <img :src="optAPreview" alt="Preview" class="w-8 h-8 object-contain rounded border border-gray-200">
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Option B --}}
                        <div class="p-3.5 rounded-xl border transition-colors space-y-2.5"
                             :class="correctOption === 'B' ? 'border-emerald-500 bg-emerald-50/50' : 'border-gray-200'">
                            <div class="flex items-center gap-2">
                                <input type="radio"
                                       name="correct_option"
                                       value="B"
                                       x-model="correctOption"
                                       class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                                <span class="font-bold text-xs" :class="correctOption === 'B' ? 'text-emerald-800' : 'text-gray-500'">Option B</span>
                                <span x-show="correctOption === 'B'" class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded">Correct Answer</span>
                            </div>
                            <input type="text"
                                   name="option_b"
                                   value="{{ old('option_b', $question->option_b) }}"
                                   required
                                   class="w-full text-xs sm:text-sm px-3 py-2 rounded-lg border border-gray-200 focus:border-[#087F82] focus:outline-none">
                            @error('option_b')
                                <p class="text-[11px] text-rose-500 mt-0.5 font-semibold">{{ $message }}</p>
                            @enderror

                            {{-- Option B Diagram Management --}}
                            <div class="pt-2 border-t border-gray-100 text-xs">
                                @if($question->option_b_image)
                                    <div class="flex items-center gap-2 p-1.5 bg-white rounded border border-gray-200 mb-1.5" x-show="!removeOptB">
                                        <img src="{{ $question->option_b_image_url }}" alt="Option B" class="w-8 h-8 object-contain">
                                        <label class="text-[11px] text-rose-600 font-semibold cursor-pointer">
                                            <input type="checkbox" name="remove_option_b_image" value="1" x-model="removeOptB" class="rounded text-rose-600">
                                            <span>Remove</span>
                                        </label>
                                    </div>
                                @endif
                                <div class="flex items-center gap-2">
                                    <input type="file" name="option_b_image" @change="handleOptionImage($event, 'B')" accept="image/*" class="text-[11px] text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[11px] file:bg-gray-100 file:text-[#153B70]">
                                    <template x-if="optBPreview">
                                        <img :src="optBPreview" alt="Preview" class="w-8 h-8 object-contain rounded border border-gray-200">
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Option C --}}
                        <div class="p-3.5 rounded-xl border transition-colors space-y-2.5"
                             :class="correctOption === 'C' ? 'border-emerald-500 bg-emerald-50/50' : 'border-gray-200'">
                            <div class="flex items-center gap-2">
                                <input type="radio"
                                       name="correct_option"
                                       value="C"
                                       x-model="correctOption"
                                       class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                                <span class="font-bold text-xs" :class="correctOption === 'C' ? 'text-emerald-800' : 'text-gray-500'">Option C</span>
                                <span x-show="correctOption === 'C'" class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded">Correct Answer</span>
                            </div>
                            <input type="text"
                                   name="option_c"
                                   value="{{ old('option_c', $question->option_c) }}"
                                   required
                                   class="w-full text-xs sm:text-sm px-3 py-2 rounded-lg border border-gray-200 focus:border-[#087F82] focus:outline-none">
                            @error('option_c')
                                <p class="text-[11px] text-rose-500 mt-0.5 font-semibold">{{ $message }}</p>
                            @enderror

                            {{-- Option C Diagram Management --}}
                            <div class="pt-2 border-t border-gray-100 text-xs">
                                @if($question->option_c_image)
                                    <div class="flex items-center gap-2 p-1.5 bg-white rounded border border-gray-200 mb-1.5" x-show="!removeOptC">
                                        <img src="{{ $question->option_c_image_url }}" alt="Option C" class="w-8 h-8 object-contain">
                                        <label class="text-[11px] text-rose-600 font-semibold cursor-pointer">
                                            <input type="checkbox" name="remove_option_c_image" value="1" x-model="removeOptC" class="rounded text-rose-600">
                                            <span>Remove</span>
                                        </label>
                                    </div>
                                @endif
                                <div class="flex items-center gap-2">
                                    <input type="file" name="option_c_image" @change="handleOptionImage($event, 'C')" accept="image/*" class="text-[11px] text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[11px] file:bg-gray-100 file:text-[#153B70]">
                                    <template x-if="optCPreview">
                                        <img :src="optCPreview" alt="Preview" class="w-8 h-8 object-contain rounded border border-gray-200">
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Option D --}}
                        <div class="p-3.5 rounded-xl border transition-colors space-y-2.5"
                             :class="correctOption === 'D' ? 'border-emerald-500 bg-emerald-50/50' : 'border-gray-200'">
                            <div class="flex items-center gap-2">
                                <input type="radio"
                                       name="correct_option"
                                       value="D"
                                       x-model="correctOption"
                                       class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                                <span class="font-bold text-xs" :class="correctOption === 'D' ? 'text-emerald-800' : 'text-gray-500'">Option D</span>
                                <span x-show="correctOption === 'D'" class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded">Correct Answer</span>
                            </div>
                            <input type="text"
                                   name="option_d"
                                   value="{{ old('option_d', $question->option_d) }}"
                                   required
                                   class="w-full text-xs sm:text-sm px-3 py-2 rounded-lg border border-gray-200 focus:border-[#087F82] focus:outline-none">
                            @error('option_d')
                                <p class="text-[11px] text-rose-500 mt-0.5 font-semibold">{{ $message }}</p>
                            @enderror

                            {{-- Option D Diagram Management --}}
                            <div class="pt-2 border-t border-gray-100 text-xs">
                                @if($question->option_d_image)
                                    <div class="flex items-center gap-2 p-1.5 bg-white rounded border border-gray-200 mb-1.5" x-show="!removeOptD">
                                        <img src="{{ $question->option_d_image_url }}" alt="Option D" class="w-8 h-8 object-contain">
                                        <label class="text-[11px] text-rose-600 font-semibold cursor-pointer">
                                            <input type="checkbox" name="remove_option_d_image" value="1" x-model="removeOptD" class="rounded text-rose-600">
                                            <span>Remove</span>
                                        </label>
                                    </div>
                                @endif
                                <div class="flex items-center gap-2">
                                    <input type="file" name="option_d_image" @change="handleOptionImage($event, 'D')" accept="image/*" class="text-[11px] text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[11px] file:bg-gray-100 file:text-[#153B70]">
                                    <template x-if="optDPreview">
                                        <img :src="optDPreview" alt="Preview" class="w-8 h-8 object-contain rounded border border-gray-200">
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('correct_option')
                        <p class="text-[11px] text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Step-by-Step Explanation & Explanation Diagram --}}
                <div class="space-y-3 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Step-by-Step Explanation / Mathematical Solution
                        </label>
                        <textarea name="explanation"
                                  rows="4"
                                  placeholder="Provide step-by-step mathematical working or conceptual reason..."
                                  class="w-full p-3.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none">{{ old('explanation', $question->explanation) }}</textarea>
                        @error('explanation')
                            <p class="text-[11px] text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Explanation Diagram Management --}}
                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 text-xs space-y-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Explanation Diagram / Solution Figure
                        </label>
                        @if($question->explanation_image)
                            <div class="flex items-center gap-3 p-2 bg-white rounded-lg border border-gray-200" x-show="!removeExp">
                                <img src="{{ $question->explanation_image_url }}" alt="Explanation Diagram" class="w-12 h-12 object-contain">
                                <label class="text-xs text-rose-600 font-semibold cursor-pointer">
                                    <input type="checkbox" name="remove_explanation_image" value="1" x-model="removeExp" class="rounded text-rose-600">
                                    <span>Remove explanation diagram</span>
                                </label>
                            </div>
                        @endif
                        <div class="flex items-center gap-3">
                            <input type="file" name="explanation_image" @change="handleExpImage" accept="image/*" class="text-xs text-gray-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-[#153B70]">
                            <template x-if="expPreview">
                                <img :src="expPreview" alt="Preview" class="w-12 h-12 object-contain rounded border border-gray-200">
                            </template>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3">
                    <a href="{{ route('admin.mcqs.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-semibold text-gray-600 hover:bg-gray-50 text-center">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold shadow-xs transition-colors text-center">
                        Update Question
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
