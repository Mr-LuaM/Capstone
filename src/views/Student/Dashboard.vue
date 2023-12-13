<template>
  <v-container fluid>
    <v-row>
      <v-col cols="4">
        <v-container
          fluid
          class="rounded-lg bg-image bg-color"
          style="position: relative"
        >
          <v-row align="center" no-gutters>
            <v-col cols="12" md="5">
              <v-avatar color="grey" size="150" class="rounded-lg">
                <v-img :src="getFullImageUrl()" alt="User Avatar" cover></v-img>
              </v-avatar>
            </v-col>
            <v-col cols="12" md="7" class="text-black">
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-subtitle>Welcome</v-list-item-subtitle>
                  <v-list-item-title class="text-h5 mb-1">{{
                    user.fullName
                  }}</v-list-item-title>
                  <v-list-item-subtitle>{{
                    getRoleName()
                  }}</v-list-item-subtitle>
                  <p class="text-caption mt-1">{{ user.email }}</p>
                </v-list-item-content>
              </v-list-item>
            </v-col>
          </v-row>
        </v-container>
      </v-col>
      <v-col>
        <ProgressBar />
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="8" class="ma-1 pa-2"> <SchoolCalendar /></v-col>

      <v-col class="ma-1 pa-2">
        <v-card class="mx-auto" max-width="450">
          <v-toolbar color="secondary-darken-1">
            <v-btn variant="text" icon="mdi-menu"></v-btn>

            <v-toolbar-title>Inbox</v-toolbar-title>

            <v-spacer></v-spacer>

            <v-btn variant="text" icon="mdi-magnify"></v-btn>
          </v-toolbar>

          <v-list :items="items" item-props lines="three">
            <template v-slot:subtitle="{ subtitle }">
              <div v-html="subtitle"></div>
            </template>
          </v-list> </v-card
      ></v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapGetters } from "vuex";
import UserStatistics from "../../components/dashboard/UserStatistics";
import StationAdminStatistics from "../../components/dashboard/StationAdminStatistics";
import TeacherStatistics from "../../components/dashboard/TeacherStatistics";
import StudentStatistics from "../../components/dashboard/StudentStatisctics";
import LineChart from "../../components/dashboard/linechart";
import StackChart from "../../components/dashboard/stackchart";
import SchoolCalendar from "@/components/scheduler/SchoolCalendar.vue";
import ProgressBar from "@/components/dashboard/progressbar.vue";

export default {
  components: {
    UserStatistics,
    StationAdminStatistics,
    TeacherStatistics,
    StudentStatistics,
    LineChart,
    StackChart,
    SchoolCalendar,
    ProgressBar,
  },
  name: "AvatarMenu",
  data() {
    return {
      backendUrl: "http://backend.test/", // Define your backend URL here
    };
  },
  computed: {
    ...mapGetters(["userName", "userEmail", "userProfile", "role"]),
    user() {
      const userName = this.userName || "";

      const initials = userName
        .split(" ")
        .map((word) => word[0])
        .join("")
        .toUpperCase();

      return {
        initials,
        fullName: userName,
        email: this.userEmail,
        profilePicture: this.getFullImageUrl(),
      };
    },
  },
  methods: {
    getFullImageUrl() {
      return this.backendUrl + this.userProfile;
    },
    logout() {
      // Perform logout actions here
      // For example, clear the token from localStorage
      localStorage.removeItem("jwt_token");

      // Clear user information from Vuex store
      this.$store.dispatch("logout");

      // Redirect to the login page and clear navigation history
      this.$router.push({ path: "/login" }).catch(() => {});
    },
    getParentRoute() {
      // Access the current route
      const currentRoute = this.$route;

      // Extract the parent route by removing the last segment
      const parentRoute = currentRoute.path.split("/").slice(0, -1).join("/");

      return parentRoute;
    },
    getRoleName() {
      switch (this.role) {
        case "2":
          return "Main Admin";
        case "3":
          return "Station Admin";
        case "4":
          return "Teacher";
        case "5":
          return "Student";
        default:
          return "Unknown Role";
      }
    },
  },
};
</script>
<style scoped>
.bg-image {
  background: url("../../assets/img/examples/f2wuB.png") no-repeat right bottom;
  background-size: contain; /* Adjust the background-size property as needed */
  min-height: 150px; /* Set the minimum height to match the height of the avatar */
  position: relative;
}
.bg-color {
  background-color: skyblue;
}
</style>
