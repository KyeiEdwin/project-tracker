<script setup>
import { computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
defineProps({ title:{type:String,required:true},subtitle:{type:String,default:''} })
const page=usePage(); const breadcrumbs=computed(()=>{const parts=page.url.split('?')[0].split('/').filter(Boolean);return[{label:'Workspace',to:'/'},...parts.map((part,index)=>({label:part.replace(/-/g,' ').replace(/\b\w/g,l=>l.toUpperCase()),to:index===parts.length-1?null:`/${parts.slice(0,index+1).join('/')}`}))]})
</script>
<template><div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2"><div><nav aria-label="Breadcrumb"><ol class="breadcrumb"><li v-for="(crumb,index) in breadcrumbs" :key="index" class="breadcrumb-item" :class="{active:!crumb.to}"><Link v-if="crumb.to" :href="crumb.to">{{ crumb.label }}</Link><span v-else>{{ crumb.label }}</span></li></ol></nav><h1 class="page-title">{{ title }}</h1><p v-if="subtitle" class="text-textmuted text-sm mt-1">{{ subtitle }}</p></div><div class="btn-list"><slot name="actions"></slot></div></div></template>
