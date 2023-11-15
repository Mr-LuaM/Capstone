<template>
  <v-card>
    <v-layout>
      <v-navigation-drawer
        app
        :value="sidebarOpen"
        @input="$emit('update:sidebarOpen', $event)"
      >
        <!-- Sidebar content goes here -->
        <v-list>
          <!-- Your menu items -->
        </v-list>
      </v-navigation-drawer>
    </v-layout>
  </v-card>
</template>

<script>
export default {
  props: {
    sidebarOpen: Boolean,
  },
};
</script>
