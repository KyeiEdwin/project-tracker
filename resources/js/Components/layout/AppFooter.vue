<script setup>
const currentYear = new Date().getFullYear()
</script>

<template>
  <footer class="footer mt-auto py-3 bg-white dark:bg-bodybg text-center">
    <div class="container">
      <span class="text-textmuted dark:text-textmuted/50">
        Copyright © {{ currentYear }} 
        <a href="javascript:void(0);" class="text-primary">Project Tracker</a>. 
        All rights reserved.
      </span>
    </div>
  </footer>
</template>

