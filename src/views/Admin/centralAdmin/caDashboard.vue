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
            <v-col cols="12" md="7" class="text-white">
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
        <v-row>
          <v-col class="ma-1 pa-2"> <UserStatistics /></v-col>

          <v-col class="ma-1 pa-2"> <StationAdminStatistics /></v-col>

          <v-col class="ma-1 pa-2"> <TeacherStatistics /></v-col>

          <v-col class="ma-1 pa-2"> <StudentStatistics /></v-col>
        </v-row>
        <v-divider :thickness="6" class="mt-5" color="success"></v-divider>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="4" class="pa-2"> <LineChart /></v-col>
      <v-col cols="4" class="pa-2"><StackChart /></v-col>
      <v-col cols="4" class="pa-2"><PieChart /></v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapGetters } from "vuex";
import UserStatistics from "../../../components/dashboard/UserStatistics";
import StationAdminStatistics from "../../../components/dashboard/StationAdminStatistics";
import TeacherStatistics from "../../../components/dashboard/TeacherStatistics";
import StudentStatistics from "../../../components/dashboard/StudentStatisctics";
import LineChart from "../../../components/dashboard/linechart";
import StackChart from "../../../components/dashboard/stackchart";
import PieChart from "../../../components/dashboard/pie.vue";

export default {
  components: {
    UserStatistics,
    StationAdminStatistics,
    TeacherStatistics,
    StudentStatistics,
    LineChart,
    StackChart,

    PieChart,
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
  background: url("../../../assets/img/examples/f2wuB.png") no-repeat right
    bottom;
  background-size: contain; /* Adjust the background-size property as needed */
  min-height: 150px; /* Set the minimum height to match the height of the avatar */
  position: relative;
}
.bg-color {
  background-color: #003366;
}
</style>
