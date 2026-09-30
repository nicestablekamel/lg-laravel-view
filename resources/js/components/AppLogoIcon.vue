<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg width="100%" height="100%" viewBox="0 0 800 800" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:affinity="https://www.affinity.studio/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
        <g transform="matrix(10.135306,0,0,10.135306,-136.948259,-576.820554)">
            <path d="M52.979,135.844C74.775,135.844 92.446,118.174 92.446,96.378C92.446,74.582 74.776,56.912 52.979,56.912C31.182,56.912 13.512,74.582 13.512,96.378C13.512,118.174 31.182,135.844 52.979,135.844Z" style="fill:rgb(167,11,82);fill-rule:nonzero;"/>
        </g>
        <g transform="matrix(10.135306,0,0,10.135306,-136.948259,-576.820554)">
            <path d="M62.589,110.267L54.416,110.267L54.416,79.377L51.385,79.377L51.385,113.315L54.416,113.315L54.416,113.295L62.589,113.295L62.589,110.267Z" style="fill:white;fill-rule:nonzero;"/>
        </g>
        <g transform="matrix(10.135306,0,0,10.135306,-136.948259,-576.820554)">
            <path d="M82.989,97.642L62.637,97.642L62.637,94.611L85.97,94.611C86.001,95.197 86.016,95.784 86.016,96.378C86.016,114.625 71.224,129.416 52.978,129.416C34.732,129.416 19.94,114.625 19.94,96.378C19.94,78.131 34.732,63.34 52.979,63.34C53.46,63.34 53.94,63.35 54.417,63.371L54.413,66.379C53.935,66.357 53.457,66.346 52.979,66.346C36.393,66.346 22.946,79.792 22.946,96.378C22.946,112.965 36.392,126.41 52.979,126.41C69.113,126.41 82.275,113.688 82.981,97.727L82.989,97.642Z" style="fill:white;fill-rule:nonzero;"/>
        </g>
        <g transform="matrix(10.135306,0,0,10.135306,-136.948259,-576.820554)">
            <path d="M40.655,88.521C43.202,88.521 45.298,86.425 45.298,83.879C45.298,81.332 43.202,79.236 40.655,79.236C38.108,79.236 36.013,81.332 36.013,83.879C36.013,86.425 38.108,88.521 40.655,88.521Z" style="fill:white;fill-rule:nonzero;"/>
        </g>
    </svg>
</template>
