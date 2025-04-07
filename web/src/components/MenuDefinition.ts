import { h } from 'vue';

export interface MenuItem {
    title: string;
    path: string;
    requiredPermission?: string;
}

export interface ModuleDefinition {
    name: string;
    permissionKey: string;
    menuItems: MenuItem[];
}

export const moduleDefinitions: Record<string, ModuleDefinition> = {
    personnel: {
        name: 'Gestion du personnel',
        permissionKey: 'personnel',
        menuItems: [
            {
                title: 'Liste des collaborateurs',
                path: '/collaborateurs',
                requiredPermission: 'read'
            },
            {
                title: 'Ajouter un collaborateur',
                path: '/collaborateurs/ajouter',
                requiredPermission: 'create'
            },
            {
                title: 'Historique des modifications',
                path: '/collaborateurs/historique',
                requiredPermission: 'read'
            }
        ]
    },
    roles: {
        name: 'Rôles et permissions',
        permissionKey: 'roles',
        menuItems: [
            {
                title: 'Gestion des rôles',
                path: '/roles',
                requiredPermission: 'read'
            },
            {
                title: 'Permissions',
                path: '/roles/permissions',
                requiredPermission: 'read'
            }
        ]
    },
    enterprise: {
        name: 'Mon entreprise',
        permissionKey: 'enterprise',
        menuItems: [
            {
                title: 'Informations',
                path: '/entreprise',
                requiredPermission: 'read'
            },
            {
                title: 'Facturation',
                path: '/entreprise/facturation',
                requiredPermission: 'read'
            }
        ]
    }
};

export const moduleIcons = {
    personnel: () => h('svg', {
        xmlns: 'http://www.w3.org/2000/svg',
        class: 'h-6 w-6',
        fill: 'none',
        viewBox: '0 0 24 24',
        stroke: 'currentColor'
    }, [
        h('path', {
            'stroke-linecap': 'round',
            'stroke-linejoin': 'round',
            'stroke-width': '2',
            d: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'
        })
    ]),
    roles: () => h('svg', {
        xmlns: 'http://www.w3.org/2000/svg',
        class: 'h-6 w-6',
        fill: 'none',
        viewBox: '0 0 24 24',
        stroke: 'currentColor'
    }, [
        h('path', {
            'stroke-linecap': 'round',
            'stroke-linejoin': 'round',
            'stroke-width': '2',
            d: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
        })
    ]),
    enterprise: () => h('svg', {
        xmlns: 'http://www.w3.org/2000/svg',
        class: 'h-6 w-6',
        fill: 'none',
        viewBox: '0 0 24 24',
        stroke: 'currentColor'
    }, [
        h('path', {
            'stroke-linecap': 'round',
            'stroke-linejoin': 'round',
            'stroke-width': '2',
            d: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'
        })
    ]),
    default: () => h('svg', {
        xmlns: 'http://www.w3.org/2000/svg',
        class: 'h-6 w-6',
        fill: 'none',
        viewBox: '0 0 24 24',
        stroke: 'currentColor'
    }, [
        h('path', {
            'stroke-linecap': 'round',
            'stroke-linejoin': 'round',
            'stroke-width': '2',
            d: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'
        })
    ])
};