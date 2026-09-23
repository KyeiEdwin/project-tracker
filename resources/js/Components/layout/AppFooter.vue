<script setup>
const currentYear = new Date().getFullYear()
</script>

<template>
  <footer class="footer mt-auto py-3 bg-white dark:bg-bodybg text-center border-t border-defaultborder dark:border-defaultborder/10">
    <div class="container px-4">
      <span class="text-textmuted dark:text-textmuted/50 text-sm">
        Copyright © {{ currentYear }} 
        <a href="javascript:void(0);" class="text-primary hover:underline">Project Tracker</a>. 
        All rights reserved.
      </span>
    </div>
  </footer>
</template>

