<template>
    <v-app>
      <!-- Admin Page Header -->
      <v-app-bar app color="secondary-darken-1" class="rounded-b-m">
        <v-app-bar-nav-icon @click="toggleSidebar"></v-app-bar-nav-icon>
        <v-toolbar-title class="ml-2">Student</v-toolbar-title>
        <v-spacer></v-spacer>
  
        <ThemeSwitcher />
      </v-app-bar>
  
      <!-- Admin Page Content -->
      <v-row>
        <!-- Admin Page Sidebar -->
  
        <v-navigation-drawer app v-model="sidebarOpen" class="elevation-12 rounded-e">
          <v-list density="comfortable" nav>
            <v-list-item
              v-for="(item, index) in menuItems"
              :key="index"
              :link="true"
              :to="item.route"
              :class="{ highlight: isRouteActive(item.route) }"
              :prepend-icon="item.icon"
            >
              <v-list-item-title>{{ item.title }}</v-list-item-title>
            </v-list-item>
          </v-list>
          <template v-slot:append>
            <div class="pa-2">
              <v-btn @click="logout" block>Logout</v-btn>
            </div>
          </template>
        </v-navigation-drawer>
  
        <!-- Admin Page Main Content -->
        <v-main>
          <v-container fluid class="pa-7">
            <v-breadcrumbs :items="breadcrumbs">
              <template v-slot:prepend>
                <v-icon size="small" icon="$vuetify"></v-icon>
              </template>
            </v-breadcrumbs>
            <router-view></router-view>
          </v-container>
        </v-main>
      </v-row>
    </v-app>
  </template>
  
  <script>
  export default {
    data() {
      return {
        sidebarOpen: true, // Initially open the sidebar
        menuItems: [
          { title: "Dashboard", route: "/student/home", icon: "mdi-view-dashboard" },
          { title: "Applicants", route: "/student/applicants", icon: "mdi-account-group" },
          { title: "Stations", route: "/student/stations", icon: "mdi-map-marker" },
          // Add more menu items for different sections
        ],
        breadcrumbs: [],
      };
    },
    created() {
      this.updateBreadcrumbs();
    },
    watch: {
      $route() {
        this.updateBreadcrumbs();
      },
    },
    methods: {
        updateBreadcrumbs() {
  const routeSegments = this.$route.path.split('/').filter(segment => segment !== '');

  this.breadcrumbs = routeSegments.map(segment => segment.charAt(0).toUpperCase() + segment.slice(1));

  console.log('Breadcrumbs:', this.breadcrumbs);
},
    
      toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
      },
      logout() {
        // Perform logout actions here
        // For example, clear the token from localStorage
        localStorage.removeItem("jwt_token");
  
        // Redirect to the login page and clear navigation history
        this.$router.push({ path: "/login" }).catch(() => {});
      },
      isRouteActive(route) {
        return this.$route.path === route;
      },
    },
  };
  </script>
  
  <style scoped>
  /* Add your styles if needed */
  </style>
  