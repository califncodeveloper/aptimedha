<x-layouts.admin>
    <x-slot:title>Add New MCQ - AptiMedha Admin</x-slot:title>
    <x-slot:header>Create New Question</x-slot:header>

    <div class="space-y-6 max-w-4xl" x-data="{ 
        correctOption: 'C',
        imagePreview: null,
        handleImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.imagePreview = URL.createObjectURL(file);
            }
        }
    }">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.mcqs.index') }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to All MCQs
            </a>
            <span class="text-xs text-gray-400">Canonical Question Entry</span>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8">
            <form action="{{ route('admin.mcqs.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- Taxonomy Hierarchy: Category, Subject, Topic --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pb-6 border-b border-gray-100">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Category <span class="text-rose-500">*</span>
                        </label>
                        <select name="category" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm focus:border-[#087F82] focus:outline-none">
                            <option value="Arithmetic Aptitude">Arithmetic Aptitude</option>
                            <option value="Data Interpretation">Data Interpretation</option>
                            <option value="Logical Reasoning">Logical Reasoning</option>
                            <option value="Verbal Ability">Verbal Ability</option>
                            <option value="Programming">Programming</option>
                            <option value="Engineering">Engineering</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Subject / Module
                        </label>
                        <input type="text" name="subject" value="Commercial Arithmetic" placeholder="e.g. Commercial Arithmetic" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm focus:border-[#087F82] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Leaf Topic <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="topic" value="Percentages" required placeholder="e.g. Percentages" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm focus:border-[#087F82] focus:outline-none">
                    </div>
                </div>

                {{-- Question Number & Status --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Question Number in Topic <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="question_number" value="11" required min="1" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm focus:border-[#087F82] focus:outline-none">
                        <p class="text-[11px] text-gray-400 mt-1">Unique sequential number within leaf topic.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Section Allocation
                        </label>
                        <select name="section" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm focus:border-[#087F82] focus:outline-none">
                            <option value="1">Section 1 (Questions 1 - 100)</option>
                            <option value="2">Section 2 (Questions 101 - 200)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Publication Status
                        </label>
                        <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm focus:border-[#087F82] focus:outline-none">
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                </div>

                {{-- Question Statement --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Question Statement <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="question" rows="3" required placeholder="Type the question clearly..." class="w-full p-3.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none"></textarea>
                </div>

                {{-- Optional Question Image --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Optional Question Diagram / Image
                    </label>
                    <div class="flex items-center gap-4">
                        <input type="file" @change="handleImage" accept="image/*" class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-[#153B70] hover:file:bg-gray-200">
                        <template x-if="imagePreview">
                            <div class="w-16 h-16 rounded-lg border border-gray-200 overflow-hidden bg-gray-50 flex items-center justify-center">
                                <img :src="imagePreview" class="w-full h-full object-cover">
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Four Selectable Options with Radio for Correct Option --}}
                <div class="space-y-3 pt-4 border-t border-gray-100">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Options & Answer Key (Click radio to select correct answer)
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Option A --}}
                        <div class="flex items-center gap-3 p-3 rounded-xl border" :class="correctOption === 'A' ? 'border-emerald-500 bg-emerald-50/50' : 'border-gray-200'">
                            <input type="radio" name="correct_option" value="A" x-model="correctOption" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                            <span class="font-bold text-xs text-gray-500">A.</span>
                            <input type="text" name="option_a" required placeholder="Option A text" class="w-full text-xs sm:text-sm border-0 bg-transparent p-0 focus:ring-0 focus:outline-none">
                        </div>

                        {{-- Option B --}}
                        <div class="flex items-center gap-3 p-3 rounded-xl border" :class="correctOption === 'B' ? 'border-emerald-500 bg-emerald-50/50' : 'border-gray-200'">
                            <input type="radio" name="correct_option" value="B" x-model="correctOption" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                            <span class="font-bold text-xs text-gray-500">B.</span>
                            <input type="text" name="option_b" required placeholder="Option B text" class="w-full text-xs sm:text-sm border-0 bg-transparent p-0 focus:ring-0 focus:outline-none">
                        </div>

                        {{-- Option C --}}
                        <div class="flex items-center gap-3 p-3 rounded-xl border" :class="correctOption === 'C' ? 'border-emerald-500 bg-emerald-50/50' : 'border-gray-200'">
                            <input type="radio" name="correct_option" value="C" x-model="correctOption" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                            <span class="font-bold text-xs text-gray-500">C.</span>
                            <input type="text" name="option_c" required placeholder="Option C text" class="w-full text-xs sm:text-sm border-0 bg-transparent p-0 focus:ring-0 focus:outline-none">
                        </div>

                        {{-- Option D --}}
                        <div class="flex items-center gap-3 p-3 rounded-xl border" :class="correctOption === 'D' ? 'border-emerald-500 bg-emerald-50/50' : 'border-gray-200'">
                            <input type="radio" name="correct_option" value="D" x-model="correctOption" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                            <span class="font-bold text-xs text-gray-500">D.</span>
                            <input type="text" name="option_d" required placeholder="Option D text" class="w-full text-xs sm:text-sm border-0 bg-transparent p-0 focus:ring-0 focus:outline-none">
                        </div>
                    </div>
                </div>

                {{-- Step-by-Step Explanation --}}
                <div class="pt-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Step-by-Step Explanation / Solution
                    </label>
                    <textarea name="explanation" rows="3" placeholder="Provide step-by-step mathematical or logical working..." class="w-full p-3.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none"></textarea>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.mcqs.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-semibold text-gray-600 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold shadow-xs transition-colors">
                        Save and Publish Question
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
