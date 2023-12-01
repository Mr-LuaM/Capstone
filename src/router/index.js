import { createRouter, createWebHistory } from "vue-router";
import { jwtDecode as jwt_decode } from "jwt-decode";
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
    path: "/edit-accounts",
    name: "edit-accounts",
    component: Main,
    children: [
      {
        path: "",
        component: () => import("../views/EditAccount.vue"),
      },
    ],
  },
  {
    path: "/admin",
    component: Main,
    meta: { requiredRoles: ["1"] },
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
    const userRoles = getUserRoles();

    // Check if the user is logged in
    if (!userRoles.length) {
      // Redirect to the login page or handle the scenario where the user is not logged in
      next("/login");
      return;
    }

    // Check if the user has the required roles
    if (!hasRequiredRoles(userRoles, requiredRoles)) {
      // Redirect to unauthorized page or show an error message
      next("/unauthorized");
      return;
    }
  }

  // Continue to the next route
  next();
});

function getUserRoles() {
  const token = localStorage.getItem("jwt_token");

  if (!token) {
    console.error("JWT token not found.");
    return [];
  }

  try {
    const decodedToken = jwt_decode(token);

    // Ensure that userRoles is an array
    return Array.isArray(decodedToken.role)
      ? decodedToken.role
      : [decodedToken.role];
  } catch (error) {
    console.error("Error decoding token:", error);
    return [];
  }
}

function hasRequiredRoles(userRoles, requiredRoles) {
  // Check if the user has at least one of the required roles
  return requiredRoles.some((role) => userRoles.includes(role));
}

export default router;
