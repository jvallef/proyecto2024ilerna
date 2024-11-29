@props([
    'route', 
    'placeholder' => 'Buscar...',
    'searchUrl',
    'minChars' => 2
])

<div x-data="{
    search: '{{ request('search') }}', 
    suggestions: [], 
    loading: false, 
    selectedIndex: -1, 
    showSuggestions: false, 
    minChars: {{ $minChars }},
    formatSuggestion(suggestion) {
        const [name, email] = suggestion.split('||');
        return `${name} (${email})`;
    },
    async fetchSuggestions() { 
        if (this.search.length < this.minChars) { 
            this.suggestions = []; 
            this.showSuggestions = false; 
            return; 
        } 
        
        this.loading = true; 
        try { 
            const response = await fetch(`{{ $searchUrl }}?q=${encodeURIComponent(this.search)}`, { 
                headers: { 
                    'Accept': 'application/json', 
                    'X-Requested-With': 'XMLHttpRequest', 
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content 
                }, 
                credentials: 'same-origin' 
            }); 
            if (!response.ok) { 
                throw new Error(`HTTP error! status: ${response.status}`); 
            } 
            const data = await response.json(); 
            this.suggestions = data; 
            this.showSuggestions = true; 
        } catch (error) { 
            console.error('Error fetching suggestions:', error); 
            this.suggestions = []; 
        } 
        this.loading = false; 
    }, 
    selectSuggestion(suggestion) { 
        this.search = suggestion;
        this.$nextTick(() => {
            this.showSuggestions = false;
            this.$refs.form.submit();
        });
    },
    clearSearch() {
        this.search = '';
        this.showSuggestions = false;
        window.location.href = '{{ $route }}';
    },
    handleKeydown(event) { 
        if (!this.showSuggestions) return; 
        switch(event.key) { 
            case 'ArrowDown': 
                event.preventDefault(); 
                this.selectedIndex = Math.min(this.selectedIndex + 1, this.suggestions.length - 1); 
                if (this.selectedIndex >= 0) {
                    this.search = this.suggestions[this.selectedIndex];
                }
                break; 
            case 'ArrowUp': 
                event.preventDefault(); 
                this.selectedIndex = Math.max(this.selectedIndex - 1, -1); 
                if (this.selectedIndex >= 0) {
                    this.search = this.suggestions[this.selectedIndex];
                }
                break; 
            case 'Enter': 
                event.preventDefault(); 
                if (this.selectedIndex >= 0) { 
                    this.selectSuggestion(this.suggestions[this.selectedIndex]); 
                } else { 
                    this.$refs.form.submit(); 
                } 
                break; 
            case 'Escape': 
                this.showSuggestions = false; 
                break; 
        } 
    }
}" @click.away="showSuggestions = false">
    <form x-ref="form" method="GET" action="{{ $route }}">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </div>
            
            <input type="text" 
                   name="search" 
                   x-model="search"
                   @input.debounce.300ms="fetchSuggestions"
                   @keydown="handleKeydown"
                   @focus="if (search.length >= minChars) { showSuggestions = true }"
                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-secondary focus:border-secondary sm:text-sm" 
                   placeholder="{{ $placeholder }}"
                   autocomplete="off">
                   
            <div x-show="loading" 
                 class="absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="animate-spin h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            
            <button x-show="search.length > 0" 
                    type="button"
                    @click="clearSearch()"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer">
                <svg class="w-4 h-4 text-gray-500 hover:text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div x-show="showSuggestions" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-1"
             class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
             style="display: none;">
            <template x-if="suggestions.length > 0">
                <ul class="divide-y divide-gray-200">
                    <template x-for="(suggestion, index) in suggestions" :key="index">
                        <li @click="selectSuggestion(suggestion)"
                            @mouseenter="selectedIndex = index"
                            :class="{ 'bg-gray-100': selectedIndex === index }"
                            class="px-4 py-2 hover:bg-gray-100 cursor-pointer">
                            <span x-text="formatSuggestion(suggestion)"></span>
                        </li>
                    </template>
                </ul>
            </template>
            <template x-if="suggestions.length === 0 && !loading && search.length >= minChars">
                <div class="px-4 py-2 text-sm text-gray-500">
                    No se encontraron resultados
                </div>
            </template>
        </div>
    </form>
</div>
