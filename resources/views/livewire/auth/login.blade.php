<div class="min-h-screen flex items-center justify-center bg-slate-900 bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900">
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 pointer-events-none"></div>

    <div class="relative w-full max-w-md p-8 m-4 rounded-2xl shadow-2xl backdrop-blur-md bg-white/10 border border-white/20">
        
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold tracking-tight text-white mb-2">Calculadora DAS</h1>
            <p class="text-blue-200/80 text-sm font-medium">Faça login na sua conta para acessar o sistema.</p>
        </div>

        <form wire:submit="authenticate" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-blue-100 mb-1">E-mail</label>
                <div class="relative">
                    <input wire:model="email" id="email" type="email" autocomplete="email" required 
                           class="block w-full px-4 py-3 rounded-lg bg-slate-800/50 border border-slate-600/50 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all sm:text-sm" 
                           placeholder="seu@email.com.br">
                </div>
                @error('email') <span class="text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-blue-100 mb-1">Senha</label>
                <div class="relative">
                    <input wire:model="password" id="password" type="password" autocomplete="current-password" required 
                           class="block w-full px-4 py-3 rounded-lg bg-slate-800/50 border border-slate-600/50 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all sm:text-sm" 
                           placeholder="••••••••">
                </div>
                @error('password') <span class="text-red-400 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="pt-2">
                <button type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 focus:ring-offset-slate-900 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="authenticate">Entrar no Sistema</span>
                    <span wire:loading wire:target="authenticate" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Autenticando...
                    </span>
                </button>
            </div>
        </form>

        <div class="mt-8 pt-6 border-t border-white/10 text-center">
            <p class="text-xs text-blue-200/50">
                Acesso restrito &copy; {{ date('Y') }} Sistema de Gestão
            </p>
        </div>
    </div>
</div>
