<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Overlay para móvil -->
        <div
            v-if="showingSidebar"
            class="fixed inset-0 z-20 bg-black bg-opacity-50 transition-opacity lg:hidden"
            @click="showingSidebar = false"
        ></div>

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 z-30 transform transition-transform duration-300 ease-in-out lg:translate-x-0"
            :class="{
                'translate-x-0': showingSidebar,
                '-translate-x-full': !showingSidebar,
            }"
        >
            <!-- Logo y título -->
            <div
                class="flex items-center justify-between h-16 border-b border-gray-200 px-4"
            >
                <div class="flex items-center">
                    <ApplicationLogo
                        class="block h-9 w-auto fill-current text-gray-800"
                    />
                    <span class="ml-3 text-xl font-semibold"
                        > Hola {{$page.props.auth.user.name}}</span
                    >
                </div>
                <!-- Botón cerrar en móvil -->
                <button
                    @click="showingSidebar = false"
                    class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100"
                >
                    <svg
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <!-- Navegación del sidebar -->
            <nav class="mt-5 px-2">
                <div class="space-y-1">
                    <!-- Dashboard Link -->
                    <Link
                        :href="route('dashboard')"
                        class="group flex items-center px-2 py-2 text-base font-medium rounded-md"
                        :class="[
                            route().current('dashboard')
                                ? 'bg-gray-100 text-gray-900'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                        ]"
                        @click="closeSidebarOnMobile"
                    >
                        <!-- Dashboard Icon -->
                        <svg
                            class="mr-3 h-6 w-6"
                            :class="[
                                route().current('dashboard')
                                    ? 'text-gray-500'
                                    : 'text-gray-400 group-hover:text-gray-500',
                            ]"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                            />
                        </svg>
                        Inicio
                    </Link>

                    <!-- Profile Link -->
                    <Link
                        :href="route('profile.edit')"
                        class="group flex items-center px-2 py-2 text-base font-medium rounded-md"
                        :class="[
                            route().current('profile.edit')
                                ? 'bg-gray-100 text-gray-900'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                        ]"
                        @click="closeSidebarOnMobile"
                    >
                        <!-- Profile Icon -->
                        <svg
                            class="mr-3 h-6 w-6"
                            :class="[
                                route().current('profile.edit')
                                    ? 'text-gray-500'
                                    : 'text-gray-400 group-hover:text-gray-500',
                            ]"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                            />
                        </svg>
                        Perfil
                    </Link>

                    <!-- Settings Section -->
                    <div class="mt-8">
                        <h3
                            class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider"
                        >
                            Configuración
                        </h3>
                        <div class="mt-1 space-y-1">
                            <a
                                href="#"
                                class="group flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900"
                            >
                                <svg
                                    class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                    />
                                </svg>
                                Seguridad
                            </a>
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="group flex items-center px-2 py-2 text-base font-medium rounded-md"
                                :class="[
                                    route().current('logout')
                                        ? 'bg-gray-100 text-gray-900'
                                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                ]"
                                @click="closeSidebarOnMobile"
                            >
                                <svg
                                    class="mr-3 h-6 w-6"
                                    :class="[
                                        route().current('profile.edit')
                                            ? 'text-gray-500'
                                            : 'text-gray-400 group-hover:text-gray-500',
                                    ]"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                    />
                                </svg>
                                Cerrar sesión
                            </Link>
                        </div>
                    </div>
                </div>
            </nav>
        </aside>

        <div class="lg:pl-64">
            <nav class="bg-white border-b border-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <button
                                @click="showingSidebar = true"
                                class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 mr-2"
                            >
                                <svg
                                    class="h-6 w-6"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                </svg>
                            </button>

                            <div
                                class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    Batalla Naval
                                </NavLink>
                            </div>
                        </div>
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </nav>
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

<script>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";

export default {
    components: {
        ApplicationLogo,
        Dropdown,
        DropdownLink,
        NavLink,
        ResponsiveNavLink,
        Link,
    },
    data() {
        return {
            showingNavigationDropdown: false,
            showingSidebar: false,
        };
    },
    methods: {
        closeSidebarOnMobile() {
            if (window.innerWidth < 1024) {
                this.showingSidebar = false;
            }
        },
    },
};
</script>
