<template>
    <v-card class="pa-3 rounded-lg" style="height: 100vh;">
      <v-layout style="height: 100%;">
        <!-- Sidebar -->
        <v-navigation-drawer
          class="rounded-lg bg-primary  elevation-10 "
          v-model="drawer"
          :rail="rail"
          permanent
          floating
          style="height: 100%;"
          @click="toggleRail"
         
        >
          <div>
            <v-divider></v-divider>
            <v-list dense nav>
              <v-list-item prepend-icon="mdi-home-city" title="Home" value="home"></v-list-item>
              <v-list-item prepend-icon="mdi-account" title="My Account" value="account"></v-list-item>
              <v-list-item prepend-icon="mdi-account-group-outline" title="Users" value="users"></v-list-item>
            </v-list>
          </div>
        </v-navigation-drawer>
  
        <!-- Main Content -->
        <v-main class="mx-4"  >
        
         
  
              <!-- Toolbar on the Right -->
              <div>
    <v-toolbar
      dark
      prominent
      image="/img/CHR-PNP-DEPED_banner-1896x800.86106138.png"
      class="rounded-lg"
    >
      <v-app-bar-nav-icon></v-app-bar-nav-icon>

      <v-toolbar-title>Vuetify</v-toolbar-title>

      <v-spacer></v-spacer>

      <v-btn icon>
        <v-icon>mdi-export</v-icon>
      </v-btn>
    </v-toolbar>
  </div>
  
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
  