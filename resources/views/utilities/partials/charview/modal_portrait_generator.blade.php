<!-- AI Character Portrait Generator Modal -->
<div x-show="showPortraitModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4" @keydown.escape.window="showPortraitModal = false" @paste.window="if(showPortraitModal) handleClipboardPaste($event)">
    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full border border-amber-900/30 overflow-hidden relative z-[10000] max-h-[94vh] flex flex-col"
         @click.outside="if(!isGeneratingPortraits && !isSavingPortrait) showPortraitModal = false"
         x-data="portraitGeneratorModal({{ $character ? $character->ID : 0 }}, '{{ $character ? addslashes($character->Name) : '' }}', '{{ isset($character) && $character ? addslashes(\App\Services\AI\GeminiImageService::generatePromptFromCharacter($character, $calculatedState ?? [], ['race_name' => $race->Name ?? 'Humanoid', 'templates' => (isset($templates) && $templates->isNotEmpty()) ? $templates->pluck('Name')->toArray() : [], 'classes_map' => isset($classesMap) ? collect($classesMap)->map(fn($c) => $c->Name ?? '')->toArray() : []])) : '' }}')">
        
        <!-- Modal Header Plaque -->
        <div class="px-6 py-4 flex items-center justify-between border-b border-amber-950/30 shrink-0" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff;">
            <div class="font-serif font-bold text-lg flex items-center gap-2 text-amber-200">
                <span>🎨</span>
                <span>AI Character Portrait Studio — {{ $character->Name }}</span>
            </div>
            <button @click="showPortraitModal = false" :disabled="isGeneratingPortraits || isSavingPortrait" class="text-stone-400 hover:text-white font-bold text-2xl leading-none cursor-pointer transition disabled:opacity-30">&times;</button>
        </div>

        <div class="p-5 sm:p-6 overflow-y-auto space-y-5 flex-1 bg-stone-50/80">
            <!-- Toast Notification for Prompt Copied -->
            <div x-show="toastMessage" x-transition class="bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-lg flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span>📋</span>
                    <span x-text="toastMessage"></span>
                </div>
                <button type="button" @click="toastMessage = ''" class="text-white/80 hover:text-white">&times;</button>
            </div>

            <!-- Free AI Services Recommendation Banner -->
            <div class="bg-amber-50/70 border border-amber-800/20 rounded-xl p-4 text-xs text-stone-800 space-y-3 shadow-xs">
                <div class="flex items-center justify-between">
                    <div class="font-bold flex items-center gap-1.5 text-amber-950 text-sm">
                        <span>🌟</span>
                        <span>Free AI Image Generators</span>
                    </div>
                    <span class="bg-amber-200/60 text-amber-950 px-2 py-0.5 rounded text-[11px] font-bold">100% Free Tiers</span>
                </div>
                <p class="text-stone-600 leading-relaxed">
                    Generate high-fidelity fantasy portraits for <strong>{{ $character->Name }}</strong> using leading free AI image services. Click any service below to automatically <strong>copy your tailored character prompt</strong> and launch the generator:
                </p>

                <!-- Free Services Quick Buttons Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                    <!-- Microsoft Designer (Most Popular Free Tier) -->
                    <button type="button" @click="copyAndLaunch('https://designer.microsoft.com/image-creator', 'Microsoft Designer (DALL-E 3)')"
                            class="flex items-center justify-between p-3 rounded-xl bg-gradient-to-r from-blue-700 to-indigo-800 text-white font-bold hover:from-blue-800 hover:to-indigo-900 transition shadow-sm cursor-pointer group text-left">
                        <div class="flex items-center gap-2.5">
                            <span class="text-lg">✨</span>
                            <div>
                                <div class="font-bold text-xs">Microsoft Designer</div>
                                <div class="text-[10px] text-blue-200 font-normal">Most Popular Free DALL-E 3 Generator</div>
                            </div>
                        </div>
                        <span class="text-xs group-hover:translate-x-0.5 transition font-mono">↗</span>
                    </button>

                    <!-- Bing Image Creator -->
                    <button type="button" @click="copyAndLaunch('https://www.bing.com/images/create', 'Bing Image Creator')"
                            class="flex items-center justify-between p-3 rounded-xl bg-gradient-to-r from-teal-700 to-cyan-800 text-white font-bold hover:from-teal-800 hover:to-cyan-900 transition shadow-sm cursor-pointer group text-left">
                        <div class="flex items-center gap-2.5">
                            <span class="text-lg">🎨</span>
                            <div>
                                <div class="font-bold text-xs">Bing Image Creator</div>
                                <div class="text-[10px] text-teal-200 font-normal">Free AI Studio by Microsoft</div>
                            </div>
                        </div>
                        <span class="text-xs group-hover:translate-x-0.5 transition font-mono">↗</span>
                    </button>

                    <!-- Leonardo.ai (RPG Art Specialist) -->
                    <button type="button" @click="copyAndLaunch('https://leonardo.ai', 'Leonardo.ai')"
                            class="flex items-center justify-between p-2.5 rounded-xl bg-purple-900/10 hover:bg-purple-900/20 border border-purple-800/30 text-purple-950 font-bold transition cursor-pointer group text-left">
                        <div class="flex items-center gap-2">
                            <span>🎭</span>
                            <div>
                                <div class="font-bold text-xs">Leonardo.ai</div>
                                <div class="text-[10px] text-stone-500 font-normal">150 Free Fast Generations Daily (RPG Art)</div>
                            </div>
                        </div>
                        <span class="text-xs group-hover:translate-x-0.5 transition font-mono">↗</span>
                    </button>

                    <!-- Pollinations.ai (No Login Instant) -->
                    <button type="button" @click="copyAndLaunch('https://pollinations.ai', 'Pollinations.ai')"
                            class="flex items-center justify-between p-2.5 rounded-xl bg-amber-900/10 hover:bg-amber-900/20 border border-amber-800/30 text-amber-950 font-bold transition cursor-pointer group text-left">
                        <div class="flex items-center gap-2">
                            <span>⚡</span>
                            <div>
                                <div class="font-bold text-xs">Pollinations.ai</div>
                                <div class="text-[10px] text-stone-500 font-normal">Instant Open FLUX (No Login Required)</div>
                            </div>
                        </div>
                        <span class="text-xs group-hover:translate-x-0.5 transition font-mono">↗</span>
                    </button>
                </div>
            </div>

            <!-- Error Banner -->
            <template x-if="errorMessage">
                <div class="bg-red-50 border border-red-300 rounded-xl p-3.5 text-xs text-red-900 flex items-start gap-2.5">
                    <span class="text-base shrink-0">⚠️</span>
                    <div class="space-y-1">
                        <div class="font-bold">Generation / Save Error</div>
                        <div class="text-red-700 font-mono text-[11px] whitespace-pre-wrap" x-text="errorMessage"></div>
                    </div>
                </div>
            </template>

            <!-- Success Banner -->
            <template x-if="successMessage">
                <div class="bg-emerald-50 border border-emerald-300 rounded-xl p-3.5 text-xs text-emerald-900 flex items-center gap-2.5 font-bold">
                    <span class="text-base">✓</span>
                    <span x-text="successMessage"></span>
                </div>
            </template>

            <!-- Character Prompt Directive Box -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold uppercase text-stone-700">Artistic Prompt Directive (Auto-Synthesized)</label>
                    <button type="button" @click="promptText = defaultPrompt" class="text-xs text-amber-800 hover:text-amber-950 font-semibold underline cursor-pointer">
                        🔄 Reset to Default
                    </button>
                </div>
                <textarea x-model="promptText" rows="3"
                          class="w-full px-3 py-2 border border-stone-300 rounded-xl text-xs font-serif text-stone-900 bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none shadow-inner leading-relaxed"></textarea>
                
                <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                    <button type="button" @click="copyPromptOnly()" class="px-3 py-1.5 rounded-lg border border-stone-300 bg-white hover:bg-stone-100 text-stone-700 text-xs font-bold flex items-center gap-1.5 cursor-pointer shadow-xs">
                        <span>📋 Copy Prompt</span>
                    </button>

                    <!-- In-App 1-Click Generation Button -->
                    <button type="button" @click="generatePortraitsInApp('free')" :disabled="isGeneratingPortraits || isSavingPortrait || !promptText.trim()"
                            class="btn-rol-primary px-4 py-2 text-xs font-bold flex items-center gap-1.5 cursor-pointer disabled:opacity-50">
                        <span x-show="!isGeneratingPortraits">⚡ Quick Generate In-App (Free)</span>
                        <span x-show="isGeneratingPortraits" class="flex items-center gap-1.5">
                            <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Painting Portraits...</span>
                        </span>
                    </button>
                </div>
            </div>

            <!-- Loading Skeleton State -->
            <div x-show="isGeneratingPortraits" class="p-8 text-center bg-white border border-amber-900/20 rounded-2xl space-y-3">
                <div class="inline-flex p-3 rounded-full bg-amber-100/60 text-amber-900 animate-pulse text-2xl">
                    🎨
                </div>
                <h4 class="font-serif font-bold text-stone-900 text-sm">Crafting Character Visuals with FLUX AI...</h4>
                <p class="text-xs text-stone-500 max-w-sm mx-auto">Synthesizing 4 candidate portraits. This typically takes 5–15 seconds.</p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3">
                    <template x-for="i in 4" :key="i">
                        <div class="aspect-square rounded-xl bg-stone-200 animate-pulse border border-stone-300 flex items-center justify-center text-stone-400">
                            <span>🖼️</span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Candidate Images Grid (When In-App generation produces results) -->
            <div x-show="candidateImages.length > 0 && !isGeneratingPortraits" class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase text-stone-700">Select Generated Portrait (Click to Choose):</span>
                    <span class="text-xs text-amber-900 font-semibold" x-show="selectedImageIndex !== null">
                        ✓ Option <span x-text="selectedImageIndex + 1"></span> Selected
                    </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <template x-for="(img, idx) in candidateImages" :key="idx">
                        <div @click="selectedImageIndex = idx"
                             class="aspect-square rounded-xl overflow-hidden cursor-pointer relative group transition duration-200 border-3 shadow-md"
                             :class="selectedImageIndex === idx ? 'border-amber-500 ring-4 ring-amber-400/40 scale-[1.02]' : 'border-stone-300 hover:border-amber-400 hover:shadow-lg'">
                            <img :src="img.base64" :alt="'Portrait option ' + (idx + 1)" class="w-full h-full object-cover">
                            
                            <!-- Selection badge -->
                            <div class="absolute top-2 right-2 rounded-full px-2 py-0.5 text-[10px] font-bold shadow"
                                 :class="selectedImageIndex === idx ? 'bg-amber-500 text-slate-950' : 'bg-slate-900/60 text-white opacity-0 group-hover:opacity-100'">
                                <span x-text="selectedImageIndex === idx ? '✓ Selected' : 'Choose #' + (idx + 1)"></span>
                            </div>

                            <!-- Bottom bar -->
                            <div class="absolute inset-x-0 bottom-0 p-1.5 bg-gradient-to-t from-black/80 to-transparent text-center">
                                <span class="text-[10px] text-white font-serif font-bold">Option <span x-text="idx + 1"></span></span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Save Action for Selected Image -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="saveSelectedPortrait()" :disabled="selectedImageIndex === null || isSavingPortrait"
                            class="btn-rol-success px-5 py-2.5 text-xs font-bold flex items-center gap-2 cursor-pointer shadow-md disabled:opacity-40">
                        <span x-show="!isSavingPortrait">💾 Apply Selected Portrait to Character</span>
                        <span x-show="isSavingPortrait" class="flex items-center gap-1.5">
                            <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Saving Portrait...</span>
                        </span>
                    </button>
                </div>
            </div>

            <!-- Pasted Image Preview Box (When Ctrl+V is used) -->
            <div x-show="pastedImageData" class="bg-amber-50/80 border-2 border-dashed border-amber-600 rounded-xl p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="font-bold text-xs text-amber-950 flex items-center gap-1.5">
                        <span>📋</span>
                        <span>Pasted Image from Clipboard</span>
                    </div>
                    <button type="button" @click="pastedImageData = ''" class="text-xs text-stone-500 hover:text-red-700 font-bold">
                        &times; Remove
                    </button>
                </div>
                <div class="flex items-center gap-4">
                    <img :src="pastedImageData" alt="Pasted preview" class="w-24 h-24 object-cover rounded-lg border border-amber-800/30 shadow-sm">
                    <div class="space-y-2">
                        <p class="text-xs text-stone-600">Ready to attach this image as {{ $character->Name }}'s official portrait.</p>
                        <button type="button" @click="savePastedPortrait()" :disabled="isSavingPortrait"
                                class="btn-rol-success px-4 py-2 text-xs font-bold flex items-center gap-1.5 cursor-pointer shadow-sm">
                            <span>💾 Save Pasted Portrait</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Image Import & Upload Methods -->
            <div class="pt-4 border-t border-stone-300/80 space-y-4">
                <div class="font-bold text-xs uppercase text-stone-700 tracking-wider">
                    Apply Portrait from External Generator or File
                </div>

                <!-- Method A: Paste Image URL -->
                <div class="bg-white border border-stone-300 rounded-xl p-3.5 space-y-2 shadow-2xs">
                    <label class="block font-bold text-xs text-stone-800">🔗 Option A: Paste Image URL / Web Address</label>
                    <div class="flex gap-2">
                        <input type="url" x-model="imageUrlInput" placeholder="https://... (Right-click image on Microsoft Designer & Copy Image Link)"
                               class="flex-1 px-3 py-1.5 border border-stone-300 rounded-lg text-xs bg-stone-50 text-stone-900 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        <button type="button" @click="importFromUrl()" :disabled="!imageUrlInput.trim() || isSavingPortrait"
                                class="btn-rol-secondary px-3.5 py-1.5 text-xs font-bold shrink-0 cursor-pointer disabled:opacity-40">
                            <span>⬇️ Import URL</span>
                        </button>
                    </div>
                </div>

                <!-- Method B: Upload File or Ctrl+V Paste -->
                <div class="bg-white border border-stone-300 rounded-xl p-3.5 space-y-2 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <label class="block font-bold text-xs text-stone-800">📁 Option B: Upload Image File (or Press Ctrl+V to Paste)</label>
                        <span class="text-[11px] text-stone-500">PNG, JPG, WebP</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                        <input type="file" x-ref="customFileInput" accept="image/png, image/jpeg, image/webp"
                               class="text-xs text-stone-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border file:border-stone-300 file:text-xs file:font-bold file:bg-stone-100 file:text-stone-800 hover:file:bg-stone-200 cursor-pointer flex-1">
                        <button type="button" @click="uploadCustomPortrait()" :disabled="isSavingPortrait"
                                class="btn-rol-secondary px-3.5 py-1.5 text-xs font-bold shrink-0 cursor-pointer">
                            <span>📁 Upload File</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Collapsible Advanced API Settings -->
            <div class="pt-2 border-t border-stone-300/60">
                <button type="button" @click="showApiKeySettings = !showApiKeySettings" class="text-[11px] text-stone-500 hover:text-stone-800 underline font-semibold cursor-pointer flex items-center gap-1">
                    <span>⚙️</span>
                    <span x-text="showApiKeySettings ? 'Hide Google AI / Gemini API Configuration' : 'Advanced: Google AI / Gemini API Configuration'"></span>
                </button>

                <div x-show="showApiKeySettings" x-collapse class="pt-2.5 space-y-2 text-xs">
                    <label class="block font-bold text-stone-700">Google AI / Gemini API Key:</label>
                    <div class="flex items-center gap-2">
                        <input :type="showKeyText ? 'text' : 'password'" x-model="customApiKey" @input="localStorage.setItem('rold20_gemini_api_key', customApiKey)" placeholder="AIzaSy... (Leave empty to use free Pollinations engine)"
                               class="flex-1 px-3 py-1.5 border border-stone-300 rounded-lg text-xs font-mono bg-white text-stone-900 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        <button type="button" @click="showKeyText = !showKeyText" class="px-2.5 py-1.5 border border-stone-300 rounded-lg bg-white text-stone-600 hover:bg-stone-100 font-mono text-xs cursor-pointer">
                            <span x-text="showKeyText ? '🙈 Hide' : '👁️ Show'"></span>
                        </button>
                    </div>
                    <p class="text-[11px] text-stone-500">
                        Note: Google AI Studio requires a project with Pay-As-You-Go billing enabled for image generation. If left blank, the app uses 100% free AI generation.
                    </p>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-3 bg-stone-100 border-t border-stone-300/80 flex items-center justify-between shrink-0">
            <button type="button" @click="showPortraitModal = false" class="px-4 py-2 text-xs font-bold text-stone-600 hover:text-stone-800 cursor-pointer">
                Close
            </button>
            <div class="text-[11px] text-stone-500">
                Portraits are stored securely in your RoL d20 database.
            </div>
        </div>
    </div>
</div>

<script>
function portraitGeneratorModal(characterId, characterName, defaultPrompt) {
    return {
        characterId: characterId,
        characterName: characterName,
        defaultPrompt: defaultPrompt,
        promptText: defaultPrompt,
        imageUrlInput: '',
        pastedImageData: '',
        customApiKey: localStorage.getItem('rold20_gemini_api_key') || '',
        showApiKeySettings: false,
        showKeyText: false,
        isGeneratingPortraits: false,
        isSavingPortrait: false,
        candidateImages: [],
        selectedImageIndex: null,
        errorMessage: '',
        successMessage: '',
        toastMessage: '',

        async copyAndLaunch(url, serviceName) {
            try {
                await navigator.clipboard.writeText(this.promptText);
                this.toastMessage = `✓ Prompt copied to clipboard! Paste it into ${serviceName}.`;
            } catch (e) {
                this.toastMessage = `Prompt ready! Please copy from the box below and paste into ${serviceName}.`;
            }
            window.open(url, '_blank');
        },

        async copyPromptOnly() {
            try {
                await navigator.clipboard.writeText(this.promptText);
                this.toastMessage = '✓ Prompt copied to clipboard!';
                setTimeout(() => { if (this.toastMessage.includes('copied')) this.toastMessage = ''; }, 3000);
            } catch (e) {
                alert('Could not auto-copy. Please select and copy the text manually.');
            }
        },

        handleClipboardPaste(event) {
            const items = (event.clipboardData || event.originalEvent?.clipboardData)?.items;
            if (!items) return;

            for (let i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1) {
                    const blob = items[i].getAsFile();
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.pastedImageData = e.target.result;
                        this.toastMessage = '✓ Image pasted from clipboard! Click Save to apply.';
                    };
                    reader.readAsDataURL(blob);
                    break;
                }
            }
        },

        async generatePortraitsInApp(provider = 'auto') {
            if (!this.promptText.trim()) return;
            this.isGeneratingPortraits = true;
            this.errorMessage = '';
            this.successMessage = '';
            this.candidateImages = [];
            this.selectedImageIndex = null;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const response = await fetch(`/utilities/character-viewer/${this.characterId}/generate-portraits`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        prompt: this.promptText,
                        provider: provider,
                        api_key: this.customApiKey.trim() || undefined,
                        sample_count: 4
                    })
                });

                const data = await response.json().catch(() => null);
                if (response.ok && data && data.success && data.images) {
                    this.candidateImages = data.images;
                    this.selectedImageIndex = 0; // default select first
                } else {
                    this.errorMessage = data?.message || `Generation failed (status ${response.status}).`;
                }
            } catch (err) {
                this.errorMessage = 'Network or server error during image generation: ' + (err.message || err);
            } finally {
                this.isGeneratingPortraits = false;
            }
        },

        async saveSelectedPortrait() {
            if (this.selectedImageIndex === null || !this.candidateImages[this.selectedImageIndex]) return;
            this.isSavingPortrait = true;
            this.errorMessage = '';
            this.successMessage = '';

            const selected = this.candidateImages[this.selectedImageIndex];

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const response = await fetch(`/utilities/character-viewer/${this.characterId}/save-portrait`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        image_data: selected.base64,
                        mime_type: selected.mimeType || 'image/jpeg'
                    })
                });

                const data = await response.json().catch(() => null);
                if (response.ok && data && data.success) {
                    this.successMessage = data.message || 'Portrait saved successfully!';
                    setTimeout(() => { window.location.reload(); }, 1000);
                } else {
                    this.errorMessage = data?.message || `Failed to save portrait (status ${response.status}).`;
                }
            } catch (err) {
                this.errorMessage = 'Network or server error saving portrait: ' + (err.message || err);
            } finally {
                this.isSavingPortrait = false;
            }
        },

        async savePastedPortrait() {
            if (!this.pastedImageData) return;
            this.isSavingPortrait = true;
            this.errorMessage = '';
            this.successMessage = '';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const response = await fetch(`/utilities/character-viewer/${this.characterId}/save-portrait`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        image_data: this.pastedImageData,
                        mime_type: 'image/jpeg'
                    })
                });

                const data = await response.json().catch(() => null);
                if (response.ok && data && data.success) {
                    this.successMessage = data.message || 'Pasted portrait saved successfully!';
                    setTimeout(() => { window.location.reload(); }, 1000);
                } else {
                    this.errorMessage = data?.message || `Failed to save portrait (status ${response.status}).`;
                }
            } catch (err) {
                this.errorMessage = 'Network or server error saving portrait: ' + (err.message || err);
            } finally {
                this.isSavingPortrait = false;
            }
        },

        async importFromUrl() {
            if (!this.imageUrlInput.trim()) return;
            this.isSavingPortrait = true;
            this.errorMessage = '';
            this.successMessage = '';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const response = await fetch(`/utilities/character-viewer/${this.characterId}/save-portrait-url`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        url: this.imageUrlInput.trim()
                    })
                });

                const data = await response.json().catch(() => null);
                if (response.ok && data && data.success) {
                    this.successMessage = data.message || 'Portrait imported from URL successfully!';
                    setTimeout(() => { window.location.reload(); }, 1000);
                } else {
                    this.errorMessage = data?.message || `Failed to import image (status ${response.status}).`;
                }
            } catch (err) {
                this.errorMessage = 'Network error downloading image from URL: ' + (err.message || err);
            } finally {
                this.isSavingPortrait = false;
            }
        },

        async uploadCustomPortrait() {
            const input = this.$refs.customFileInput;
            if (!input || !input.files || !input.files[0]) {
                alert('Please select an image file first.');
                return;
            }

            this.isSavingPortrait = true;
            this.errorMessage = '';
            this.successMessage = '';

            const formData = new FormData();
            formData.append('portrait', input.files[0]);

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const response = await fetch(`/utilities/character-viewer/${this.characterId}/upload-portrait`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: formData
                });

                const data = await response.json().catch(() => null);
                if (response.ok && data && data.success) {
                    this.successMessage = data.message || 'Portrait uploaded successfully!';
                    setTimeout(() => { window.location.reload(); }, 1000);
                } else {
                    this.errorMessage = data?.message || `Upload failed (status ${response.status}).`;
                }
            } catch (err) {
                this.errorMessage = 'Network or server error uploading portrait: ' + (err.message || err);
            } finally {
                this.isSavingPortrait = false;
            }
        }
    };
}
</script>