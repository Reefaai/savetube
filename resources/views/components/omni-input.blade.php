<section class="max-w-3xl mx-auto px-6 mb-10 relative z-20">
    <div class="glass-panel p-2 rounded-2xl ghost-border ambient-shadow relative overflow-hidden group input-glow transition-all duration-300">
        {{-- Gradient border glow --}}
        <div class="absolute inset-0 bg-gradient-to-r from-primary/10 via-transparent to-primary-container/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>

        <form class="flex flex-col sm:flex-row gap-3 items-center relative z-10" @submit.prevent="processUrl()">
            <div class="flex-grow flex items-center w-full px-4 py-3 bg-surface-container-lowest rounded-xl ghost-border relative gap-2">

                {{-- Platform Badge (auto-detected or manually selected) --}}
                <div x-show="selectedPlatform" x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100"
                     class="flex-shrink-0 flex items-center gap-1.5 pl-2 pr-3 py-1.5 rounded-lg text-xs font-bold tracking-wide cursor-pointer select-none transition-all duration-200"
                     :class="{
                         'bg-[#FF0000]/15 text-[#FF6666] ring-1 ring-[#FF0000]/30': selectedPlatform === 'youtube',
                         'bg-[#00f2fe]/15 text-[#00f2fe] ring-1 ring-[#00f2fe]/30': selectedPlatform === 'tiktok',
                         'bg-[#1877F2]/15 text-[#4a9ef7] ring-1 ring-[#1877F2]/30': selectedPlatform === 'facebook',
                         'bg-[#E1306C]/15 text-[#e85c8a] ring-1 ring-[#E1306C]/30': selectedPlatform === 'instagram',
                         'bg-[#000000]/15 text-on-surface-variant ring-1 ring-outline-variant/30': selectedPlatform === 'threads',
                     }"
                     @click="showPlatformPicker = !showPlatformPicker"
                     style="display: none;">
                    <span class="material-symbols-outlined text-[16px]"
                          x-text="selectedPlatform === 'youtube' ? 'play_circle' :
                                  selectedPlatform === 'tiktok' ? 'music_note' :
                                  selectedPlatform === 'facebook' ? 'thumb_up' :
                                  selectedPlatform === 'instagram' ? 'photo_camera' : 'tag'"></span>
                    <span x-text="selectedPlatform" class="capitalize"></span>
                    <span class="material-symbols-outlined text-[14px] opacity-50">close</span>
                </div>

                {{-- Manual platform picker (fallback dropdown) --}}
                <div x-show="showPlatformPicker || (!selectedPlatform && videoUrl.trim())" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute left-0 right-0 top-full mt-2 p-3 bg-surface-container rounded-xl ghost-border ambient-shadow z-50"
                     @click.outside="showPlatformPicker = false"
                     style="display: none;">
                    <p class="text-[11px] text-outline uppercase tracking-widest font-semibold mb-2.5 px-1">Pilih Platform</p>
                    <div class="flex gap-2 flex-wrap">
                        <template x-for="p in ['youtube', 'tiktok', 'facebook', 'instagram']" :key="p">
                            <button type="button"
                                    @click="selectedPlatform = p; showPlatformPicker = false; showResult = false; errorMessage = '';"
                                    :class="selectedPlatform === p
                                        ? 'ring-2 ring-primary/40 bg-primary/10 text-primary-container'
                                        : 'bg-surface-container-high text-on-surface-variant hover:bg-surface-container-highest hover:text-on-surface'"
                                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold transition-all duration-150 cursor-pointer">
                                <span class="material-symbols-outlined text-[18px]"
                                      x-text="p === 'youtube' ? 'play_circle' : p === 'tiktok' ? 'music_note' : p === 'facebook' ? 'thumb_up' : 'photo_camera'"></span>
                                <span x-text="p" class="capitalize"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Paste icon --}}
                <span class="material-symbols-outlined text-outline flex-shrink-0" x-show="!selectedPlatform">content_paste</span>

                {{-- URL Input --}}
                <input autocomplete="off"
                       class="w-full bg-transparent border-none text-on-surface placeholder-outline focus:ring-0 font-body text-base px-0"
                       :placeholder="selectedPlatform === 'youtube'   ? 'Paste link YouTube di sini...' :
                                     selectedPlatform === 'tiktok'    ? 'Paste link TikTok di sini...' :
                                     selectedPlatform === 'facebook'  ? 'Paste link Facebook di sini...' :
                                     selectedPlatform === 'instagram' ? 'Paste link Instagram di sini...' :
                                     'Tempel link video dari platform mana saja...'"
                       type="text"
                       x-model="videoUrl"
                       :disabled="isLoading" />
            </div>

            <button class="w-full sm:w-auto cta-gradient font-bold px-8 py-4 rounded-xl flex items-center justify-center gap-2 hover-scale whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none transition-all duration-200"
                    type="submit"
                    :disabled="isLoading || !videoUrl.trim() || !selectedPlatform">
                {{-- Loading spinner --}}
                <svg x-show="isLoading" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{-- Default icon --}}
                <span x-show="!isLoading" class="material-symbols-outlined">download</span>
                <span x-text="isLoading ? 'Memproses...' : 'Proses Video'">Proses Video</span>
            </button>
        </form>
    </div>

    {{-- Hint: URL dimasukkan tapi platform belum terdeteksi --}}
    <p x-show="!selectedPlatform && videoUrl.trim()"
       x-transition.opacity
       class="text-center mt-3 text-sm text-error font-medium"
       style="display: none;">
        ⚠️ Platform tidak dikenali otomatis.
        <button type="button" @click="showPlatformPicker = true" class="underline underline-offset-2 hover:text-primary-container transition-colors">
            Pilih manual
        </button>
    </p>
</section>
