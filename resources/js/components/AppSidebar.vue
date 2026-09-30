<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BookOpen, Box, FolderGit2, LayoutGrid, LayoutTemplate, List, Mail, MessageCircle, Phone, Plus } from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';

import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';
import { index as invoicesIndex } from '@/routes/invoices'
import { create } from '@/routes/invoices';
import { index as partsIndex } from '@/routes/parts'
import { email } from '@/routes/password';

const mainNavItems: NavItem[] = [
    {
        title: 'Tableau de bord',
        href: dashboard(),
        icon: LayoutTemplate ,
    },
    {
        title: 'Nouvelle Décharge',
        href: create(),
        icon: Plus,
    },
    {
        title: 'Historique',
        href: invoicesIndex(),
        icon: List,
    },
    {
        title: 'Stock',
        href: partsIndex(),
        icon: Box,
    },  


];

const footerNavItems: NavItem[] = [
    {
        title: '+213 778 64 24 19',
        href: 'tel: +213 778 64 24 19',
        icon: Phone,
    },
    {
        title: 'Email Support',
        href: 'mailto: amranikamel016@gmail.com',
        icon: Mail,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
