<template>
    <v-card class="pa-3 rounded" style="height: 100vh;">
      <v-layout style="height: 100%;">
        <!-- Sidebar -->
        <v-navigation-drawer
          class="rounded bg-surface elevation-10 "
          v-model="drawer"
          :rail="rail"
          permanent
          floating
          style="height: 100%;"
          @click="toggleRail"
         
        >
          <div>
            <v-divider></v-divider>
            <v-list comfortable nav>
              <v-list-item class="bg-surface" prepend-icon="mdi-home-city" title="Home" value="home"></v-list-item>
              <v-list-item class="bg-primary" prepend-icon="mdi-account" title="My Account" value="account"></v-list-item>
              <v-list-item class="bg-primary" prepend-icon="mdi-account-group-outline" title="Users" value="users"></v-list-item>
            </v-list>
          </div>
        </v-navigation-drawer>
  
        <!-- Main Content -->
        <v-main class="mx-4"  >
        
         
  
              <!-- Toolbar on the Right -->
          
    <v-toolbar density="default" :elevation="2"  class="bg-secondary-darken-1 rounded-10">
      <v-app-bar-nav-icon></v-app-bar-nav-icon>

      <v-toolbar-title>Title</v-toolbar-title>

      <v-spacer></v-spacer>

      <v-btn icon>
        <v-icon>mdi-magnify</v-icon>
      </v-btn>

      <v-btn icon>
        <v-icon>mdi-heart</v-icon>
      </v-btn>

      <v-btn icon>
        <v-icon>mdi-dots-vertical</v-icon>
      </v-btn>
    </v-toolbar>

  
              <!-- Your main content goes here -->
           <!-- <<router-view></router-view>> -->
            
       
        </v-main>
      </v-layout>
    </v-card>
  </template>
  
  <script>
  export default {
    data() {
      return {
        drawer: true,
        rail: true,
      };
    },
    methods: {
      toggleRail() {
        this.rail = !this.rail;
      },
    },
  };
  </script>
  