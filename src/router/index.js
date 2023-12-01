import { createRouter, createWebHistory } from "vue-router";
import store from "@/store";
import Solo from "../layouts/Solo";
import Main from "../layouts/Main";
import StudNav from "../layouts/StudNav";
import HomeView from "../views/HomeView.vue";

const routes = [
  {
    path: "/",
    name: "home",
    component: HomeView,
  },
  {
    path: "/login",
    name: "login",
    component: HomeView,
  },
  {
    path: "/auth/verify/:verificationCode",
    name: "verify-account",
    component: HomeView,
  },
  {
    path: "/admin",
    component: Main,

    children: [
      {
        path: "dashboard",
        name: "caDashboard",
        component: () => import("../views/Admin/centralAdmin/caDashboard.vue"),
      },
      {
        path: "applicants",
        name: "caManageApplicants",
        component: () =>
          import("../views/Admin/centralAdmin/caManageApplicants.vue"),
      },
      {
        path: "accounts",
        name: "caManageAccounts",
        component: () =>
          import("../views/Admin/centralAdmin/caManageAccounts.vue"),
      },
      {
        path: "stations",
        name: "caManageStation",
        component: () =>
          import("../views/Admin/centralAdmin/caManageStation.vue"),
      },
      {
        path: "courses",
        name: "caManageCourses",
        component: () =>
          import("../views/Admin/centralAdmin/caManageCourse.vue"),
      },
    ],
  },
  {
    path: "/application",
    name: "application",
    component: Solo,
    children: [
      {
        path: "",
        component: () => import("../views/Registration/ApplicationForm.vue"),
      },
    ],
  },
  {
    path: "/student",
    component: StudNav,
    children: [
      {
        path: "home",
        name: "studentHome",
        component: () => import("../views/Student/Home.vue"),
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

// Navigation guard
router.beforeEach((to, from, next) => {
  const requiredRoles = to.meta.requiredRoles;

  // Check if the route requires roles
  if (requiredRoles) {
    // Get user information from the Vuex store
    const isLoggedIn = store.getters.isLoggedIn;
    const userRoles = store.getters.userRole;

    console.log("isLoggedIn:", isLoggedIn);
    console.log("userRoles:", userRoles);
    console.log("requiredRoles:", requiredRoles);
    console.log(
      "hasRequiredRoles:",
      hasRequiredRoles(userRoles, requiredRoles)
    );

    // Check if the user is logged in
    if (!isLoggedIn) {
      // Redirect to the login page or handle the scenario where the user is not logged in
      next("/login");
      return;
    }

    // Check if userRoles is an array and has the required roles
    if (
      Array.isArray(userRoles) &&
      hasRequiredRoles(userRoles, requiredRoles)
    ) {
      // Continue to the next route
      next();
      return;
    } else {
      // Redirect to unauthorized page or show an error message
      next("/unauthorized");
      return;
    }
  }

  // Continue to the next route
  next();
});

function hasRequiredRoles(userRoles, requiredRoles) {
  // Ensure userRoles is either an array or a single value
  const rolesArray = Array.isArray(userRoles) ? userRoles : [userRoles];

  // Convert requiredRoles to a plain array (not necessary if it's already an array)
  const requiredRolesArray = Array.from(requiredRoles);

  // Check if there is any intersection between rolesArray and requiredRolesArray using strict equality
  return rolesArray.some((role) => requiredRolesArray.includes(role));
}

export default router;
