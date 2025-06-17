<template>
    <Head title="Naval Battle - Welcome" />

    <div class="relative h-screen bg-gradient-to-b from-blue-900 via-blue-800 to-blue-900 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0 bg-wave-pattern"></div>
        </div>
        

        <div class="absolute inset-0 opacity-5">
            <div class="grid-pattern"></div>
        </div>

        <div v-if="canLogin" class="absolute top-0 right-0 p-6 z-10">
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Dashboard
            </Link>

            <template v-else>
                <Link
                    :href="route('login')"
                    class="inline-flex items-center px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white font-semibold rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 mr-3"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Login
                </Link>

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Register
                </Link>
            </template>
        </div>


        <div class="flex items-center justify-center h-full px-6">
            <div class="max-w-4xl mx-auto text-center">
                <div class="mb-8">
                    <div class="flex justify-center mb-6">
                        <div class="relative">
                            <svg class="w-24 h-24 text-blue-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L13.09 8.26L22 9L13.09 9.74L12 16L10.91 9.74L2 9L10.91 8.26L12 2Z"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-16 h-16 text-slate-200" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 17h18v2H3v-2zm0-4h18v2H3v-2zm0-4h18v2H3V9zm0-4h18v2H3V5z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-3 tracking-tight">
                        <span class="bg-gradient-to-r from-blue-200 to-cyan-200 bg-clip-text text-transparent">
                            Batalla
                        </span>
                        <br>
                        <span class="bg-gradient-to-r from-slate-200 to-blue-200 bg-clip-text text-transparent">
                            Naval
                        </span>
                    </h1>
                    
                    <p class="text-lg md:text-xl text-blue-200 font-medium mb-6">
                        Juega con tus amigos • Domina • obten la Victoria
                    </p>
                </div>

                <div class="mb-8">
                    <div class="grid grid-cols-10 gap-1 max-w-xs mx-auto bg-slate-800/50 p-3 rounded-lg backdrop-blur-sm">
                        <div v-for="i in 100" :key="i" 
                             :class="[
                                 'aspect-square rounded-sm transition-all duration-200 w-3 h-3',
                                 getGridCellClass(i)
                             ]">
                        </div>
                    </div>
                    <p class="text-blue-300 text-xs mt-2 font-medium">Strategic Battle Grid</p>
                </div>

                <div v-if="!$page.props.auth.user" class="space-y-4">
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <Link
                            :href="route('login')"
                            class="group relative inline-flex items-center px-6 py-3 bg-gradient-to-r from-slate-600 to-slate-700 hover:from-slate-700 hover:to-slate-800 text-white font-bold text-base rounded-xl shadow-2xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-slate-500/50 min-w-[160px]"
                        >
                            <svg class="w-5 h-5 mr-2 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Login
                            <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-transparent via-white/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="group relative inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-bold text-base rounded-xl shadow-2xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-emerald-500/50 min-w-[160px]"
                        >
                            <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Register
                            <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-transparent via-white/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </Link>
                    </div>
                    
                    <p class="text-blue-300/80 text-sm">
                        Inicia sesión para unirte a la batalla
                    </p>
                </div>

                <div v-else class="space-y-6">
                    <div class="bg-blue-800/30 backdrop-blur-sm rounded-2xl p-8 border border-blue-600/30">
                        <h2 class="text-3xl font-bold text-white mb-4">Welcome back, Admiral!</h2>
                        <p class="text-blue-200 text-lg mb-6">Your fleet awaits your command</p>
                        <Link
                            :href="route('dashboard')"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Dashboard
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="absolute bottom-10 left-10 opacity-20 animate-float">
            <svg class="w-16 h-16 text-blue-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M3 17h18v2H3v-2zm0-4h18v2H3v-2zm0-4h18v2H3V9zm0-4h18v2H3V5z"/>
            </svg>
        </div>
        
        <div class="absolute top-20 right-20 opacity-20 animate-float-delayed">
            <svg class="w-12 h-12 text-slate-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9l-6.91.74L12 16l-3.09-6.26L2 9l6.91-.74L12 2z"/>
            </svg>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3';

export default {
    props: {
        canLogin: Boolean,
        canRegister: Boolean,
        laravelVersion: String,
        phpVersion: String,
    },
    components: {
        Head,
        Link,
    },
    methods: {
        getGridCellClass(index) {
            // Create a pattern that looks like a naval battle grid
            const row = Math.floor((index - 1) / 10);
            const col = (index - 1) % 10;
            
            // Add some "ships" and "hits" for visual effect
            const ships = [12, 13, 14, 27, 37, 47, 63, 64, 65, 66, 82, 92];
            const hits = [23, 45, 67, 89];
            const misses = [15, 25, 35, 55, 75, 85];
            
            if (ships.includes(index)) {
                return 'bg-slate-600 hover:bg-slate-500';
            } else if (hits.includes(index)) {
                return 'bg-red-600 hover:bg-red-500';
            } else if (misses.includes(index)) {
                return 'bg-blue-600 hover:bg-blue-500';
            } else {
                return 'bg-blue-900/50 hover:bg-blue-800/50';
            }
        }
    }
};
</script>

<style scoped>
.bg-wave-pattern {
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M30 30c0-11.046-8.954-20-20-20s-20 8.954-20 20 8.954 20 20 20 20-8.954 20-20zm0 0c0 11.046 8.954 20 20 20s20-8.954 20-20-8.954-20-20-20-20 8.954-20 20z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.grid-pattern {
    background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px),
                      linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px);
    background-size: 20px 20px;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(2deg); }
}

@keyframes float-delayed {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-15px) rotate(-2deg); }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 8s ease-in-out infinite;
    animation-delay: 2s;
}
</style>
